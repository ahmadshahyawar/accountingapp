<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['unit', 'warehouse'])->orderBy('name')->get();

        return view('items.index', compact('items'));
    }

    public function create()
    {
        return $this->form(new Item);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['photo_path'] = $this->storePhoto($request);

        Item::create($data);

        return redirect()->route('items.index')->with('success', 'جنس جدید ثبت شد.');
    }

    public function edit(Item $item)
    {
        return $this->form($item);
    }

    public function update(Request $request, Item $item)
    {
        $data = $this->validated($request, $item->id);

        if ($request->boolean('remove_photo')) {
            if ($item->photo_path) {
                Storage::disk('public')->delete($item->photo_path);
            }
            $data['photo_path'] = null;
        } elseif ($newPhoto = $this->storePhoto($request)) {
            if ($item->photo_path) {
                Storage::disk('public')->delete($item->photo_path);
            }
            $data['photo_path'] = $newPhoto;
        }

        $item->update($data);

        return redirect()->route('items.index')->with('success', 'معلومات جنس بروزرسانی شد.');
    }

    /** Stores the uploaded عکس (photo), matching the old app's تعریف جنس form. */
    private function storePhoto(Request $request): ?string
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        return $request->file('photo')->store('items', 'public');
    }

    public function destroy(Item $item)
    {
        if ($item->stockMoves()->exists()) {
            return back()->with('error', 'این جنس دارای گردش انبار است و قابل حذف نیست.');
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'حذف شد.');
    }

    private function form(Item $item)
    {
        return view('items.form', [
            'item' => $item,
            'units' => Unit::orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
        ]);
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => 'required|string|max:100|unique:items,code'.($ignoreId ? ",{$ignoreId}" : ''),
            'name' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:4096',
        ]);
    }
}
