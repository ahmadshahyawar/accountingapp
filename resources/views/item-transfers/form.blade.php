<x-layouts.app title="انتقال اجناس">
    <x-ui.page-header title="انتقال اجناس جدید" :back-route="route('item-transfers.index')" />

    <form action="{{ route('item-transfers.store') }}" method="POST"
        x-data="{
            items: @js($items->map(fn($i) => ['id' => $i->id, 'code' => $i->code, 'name' => $i->name, 'unit' => $i->unit->name])),
            lines: [],
            pending: { item_id: '', quantity: 0 },
            addLine() {
                if (!this.pending.item_id || !this.pending.quantity) return;
                const it = this.items.find(x => x.id == this.pending.item_id);
                this.lines.push({ item_id: this.pending.item_id, code: it.code, name: it.name, unit: it.unit, quantity: this.pending.quantity });
                this.pending = { item_id: '', quantity: 0 };
            },
            removeLine(i) { this.lines.splice(i, 1) }
        }"
        class="bg-white rounded border border-gray-300 p-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-6">
            <x-ui.field label="تاریخ" name="date" type="date" :value="now()->toDateString()" required />
            <x-ui.field label="از گدام" name="from_warehouse_id" type="select" required :options="$warehouses->pluck('name', 'id')->all()" />
            <x-ui.field label="به گدام" name="to_warehouse_id" type="select" required :options="$warehouses->pluck('name', 'id')->all()" />
        </div>

        <h3 class="font-bold mt-4 mb-2 text-sm">اجناس</h3>
        <table class="w-full text-sm text-right mb-2 border">
            <thead class="bg-gray-50 text-gray-600">
                <tr><th class="px-2 py-1.5">کد جنس</th><th>مشخصات جنس</th><th>واحد</th><th>تعداد</th><th></th></tr>
            </thead>
            <tbody class="divide-y">
                <template x-for="(line, index) in lines" :key="index">
                    <tr>
                        <td class="px-2 py-1.5" x-text="line.code"></td>
                        <td x-text="line.name"></td>
                        <td x-text="line.unit"></td>
                        <td x-text="line.quantity"></td>
                        <td>
                            <button type="button" @click="removeLine(index)" class="text-red-600">✕</button>
                            <input type="hidden" :name="`lines[${index}][item_id]`" :value="line.item_id">
                            <input type="hidden" :name="`lines[${index}][quantity]`" :value="line.quantity">
                        </td>
                    </tr>
                </template>
                <tr x-show="lines.length === 0"><td colspan="5" class="text-center text-gray-400 py-6">هیچ جنسی اضافه نشده است</td></tr>
            </tbody>
        </table>

        <div class="flex items-end gap-3 mb-4 bg-gray-50 border rounded-md p-3">
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">اجناس</label>
                <select x-model="pending.item_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border">
                    <option value="">— جستجوی اجناس —</option>
                    <template x-for="it in items" :key="it.id">
                        <option :value="it.id" x-text="it.name + ' (' + it.unit + ')'"></option>
                    </template>
                </select>
            </div>
            <div class="w-28">
                <label class="block text-xs text-gray-500 mb-1">تعداد</label>
                <input type="number" step="0.01" x-model.number="pending.quantity" @keydown.enter.prevent="addLine()"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border">
            </div>
            <button type="button" @click="addLine()" class="px-4 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800 whitespace-nowrap">+ افزودن</button>
        </div>

        <x-ui.field label="یادداشت" name="notes" type="textarea" />

        <button type="submit" class="px-6 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">ثبت انتقال</button>
    </form>
</x-layouts.app>
