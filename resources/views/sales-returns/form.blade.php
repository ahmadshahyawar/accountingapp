<x-layouts.app title="برگشت از فروش">
    <x-ui.page-header title="برگشت از فروش جدید" :back-route="route('sales-returns.index')" />

    <form action="{{ route('sales-returns.store') }}" method="POST"
        x-data="{
            items: @js($items->map(fn($i) => ['id' => $i->id, 'code' => $i->code, 'name' => $i->name, 'price' => (float) $i->sale_price, 'unit' => $i->unit->name])),
            lines: [],
            pending: { item_id: '', quantity: 0, unit_price: 0 },
            fillPriceFromItem() { const it = this.items.find(x => x.id == this.pending.item_id); if (it) this.pending.unit_price = it.price },
            addLine() {
                if (!this.pending.item_id || !this.pending.quantity) return;
                const it = this.items.find(x => x.id == this.pending.item_id);
                this.lines.push({ item_id: this.pending.item_id, code: it.code, name: it.name, unit: it.unit, quantity: this.pending.quantity, unit_price: this.pending.unit_price });
                this.pending = { item_id: '', quantity: 0, unit_price: 0 };
            },
            removeLine(i) { this.lines.splice(i, 1) },
            lineTotal(l) { return l.quantity * l.unit_price },
            get grandTotal() { return this.lines.reduce((s, l) => s + this.lineTotal(l), 0) }
        }"
        class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6">
            <x-ui.field label="تاریخ" name="date" type="date" :value="now()->toDateString()" required />
            <x-ui.field label="مشتری" name="person_id" type="select" required :options="['' => '— انتخاب —'] + $customers->pluck('name', 'id')->all()" />
            <x-ui.field label="گدام" name="warehouse_id" type="select" required :options="$warehouses->pluck('name', 'id')->all()" />
            <div class="grid grid-cols-2 gap-4">
                <x-ui.field label="واحد پول" name="currency_id" type="select" required :options="$currencies->pluck('code', 'id')->all()" />
                <x-ui.field label="نرخ تبدیل" name="fx_rate" type="number" step="0.0001" value="1" required />
            </div>
        </div>

        <h3 class="font-bold mt-4 mb-2 text-sm">اجناس برگشتی</h3>
        <table class="w-full text-sm text-right mb-2 border">
            <thead class="bg-gray-50 text-gray-600">
                <tr><th class="px-2 py-1.5">کد جنس</th><th>مشخصات جنس</th><th>واحد</th><th>تعداد</th><th>قیمت واحد</th><th>مجموعه</th><th></th></tr>
            </thead>
            <tbody class="divide-y">
                <template x-for="(line, index) in lines" :key="index">
                    <tr>
                        <td class="px-2 py-1.5" x-text="line.code"></td>
                        <td x-text="line.name"></td>
                        <td x-text="line.unit"></td>
                        <td x-text="line.quantity"></td>
                        <td x-text="Number(line.unit_price).toFixed(2)"></td>
                        <td x-text="lineTotal(line).toFixed(2)"></td>
                        <td>
                            <button type="button" @click="removeLine(index)" class="text-red-600">✕</button>
                            <input type="hidden" :name="`lines[${index}][item_id]`" :value="line.item_id">
                            <input type="hidden" :name="`lines[${index}][quantity]`" :value="line.quantity">
                            <input type="hidden" :name="`lines[${index}][unit_price]`" :value="line.unit_price">
                        </td>
                    </tr>
                </template>
                <tr x-show="lines.length === 0"><td colspan="7" class="text-center text-gray-400 py-6">هیچ جنسی اضافه نشده است</td></tr>
            </tbody>
        </table>

        <div class="flex items-end gap-3 mb-4 bg-gray-50 border rounded-md p-3">
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">اجناس</label>
                <select x-model="pending.item_id" @change="fillPriceFromItem()" class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm py-2 px-3 border">
                    <option value="">— جستجوی اجناس —</option>
                    <template x-for="it in items" :key="it.id">
                        <option :value="it.id" x-text="it.name + ' (' + it.unit + ')'"></option>
                    </template>
                </select>
            </div>
            <div class="w-28">
                <label class="block text-xs text-gray-500 mb-1">تعداد</label>
                <input type="number" step="0.01" x-model.number="pending.quantity" @keydown.enter.prevent="addLine()"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm py-2 px-3 border">
            </div>
            <div class="w-28">
                <label class="block text-xs text-gray-500 mb-1">قیمت</label>
                <input type="number" step="0.01" x-model.number="pending.unit_price" @keydown.enter.prevent="addLine()"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm py-2 px-3 border">
            </div>
            <button type="button" @click="addLine()" class="px-4 py-2 bg-amber-700 text-white rounded-md text-sm hover:bg-amber-800 whitespace-nowrap">+ افزودن</button>
        </div>

        <div class="text-left font-bold text-lg mb-4" x-text="'مجموع کل: ' + grandTotal.toFixed(2)"></div>

        <x-ui.field label="یادداشت" name="notes" type="textarea" />

        <button type="submit" class="px-6 py-2 bg-amber-700 text-white rounded-md text-sm hover:bg-amber-800">ثبت برگشت از فروش</button>
    </form>
</x-layouts.app>
