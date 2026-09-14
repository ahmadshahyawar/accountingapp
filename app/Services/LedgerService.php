<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * The single door every module (sales, purchases, cash vouchers, opening balances)
 * posts through. Centralizing this is what keeps debits == credits guaranteed
 * regardless of which screen created the entry — see plan's Data model section.
 */
class LedgerService
{
    /**
     * @param  array{fiscal_year_id:int,date:string,reference_type:string,reference_id:?int,description:?string,currency_id:int,fx_rate?:float,lines:array<int,array{account_id:int,person_id?:?int,description?:?string,debit?:float,credit?:float}>}  $data
     */
    public function postEntry(array $data): JournalEntry
    {
        $lines = $data['lines'] ?? [];
        $fxRate = (float) ($data['fx_rate'] ?? 1);

        if (count($lines) < 2) {
            throw new InvalidArgumentException('A journal entry needs at least two lines.');
        }

        $totalDebit = round(array_sum(array_column($lines, 'debit')), 4);
        $totalCredit = round(array_sum(array_column($lines, 'credit')), 4);

        if (abs($totalDebit - $totalCredit) > 0.0005) {
            throw new InvalidArgumentException(
                "Journal entry does not balance: debit {$totalDebit} != credit {$totalCredit}."
            );
        }

        return DB::transaction(function () use ($data, $lines, $fxRate) {
            $entry = JournalEntry::create([
                'fiscal_year_id' => $data['fiscal_year_id'],
                'date' => $data['date'],
                'reference_type' => $data['reference_type'],
                'reference_id' => $data['reference_id'] ?? null,
                'description' => $data['description'] ?? null,
                'currency_id' => $data['currency_id'],
            ]);

            foreach ($lines as $line) {
                $debit = (float) ($line['debit'] ?? 0);
                $credit = (float) ($line['credit'] ?? 0);

                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'person_id' => $line['person_id'] ?? null,
                    'description' => $line['description'] ?? null,
                    'debit' => $debit,
                    'credit' => $credit,
                    'base_debit' => round($debit * $fxRate, 4),
                    'base_credit' => round($credit * $fxRate, 4),
                ]);
            }

            return $entry->load('lines');
        });
    }

    public function reverseEntry(JournalEntry $entry, ?string $onDate = null): JournalEntry
    {
        return $this->postEntry([
            'fiscal_year_id' => $entry->fiscal_year_id,
            'date' => $onDate ?? now()->toDateString(),
            'reference_type' => $entry->reference_type,
            'reference_id' => $entry->reference_id,
            'description' => 'Reversal of #'.$entry->id.($entry->description ? " — {$entry->description}" : ''),
            'currency_id' => $entry->currency_id,
            'lines' => $entry->lines->map(fn ($line) => [
                'account_id' => $line->account_id,
                'person_id' => $line->person_id,
                'description' => $line->description,
                'debit' => $line->credit,
                'credit' => $line->debit,
            ])->all(),
        ]);
    }
}
