<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemTransfer;
use App\Models\StockMove;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemTransferController extends Controller
{
    public function index()
    {
        $transfers = ItemTransfer::with(['fromWarehouse', 'toWarehouse'])
            ->orderByDesc('date')->orderByDesc('id')->get();

        return view('item-transfers.index', compact('transfers'));
    }

    public function create()
    {
        return view('item-transfers.form', [
            'warehouses' => Warehouse::orderBy('name')->get(),
            'items' => Item::with('unit')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'from_warehouse_id' => 'required|exists:warehouses,id|different:to_warehouse_id',
            'to_warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|numeric|min:0.0001',
        ]);

        $transfer = DB::transaction(function () use ($data) {
            $number = 'IT-'.now()->format('Ymd').'-'.str_pad((string) (ItemTransfer::count() + 1), 4, '0', STR_PAD_LEFT);

            $transfer = ItemTransfer::create([
                'number' => $number,
                'date' => $data['date'],
                'from_warehouse_id' => $data['from_warehouse_id'],
                'to_warehouse_id' => $data['to_warehouse_id'],
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['lines'] as $line) {
                $transfer->lines()->create([
                    'item_id' => $line['item_id'],
                    'quantity' => $line['quantity'],
                ]);

                $costPrice = Item::find($line['item_id'])->cost_price;

                StockMove::create([
                    'item_id' => $line['item_id'],
                    'warehouse_id' => $data['from_warehouse_id'],
                    'date' => $data['date'],
                    'type' => 'out',
                    'quantity' => $line['quantity'],
                    'unit_cost' => $costPrice,
                    'reference_type' => 'item_transfer',
                    'reference_id' => $transfer->id,
                ]);

                StockMove::create([
                    'item_id' => $line['item_id'],
                    'warehouse_id' => $data['to_warehouse_id'],
                    'date' => $data['date'],
                    'type' => 'in',
                    'quantity' => $line['quantity'],
                    'unit_cost' => $costPrice,
                    'reference_type' => 'item_transfer',
                    'reference_id' => $transfer->id,
                ]);
            }

            return $transfer;
        });

        return redirect()->route('item-transfers.index')->with('success', "انتقال اجناس {$transfer->number} ثبت شد.");
    }

    public function destroy(ItemTransfer $itemTransfer)
    {
        DB::transaction(function () use ($itemTransfer) {
            StockMove::where('reference_type', 'item_transfer')->where('reference_id', $itemTransfer->id)->delete();
            $itemTransfer->lines()->delete();
            $itemTransfer->delete();
        });

        return redirect()->route('item-transfers.index')->with('success', 'انتقال حذف شد.');
    }
}
