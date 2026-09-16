<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Item;
use App\Models\ProformaInvoice;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProformaInvoiceController extends Controller
{
    public function index()
    {
        $invoices = ProformaInvoice::orderByDesc('date')->orderByDesc('id')->get();

        return view('proforma-invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('proforma-invoices.form', [
            'warehouses' => Warehouse::orderBy('name')->get(),
            'items' => Item::with('unit')->orderBy('name')->get(),
            'currencies' => Currency::orderBy('code')->get(),
            'nextNumber' => 'PF-'.now()->format('Ymd').'-'.str_pad((string) (ProformaInvoice::count() + 1), 4, '0', STR_PAD_LEFT),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_mobile' => 'nullable|string|max:50',
            'warehouse_id' => 'required|exists:warehouses,id',
            'currency_id' => 'required|exists:currencies,id',
            'fx_rate' => 'required|numeric|min:0.000001',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|numeric|min:0.0001',
            'lines.*.unit_price' => 'required|numeric|min:0',
        ]);

        $invoice = DB::transaction(function () use ($data) {
            $number = 'PF-'.now()->format('Ymd').'-'.str_pad((string) (ProformaInvoice::count() + 1), 4, '0', STR_PAD_LEFT);

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
            $totalAmount -= $data['discount'] ?? 0;

            $invoice = ProformaInvoice::create([
                'number' => $number,
                'date' => $data['date'],
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_mobile' => $data['customer_mobile'] ?? null,
                'warehouse_id' => $data['warehouse_id'],
                'currency_id' => $data['currency_id'],
                'fx_rate' => $data['fx_rate'],
                'discount' => $data['discount'] ?? 0,
                'total_amount' => $totalAmount,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lineData as $line) {
                $invoice->lines()->create($line);
            }

            return $invoice;
        });

        return redirect()->route('proforma-invoices.index')->with('success', "پیش فاکتور {$invoice->number} ثبت شد.");
    }

    public function destroy(ProformaInvoice $proformaInvoice)
    {
        $proformaInvoice->lines()->delete();
        $proformaInvoice->delete();

        return redirect()->route('proforma-invoices.index')->with('success', 'پیش فاکتور حذف شد.');
    }
}
