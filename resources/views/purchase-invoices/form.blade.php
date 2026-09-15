<x-layouts.app title="فاکتور خرید">
    <x-ui.page-header title="فاکتور خرید جدید" :back-route="route('purchase-invoices.index')" />

    <form action="{{ route('purchase-invoices.store') }}" method="POST"
        x-data="{
            items: @js($items->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'price' => (float) $i->cost_price, 'unit' => $i->unit->name])),
            lines: [{ item_id: '', quantity: 1, unit_price: 0, discount: 0 }],
            addLine() { this.lines.push({ item_id: '', quantity: 1, unit_price: 0, discount: 0 }) },
            removeLine(i) { this.lines.splice(i, 1) },
            fillPrice(line) { const it = this.items.find(x => x.id == line.item_id); if (it) line.unit_price = it.price },
            lineTotal(l) { return (l.quantity * l.unit_price) - (l.discount || 0) },
            get grandTotal() { return this.lines.reduce((s, l) => s + this.lineTotal(l), 0) }
        }"
        class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6">
            <x-ui.field label="تاریخ" name="date" type="date" :value="now()->toDateString()" required />
            <x-ui.field label="تامین‌کننده" name="person_id" type="select" required
                :options="['' => '— انتخاب —'] + $suppliers->pluck('name', 'id')->all()" />
            <x-ui.field label="گدام" name="warehouse_id" type="select" required
                :options="$warehouses->pluck('name', 'id')->all()" />
            <div class="grid grid-cols-2 gap-4">
                <x-ui.field label="واحد پول" name="currency_id" type="select" required :options="$currencies->pluck('code', 'id')->all()" />
                <x-ui.field label="نرخ تبدیل" name="fx_rate" type="number" step="0.0001" value="1" required />
            </div>
        </div>

        <h3 class="font-bold mt-4 mb-2 text-sm">اجناس</h3>
        <table class="w-full text-sm text-right mb-3">
            <thead class="text-gray-500">
                <tr><th class="py-1">جنس</th><th>مقدار</th><th>قیمت واحد</th><th>تخفیف</th><th>مجموعه</th><th></th></tr>
            </thead>
            <tbody>
                <template x-for="(line, index) in lines" :key="index">
                    <tr>
                        <td class="py-1 pl-2">
                            <select :name="`lines[${index}][item_id]`" x-model="line.item_id" @change="fillPrice(line)" class="border rounded px-2 py-1 w-full" required>
                                <option value="">— انتخاب —</option>
                                <template x-for="it in items" :key="it.id">
                                    <option :value="it.id" x-text="it.name + ' (' + it.unit + ')'"></option>
                                </template>
                            </select>
                        </td>
                        <td class="pl-2"><input type="number" step="0.01" :name="`lines[${index}][quantity]`" x-model.number="line.quantity" class="border rounded px-2 py-1 w-20" required></td>
                        <td class="pl-2"><input type="number" step="0.01" :name="`lines[${index}][unit_price]`" x-model.number="line.unit_price" class="border rounded px-2 py-1 w-24" required></td>
                        <td class="pl-2"><input type="number" step="0.01" :name="`lines[${index}][discount]`" x-model.number="line.discount" class="border rounded px-2 py-1 w-20"></td>
                        <td class="pl-2" x-text="lineTotal(line).toFixed(2)"></td>
                        <td><button type="button" @click="removeLine(index)" class="text-red-600">✕</button></td>
                    </tr>
                </template>
            </tbody>
        </table>
        <button type="button" @click="addLine()" class="text-sky-700 text-sm mb-4 hover:underline">+ افزودن سطر</button>

        <div class="text-left font-bold text-lg mb-4" x-text="'مجموع کل: ' + grandTotal.toFixed(2)"></div>

        <label class="flex items-center gap-2 text-sm mb-4">
            <input type="checkbox" name="update_cost" value="1" class="rounded border-gray-300 text-sky-600" checked>
            قیمت خرید جنس ها بروزرسانی شود
        </label>

        <x-ui.field label="یادداشت" name="notes" type="textarea" />

        <button type="submit" class="px-6 py-2 bg-orange-700 text-white rounded-md text-sm hover:bg-orange-800">ثبت فاکتور</button>
    </form>
</x-layouts.app>
