<x-layouts.app title="فاکتور خرید">
    <x-ui.page-header title="فاکتور خرید جدید" :back-route="route('purchase-invoices.index')" />

    <form action="{{ route('purchase-invoices.store') }}" method="POST"
        x-data="{
            items: @js($items->map(fn($i) => ['id' => $i->id, 'code' => $i->code, 'name' => $i->name, 'price' => (float) $i->cost_price, 'unit' => $i->unit->name])),
            suppliers: @js($suppliers->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'phone' => $s->phone, 'mobile' => $s->mobile])),
            balances: @js($balances),
            personId: '{{ old('person_id') }}',
            lines: [],
            pending: { item_id: '', quantity: 0, unit_price: 0 },
            discount: {{ old('discount', 0) }},
            expense: {{ old('expense', 0) }},
            paidAmount: {{ old('paid_amount', 0) }},
            get selectedSupplier() { return this.suppliers.find(s => s.id == this.personId) },
            get previousBalance() { return this.personId && this.balances[this.personId] ? this.balances[this.personId] : 0 },
            fillPriceFromItem() { const it = this.items.find(x => x.id == this.pending.item_id); if (it) this.pending.unit_price = it.price },
            addLine() {
                if (!this.pending.item_id || !this.pending.quantity) return;
                const it = this.items.find(x => x.id == this.pending.item_id);
                this.lines.push({ item_id: this.pending.item_id, code: it.code, name: it.name, unit: it.unit, quantity: this.pending.quantity, unit_price: this.pending.unit_price, discount: 0 });
                this.pending = { item_id: '', quantity: 0, unit_price: 0 };
            },
            removeLine(i) { this.lines.splice(i, 1) },
            lineTotal(l) { return (l.quantity * l.unit_price) - (l.discount || 0) },
            get linesTotal() { return this.lines.reduce((s, l) => s + this.lineTotal(l), 0) },
            get grandTotal() { return this.linesTotal - (parseFloat(this.discount) || 0) + (parseFloat(this.expense) || 0) },
            get remainingBalance() { return this.previousBalance + this.grandTotal - (parseFloat(this.paidAmount) || 0) },
        }"
        class="bg-white rounded border border-gray-300 p-6">
        @csrf

        <div class="flex justify-between items-start mb-4">
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">تاریخ<span class="text-red-600">*</span></label>
                <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 px-3 border">
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">شماره فاکتور</label>
                <input type="text" value="{{ $nextNumber }}" readonly
                    class="w-full rounded-md border-gray-200 bg-gray-50 shadow-sm text-sm py-2 px-3 border text-gray-500 text-left">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 items-start">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ارز<span class="text-red-600">*</span></label>
                <select name="currency_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 px-3 border">
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" @selected(old('currency_id') == $currency->id)>{{ $currency->code }}</option>
                    @endforeach
                </select>
                <label class="block text-sm font-medium text-gray-700 mt-2 mb-1">نرخ ارز<span class="text-red-600">*</span></label>
                <input type="number" step="0.0001" name="fx_rate" value="{{ old('fx_rate', 1) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 px-3 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">گدام<span class="text-red-600">*</span></label>
                <select name="warehouse_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 px-3 border">
                    <option value="">— انتخاب —</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">تامین‌کننده<span class="text-red-600">*</span></label>
                <select name="person_id" x-model="personId" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 px-3 border">
                    <option value="">— جستجوی حساب ها —</option>
                    <template x-for="s in suppliers" :key="s.id">
                        <option :value="s.id" x-text="s.name"></option>
                    </template>
                </select>
                <div class="flex gap-6 mt-2 text-sm text-gray-500">
                    <span>تلفن: <span x-text="selectedSupplier?.phone || '—'"></span></span>
                    <span>موبایل: <span x-text="selectedSupplier?.mobile || '—'"></span></span>
                </div>
            </div>
        </div>

        <h3 class="font-bold mt-2 mb-2 text-sm">اجناس فاکتور</h3>
        <table class="w-full text-sm text-right mb-2 border">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-2 py-1.5">کد جنس</th><th>مشخصات جنس</th><th>واحد</th><th>تعداد</th>
                    <th>قیمت فی</th><th>جمع کل</th><th>مفاد</th><th></th>
                </tr>
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
                        <td x-text="Number(line.discount || 0).toFixed(2)"></td>
                        <td>
                            <button type="button" @click="removeLine(index)" class="text-red-600">✕</button>
                            <input type="hidden" :name="`lines[${index}][item_id]`" :value="line.item_id">
                            <input type="hidden" :name="`lines[${index}][quantity]`" :value="line.quantity">
                            <input type="hidden" :name="`lines[${index}][unit_price]`" :value="line.unit_price">
                            <input type="hidden" :name="`lines[${index}][discount]`" :value="line.discount || 0">
                        </td>
                    </tr>
                </template>
                <tr x-show="lines.length === 0"><td colspan="8" class="text-center text-gray-400 py-6">هیچ جنسی اضافه نشده است</td></tr>
            </tbody>
        </table>

        {{-- Item search + add-line controls — matches the old app's real interaction: fill item,
             quantity and price, then click افزودن (or press Enter) to commit the row. --}}
        <div class="flex items-end gap-3 mb-4 bg-gray-50 border rounded-md p-3">
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">اجناس</label>
                <select x-model="pending.item_id" @change="fillPriceFromItem()" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 px-3 border">
                    <option value="">— جستجوی اجناس —</option>
                    <template x-for="it in items" :key="it.id">
                        <option :value="it.id" x-text="it.name + ' (' + it.unit + ')'"></option>
                    </template>
                </select>
            </div>
            <div class="w-28">
                <label class="block text-xs text-gray-500 mb-1">تعداد</label>
                <input type="number" step="0.01" x-model.number="pending.quantity" @keydown.enter.prevent="addLine()"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 px-3 border">
            </div>
            <div class="w-28">
                <label class="block text-xs text-gray-500 mb-1">قیمت</label>
                <input type="number" step="0.01" x-model.number="pending.unit_price" @keydown.enter.prevent="addLine()"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 px-3 border">
            </div>
            <button type="button" @click="addLine()" class="px-4 py-2 bg-orange-700 text-white rounded-md text-sm hover:bg-orange-800 whitespace-nowrap">+ افزودن</button>
        </div>

        <label class="flex items-center gap-2 text-sm mb-4">
            <input type="checkbox" name="update_cost" value="1" class="rounded border-gray-300 text-orange-600" checked>
            قیمت خرید جنس ها بروزرسانی شود
        </label>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="space-y-2 order-3 md:order-1">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">تخفیف:</span>
                    <input type="number" step="0.01" name="discount" x-model.number="discount"
                        class="w-32 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-1.5 px-2 border text-left">
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">مصارف:</span>
                    <input type="number" step="0.01" name="expense" x-model.number="expense"
                        class="w-32 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-1.5 px-2 border text-left">
                </div>
                <div class="flex justify-between text-sm font-bold border-t pt-2"><span>مبلغ کل:</span><span x-text="grandTotal.toFixed(2)"></span></div>
                <div class="flex justify-between text-sm font-bold text-red-700"><span>الباقی حساب:</span><span x-text="remainingBalance.toFixed(2)"></span></div>
            </div>
            <div class="space-y-2 order-2">
                <div class="flex justify-between text-sm"><span class="text-gray-600">حساب قبلی:</span><span x-text="previousBalance.toFixed(2)"></span></div>
                <div class="flex justify-between items-center text-sm gap-2">
                    <span class="text-gray-600">پرداخت:</span>
                    <input type="number" step="0.01" name="paid_amount" x-model.number="paidAmount"
                        class="w-32 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-1.5 px-2 border text-left">
                </div>
            </div>
            <div class="order-1 md:order-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">توضیحات فاکتور</label>
                <textarea name="notes" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm py-2 px-3 border">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between border-t pt-4">
            <a href="{{ route('purchase-invoices.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300">خروج</a>
            <button type="submit" class="px-8 py-2 bg-orange-700 text-white rounded-md text-sm hover:bg-orange-800">ذخیره</button>
        </div>
    </form>
</x-layouts.app>
