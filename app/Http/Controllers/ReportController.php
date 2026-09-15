<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\Person;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function trialBalance()
    {
        $accounts = Account::where('is_group', false)->orderBy('code')->get();

        $rows = $accounts->map(function (Account $account) {
            $debit = (float) $account->journalLines()->sum('base_debit');
            $credit = (float) $account->journalLines()->sum('base_credit');

            return [
                'account' => $account,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $account->normal_balance === 'debit' ? $debit - $credit : $credit - $debit,
            ];
        })->filter(fn ($row) => $row['debit'] != 0 || $row['credit'] != 0)->values();

        return view('reports.trial-balance', [
            'rows' => $rows,
            'totalDebit' => $rows->sum('debit'),
            'totalCredit' => $rows->sum('credit'),
        ]);
    }

    public function accountStatement(Request $request)
    {
        $accounts = Account::where('is_group', false)->orderBy('code')->get();
        $accountId = $request->get('account_id');
        $from = $request->get('from');
        $to = $request->get('to');

        $lines = collect();
        $account = null;
        $openingBalance = 0;

        if ($accountId) {
            $account = Account::findOrFail($accountId);

            // No "from" means the statement starts at the beginning of history, so there is
            // nothing to carry forward — only compute a prior-period balance when one is asked for.
            $openingBalance = $from
                ? $account->journalLines()
                    ->whereHas('journalEntry', fn ($q) => $q->where('date', '<', $from))
                    ->get()
                    ->reduce(function ($carry, $line) use ($account) {
                        $delta = $account->normal_balance === 'debit' ? $line->base_debit - $line->base_credit : $line->base_credit - $line->base_debit;

                        return $carry + $delta;
                    }, 0)
                : 0;

            $lines = $account->journalLines()
                ->with(['journalEntry', 'person'])
                ->whereHas('journalEntry', function ($q) use ($from, $to) {
                    $q->when($from, fn ($q2) => $q2->where('date', '>=', $from))
                        ->when($to, fn ($q2) => $q2->where('date', '<=', $to));
                })
                ->get()
                ->sortBy(fn ($line) => $line->journalEntry->date)
                ->values();
        }

        return view('reports.account-statement', compact('accounts', 'account', 'lines', 'from', 'to', 'openingBalance', 'accountId'));
    }

    public function dayBook(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $entries = JournalEntry::with(['lines.account', 'lines.person'])
            ->whereBetween('date', [$from, $to])
            ->orderBy('date')->orderBy('id')
            ->get();

        return view('reports.day-book', compact('entries', 'from', 'to'));
    }

    public function debtors()
    {
        $arAccount = Account::where('code', '1200')->first();
        $rows = $this->personBalances($arAccount, 'debit');

        return view('reports.debtors', ['rows' => $rows]);
    }

    public function creditors()
    {
        $apAccount = Account::where('code', '2100')->first();
        $rows = $this->personBalances($apAccount, 'credit');

        return view('reports.creditors', ['rows' => $rows]);
    }

    public function salesGraph()
    {
        $monthly = SalesInvoice::selectRaw("strftime('%Y-%m', date) as month, SUM(total_amount) as total")
            ->groupBy('month')->orderBy('month')->get();

        return view('reports.sales-graph', compact('monthly'));
    }

    private function personBalances(?Account $account, string $side)
    {
        if (! $account) {
            return collect();
        }

        return JournalLine::where('account_id', $account->id)
            ->whereNotNull('person_id')
            ->with('person')
            ->get()
            ->groupBy('person_id')
            ->map(function ($lines) use ($side) {
                $debit = $lines->sum('base_debit');
                $credit = $lines->sum('base_credit');

                return [
                    'person' => $lines->first()->person,
                    'balance' => $side === 'debit' ? $debit - $credit : $credit - $debit,
                ];
            })
            ->filter(fn ($row) => abs($row['balance']) > 0.005)
            ->sortByDesc('balance')
            ->values();
    }
}
