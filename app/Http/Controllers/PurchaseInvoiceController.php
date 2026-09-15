<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Currency;
use App\Models\FiscalYear;
use App\Models\Item;
use App\Models\Person;
use App\Models\PurchaseInvoice;
use App\Models\StockMove;
use App\Models\Warehouse;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    public function index()
    {
        $invoices = PurchaseInvoice::with(['supplier', 'currency'])->orderByDesc('date')->orderByDesc('id')->get();

        return view('purchase-invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('purchase-invoices.form', [
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
            'lines.*.discount' => 'nullable|numeric|min:0',
            'update_cost' => 'boolean',
        ]);

        $fiscalYear = FiscalYear::current();
        abort_unless($fiscalYear, 422, 'سال مالی جاری تعریف نشده است.');

        $apAccount = Account::where('code', '2100')->firstOrFail();
        $inventoryAccount = Account::where('code', '1300')->firstOrFail();

        $invoice = DB::transaction(function () use ($data, $fiscalYear, $apAccount, $inventoryAccount, $ledger) {
            $number = 'PINV-'.now()->format('Ymd').'-'.str_pad((string) (PurchaseInvoice::count() + 1), 4, '0', STR_PAD_LEFT);

            $totalAmount = 0;
            $lineData = [];

            foreach ($data['lines'] as $line) {
                $lineTotal = ($line['quantity'] * $line['unit_price']) - ($line['discount'] ?? 0);
                $totalAmount += $lineTotal;

                $lineData[] = [
                    'item_id' => $line['item_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'discount' => $line['discount'] ?? 0,
                    'total' => $lineTotal,
                ];
            }

            $invoice = PurchaseInvoice::create([
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
                $invoice->lines()->create($line);

                StockMove::create([
                    'item_id' => $line['item_id'],
                    'warehouse_id' => $data['warehouse_id'],
                    'date' => $data['date'],
                    'type' => 'in',
                    'quantity' => $line['quantity'],
                    'unit_cost' => $line['unit_price'],
                    'reference_type' => 'purchase_invoice',
                    'reference_id' => $invoice->id,
                ]);

                if (! empty($data['update_cost'])) {
                    Item::where('id', $line['item_id'])->update(['cost_price' => $line['unit_price']]);
                }
            }

            $entry = $ledger->postEntry([
                'fiscal_year_id' => $fiscalYear->id,
                'date' => $data['date'],
                'reference_type' => 'purchase_invoice',
                'reference_id' => $invoice->id,
                'description' => "فاکتور خرید {$number}",
                'currency_id' => $data['currency_id'],
                'fx_rate' => $data['fx_rate'],
                'lines' => [
                    ['account_id' => $inventoryAccount->id, 'debit' => $totalAmount, 'credit' => 0],
                    ['account_id' => $apAccount->id, 'person_id' => $data['person_id'], 'debit' => 0, 'credit' => $totalAmount],
                ],
            ]);

            $invoice->update(['journal_entry_id' => $entry->id]);

            return $invoice;
        });

        return redirect()->route('purchase-invoices.show', $invoice)->with('success', "فاکتور خرید {$invoice->number} ثبت شد.");
    }

    public function show(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load(['supplier', 'warehouse', 'currency', 'lines.item']);

        return view('purchase-invoices.show', ['invoice' => $purchaseInvoice]);
    }

    public function destroy(PurchaseInvoice $purchaseInvoice)
    {
        DB::transaction(function () use ($purchaseInvoice) {
            \App\Models\JournalEntry::where('reference_type', 'purchase_invoice')->where('reference_id', $purchaseInvoice->id)->each(fn ($e) => $e->delete());
            StockMove::where('reference_type', 'purchase_invoice')->where('reference_id', $purchaseInvoice->id)->delete();
            $purchaseInvoice->lines()->delete();
            $purchaseInvoice->delete();
        });

        return redirect()->route('purchase-invoices.index')->with('success', 'فاکتور حذف شد.');
    }
}
