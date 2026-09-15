<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Currency;
use App\Models\FiscalYear;
use App\Models\Item;
use App\Models\Person;
use App\Models\SalesInvoice;
use App\Models\StockMove;
use App\Models\Warehouse;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesInvoiceController extends Controller
{
    public function index()
    {
        $invoices = SalesInvoice::with(['customer', 'currency'])->orderByDesc('date')->orderByDesc('id')->get();

        return view('sales-invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('sales-invoices.form', [
            'customers' => Person::customers()->orderBy('name')->get(),
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
        ]);

        $fiscalYear = FiscalYear::current();
        abort_unless($fiscalYear, 422, 'سال مالی جاری تعریف نشده است.');

        $arAccount = Account::where('code', '1200')->firstOrFail();
        $revenueAccount = Account::where('code', '4100')->firstOrFail();
        $cogsAccount = Account::where('code', '5100')->firstOrFail();
        $inventoryAccount = Account::where('code', '1300')->firstOrFail();

        $invoice = DB::transaction(function () use ($data, $fiscalYear, $arAccount, $revenueAccount, $cogsAccount, $inventoryAccount, $ledger) {
            $number = 'INV-'.now()->format('Ymd').'-'.str_pad((string) (SalesInvoice::count() + 1), 4, '0', STR_PAD_LEFT);

            $totalAmount = 0;
            $totalCost = 0;
            $lineData = [];

            foreach ($data['lines'] as $line) {
                $item = Item::findOrFail($line['item_id']);
                $lineTotal = ($line['quantity'] * $line['unit_price']) - ($line['discount'] ?? 0);
                $totalAmount += $lineTotal;
                $totalCost += $item->cost_price * $line['quantity'];

                $lineData[] = [
                    'item_id' => $item->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'discount' => $line['discount'] ?? 0,
                    'total' => $lineTotal,
                ];
            }

            $invoice = SalesInvoice::create([
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
                    'type' => 'out',
                    'quantity' => $line['quantity'],
                    'unit_cost' => Item::find($line['item_id'])->cost_price,
                    'reference_type' => 'sales_invoice',
                    'reference_id' => $invoice->id,
                ]);
            }

            $entry = $ledger->postEntry([
                'fiscal_year_id' => $fiscalYear->id,
                'date' => $data['date'],
                'reference_type' => 'sales_invoice',
                'reference_id' => $invoice->id,
                'description' => "فاکتور فروش {$number}",
                'currency_id' => $data['currency_id'],
                'fx_rate' => $data['fx_rate'],
                'lines' => [
                    ['account_id' => $arAccount->id, 'person_id' => $data['person_id'], 'debit' => $totalAmount, 'credit' => 0],
                    ['account_id' => $revenueAccount->id, 'debit' => 0, 'credit' => $totalAmount],
                    // COGS is always posted in base currency (fx_rate 1) since it's derived from item cost, not the invoice currency.
                ],
            ]);

            if ($totalCost > 0) {
                $ledger->postEntry([
                    'fiscal_year_id' => $fiscalYear->id,
                    'date' => $data['date'],
                    'reference_type' => 'sales_invoice',
                    'reference_id' => $invoice->id,
                    'description' => "بهای تمام شده فروش {$number}",
                    'currency_id' => Currency::where('is_base', true)->value('id'),
                    'fx_rate' => 1,
                    'lines' => [
                        ['account_id' => $cogsAccount->id, 'debit' => $totalCost, 'credit' => 0],
                        ['account_id' => $inventoryAccount->id, 'debit' => 0, 'credit' => $totalCost],
                    ],
                ]);
            }

            $invoice->update(['journal_entry_id' => $entry->id]);

            return $invoice;
        });

        return redirect()->route('sales-invoices.show', $invoice)->with('success', "فاکتور فروش {$invoice->number} ثبت شد.");
    }

    public function show(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load(['customer', 'warehouse', 'currency', 'lines.item']);

        return view('sales-invoices.show', ['invoice' => $salesInvoice]);
    }

    public function destroy(SalesInvoice $salesInvoice)
    {
        DB::transaction(function () use ($salesInvoice) {
            \App\Models\JournalEntry::where('reference_type', 'sales_invoice')->where('reference_id', $salesInvoice->id)->each(fn ($e) => $e->delete());
            StockMove::where('reference_type', 'sales_invoice')->where('reference_id', $salesInvoice->id)->delete();
            $salesInvoice->lines()->delete();
            $salesInvoice->delete();
        });

        return redirect()->route('sales-invoices.index')->with('success', 'فاکتور حذف شد.');
    }
}
