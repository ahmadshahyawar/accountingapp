<x-layouts.app title="انتقال اجناس">
    <x-ui.page-header title="انتقال اجناس جدید" :back-route="route('item-transfers.index')" />

    <form action="{{ route('item-transfers.store') }}" method="POST"
        x-data="{
            items: @js($items->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'unit' => $i->unit->name])),
            lines: [{ item_id: '', quantity: 1 }],
            addLine() { this.lines.push({ item_id: '', quantity: 1 }) },
            removeLine(i) { this.lines.splice(i, 1) }
        }"
        class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-6">
            <x-ui.field label="تاریخ" name="date" type="date" :value="now()->toDateString()" required />
            <x-ui.field label="از گدام" name="from_warehouse_id" type="select" required :options="$warehouses->pluck('name', 'id')->all()" />
            <x-ui.field label="به گدام" name="to_warehouse_id" type="select" required :options="$warehouses->pluck('name', 'id')->all()" />
        </div>

        <h3 class="font-bold mt-4 mb-2 text-sm">اجناس</h3>
        <table class="w-full text-sm text-right mb-3">
            <thead class="text-gray-500">
                <tr><th class="py-1">جنس</th><th>مقدار</th><th></th></tr>
            </thead>
            <tbody>
                <template x-for="(line, index) in lines" :key="index">
                    <tr>
                        <td class="py-1 pl-2">
                            <select :name="`lines[${index}][item_id]`" x-model="line.item_id" class="border rounded px-2 py-1 w-full" required>
                                <option value="">— انتخاب —</option>
                                <template x-for="it in items" :key="it.id">
                                    <option :value="it.id" x-text="it.name + ' (' + it.unit + ')'"></option>
                                </template>
                            </select>
                        </td>
                        <td class="pl-2"><input type="number" step="0.01" :name="`lines[${index}][quantity]`" x-model.number="line.quantity" class="border rounded px-2 py-1 w-24" required></td>
                        <td><button type="button" @click="removeLine(index)" class="text-red-600">✕</button></td>
                    </tr>
                </template>
            </tbody>
        </table>
        <button type="button" @click="addLine()" class="text-sky-700 text-sm mb-4 hover:underline">+ افزودن سطر</button>

        <x-ui.field label="یادداشت" name="notes" type="textarea" />

        <button type="submit" class="px-6 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">ثبت انتقال</button>
    </form>
</x-layouts.app>
