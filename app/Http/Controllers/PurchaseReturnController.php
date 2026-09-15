<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Currency;
use App\Models\FiscalYear;
use App\Models\Item;
use App\Models\Person;
use App\Models\PurchaseReturn;
use App\Models\StockMove;
use App\Models\Warehouse;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function index()
    {
        $returns = PurchaseReturn::with(['supplier', 'currency'])->orderByDesc('date')->orderByDesc('id')->get();

        return view('purchase-returns.index', compact('returns'));
    }

    public function create()
    {
        return view('purchase-returns.form', [
            'suppliers' => Person::suppliers()->orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
            'items' => Item::with('unit')->orderBy('name')->get(),
            'currencies' => Currency::orderBy('code')->get(),
        ]);
    }

    public function store(Request $request, LedgerService $ledger)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'person_id' => 'required|exists:persons,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'currency_id' => 'required|exists:currencies,id',
            'fx_rate' => 'required|numeric|min:0.000001',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|numeric|min:0.0001',
            'lines.*.unit_price' => 'required|numeric|min:0',
        ]);

        $fiscalYear = FiscalYear::current();
        abort_unless($fiscalYear, 422, 'سال مالی جاری تعریف نشده است.');

        $apAccount = Account::where('code', '2100')->firstOrFail();
        $inventoryAccount = Account::where('code', '1300')->firstOrFail();

        $return = DB::transaction(function () use ($data, $fiscalYear, $apAccount, $inventoryAccount, $ledger) {
            $number = 'PRET-'.now()->format('Ymd').'-'.str_pad((string) (PurchaseReturn::count() + 1), 4, '0', STR_PAD_LEFT);

            $totalAmount = 0;
            $lineData = [];
            foreach ($data['lines'] as $line) {
                $lineTotal = $line['quantity'] * $line['unit_price'];
                $totalAmount += $lineTotal;
                $lineData[] = [
                    'item_id' => $line['item_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'total' => $lineTotal,
                ];
            }

            $return = PurchaseReturn::create([
                'number' => $number,
                'date' => $data['date'],
                'person_id' => $data['person_id'],
                'warehouse_id' => $data['warehouse_id'],
                'currency_id' => $data['currency_id'],
                'fx_rate' => $data['fx_rate'],
                'total_amount' => $totalAmount,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lineData as $line) {
                $return->lines()->create($line);

                StockMove::create([
                    'item_id' => $line['item_id'],
                    'warehouse_id' => $data['warehouse_id'],
                    'date' => $data['date'],
                    'type' => 'out', // goods leave our stock, going back to the supplier
                    'quantity' => $line['quantity'],
                    'unit_cost' => Item::find($line['item_id'])->cost_price,
                    'reference_type' => 'purchase_return',
                    'reference_id' => $return->id,
                ]);
            }

            // Reverse of the original purchase: reduce inventory and what we owe the supplier.
            $entry = $ledger->postEntry([
                'fiscal_year_id' => $fiscalYear->id,
                'date' => $data['date'],
                'reference_type' => 'purchase_return',
                'reference_id' => $return->id,
                'description' => "برگشت از خرید {$number}",
                'currency_id' => $data['currency_id'],
                'fx_rate' => $data['fx_rate'],
                'lines' => [
                    ['account_id' => $apAccount->id, 'person_id' => $data['person_id'], 'debit' => $totalAmount, 'credit' => 0],
                    ['account_id' => $inventoryAccount->id, 'debit' => 0, 'credit' => $totalAmount],
                ],
            ]);

            $return->update(['journal_entry_id' => $entry->id]);

            return $return;
        });

        return redirect()->route('purchase-returns.index')->with('success', "برگشت از خرید {$return->number} ثبت شد.");
    }

    public function destroy(PurchaseReturn $purchaseReturn)
    {
        DB::transaction(function () use ($purchaseReturn) {
            \App\Models\JournalEntry::where('reference_type', 'purchase_return')->where('reference_id', $purchaseReturn->id)->each(fn ($e) => $e->delete());
            StockMove::where('reference_type', 'purchase_return')->where('reference_id', $purchaseReturn->id)->delete();
            $purchaseReturn->lines()->delete();
            $purchaseReturn->delete();
        });

        return redirect()->route('purchase-returns.index')->with('success', 'برگشت حذف شد.');
    }
}
