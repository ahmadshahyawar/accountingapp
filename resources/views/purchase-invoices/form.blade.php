<x-layouts.app title="فاکتور خرید">
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
        }">
        @csrf

        <div class="grid-toolbar" style="border-top:none;border-bottom:1px solid #d7dce1;padding:8px 2px 12px;margin-bottom:0">
            <div class="grp" style="flex:1">
                <div style="width:110px">
                    <label style="font-size:12px;color:#586470;display:block">شماره فاکتور:</label>
                    <input type="text" value="{{ $nextNumber }}" readonly style="width:100%;border:1px solid #b9bfc6;border-radius:3px;padding:5px 8px;font-size:13px;background:#f2f4f6;color:#586470">
                </div>
            </div>
            <div class="legacy-form-title" style="flex:2;padding:0">فاکتور خرید</div>
            <div class="grp" style="flex:1;justify-content:flex-end">
                <div style="width:190px">
                    <label style="font-size:12px;color:#586470;display:block">تاریخ:</label>
                    <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required style="width:100%;border:1px solid #b9bfc6;border-radius:3px;padding:5px 8px;font-size:13px">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 my-4 items-start">
            <div class="md:col-span-2">
                <div class="legacy-field" style="margin-bottom:8px">
                    <label>تامین‌کننده (جستجوی حساب ها)</label>
                    <select name="person_id" x-model="personId" required data-searchable>
                        <option value="">— جستجوی حساب ها —</option>
                        <template x-for="s in suppliers" :key="s.id">
                            <option :value="s.id" x-text="s.name"></option>
                        </template>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="legacy-field" style="margin-bottom:0">
                        <label>موبایل</label>
                        <input type="text" readonly :value="selectedSupplier?.mobile || '—'" style="background:#f2f4f6;color:#586470">
                    </div>
                    <div class="legacy-field" style="margin-bottom:0">
                        <label>تلفن</label>
                        <input type="text" readonly :value="selectedSupplier?.phone || '—'" style="background:#f2f4f6;color:#586470">
                    </div>
                </div>
            </div>
            <div class="legacy-field">
                <label>گدام<span class="text-red-600">*</span></label>
                <select name="warehouse_id" required data-searchable>
                    <option value="">— انتخاب —</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <div class="legacy-field" style="margin-bottom:8px">
                    <label>ارز<span class="text-red-600">*</span></label>
                    <select name="currency_id" required data-searchable>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" @selected(old('currency_id') == $currency->id)>{{ $currency->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="legacy-field" style="margin-bottom:0">
                    <label>نرخ ارز<span class="text-red-600">*</span></label>
                    <input type="number" step="0.0001" name="fx_rate" value="{{ old('fx_rate', 1) }}" required>
                </div>
            </div>
        </div>

        <table class="legacy-grid" style="margin-bottom:8px">
            <thead>
                <tr>
                    <th>کد جنس</th><th>مشخصات جنس</th><th>تعداد</th><th>واحد</th>
                    <th>قیمت فی</th><th>جمع کل</th><th></th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(line, index) in lines" :key="index">
                    <tr>
                        <td x-text="line.code"></td>
                        <td x-text="line.name"></td>
                        <td x-text="line.quantity"></td>
                        <td x-text="line.unit"></td>
                        <td x-text="Number(line.unit_price).toFixed(2)"></td>
                        <td x-text="lineTotal(line).toFixed(2)"></td>
                        <td>
                            <button type="button" @click="removeLine(index)" class="text-red-600">✕</button>
                            <input type="hidden" :name="`lines[${index}][item_id]`" :value="line.item_id">
                            <input type="hidden" :name="`lines[${index}][quantity]`" :value="line.quantity">
                            <input type="hidden" :name="`lines[${index}][unit_price]`" :value="line.unit_price">
                            <input type="hidden" :name="`lines[${index}][discount]`" :value="line.discount || 0">
                        </td>
                    </tr>
                </template>
                <tr x-show="lines.length === 0"><td colspan="7" style="text-align:center;color:#9aa3ab;padding:20px">هیچ جنسی اضافه نشده است</td></tr>
            </tbody>
        </table>

        {{-- Item search + add-line controls — matches the old app's real interaction: fill item,
             quantity and price, then click افزودن (or press Enter) to commit the row. --}}
        <div class="legacy-searchrow" style="gap:8px;margin-bottom:12px">
            <button type="button" @click="addLine()" class="btn3d">
                <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="8"/><path d="M10 6v8M6 10h8"/></svg>
                افزودن
            </button>
            <input type="number" step="0.01" x-model.number="pending.unit_price" @keydown.enter.prevent="addLine()" placeholder="قیمت" style="width:100px;border:1px solid #d1d5db;border-radius:4px;padding:5px 8px;font-size:13px">
            <input type="number" step="0.01" x-model.number="pending.quantity" @keydown.enter.prevent="addLine()" placeholder="تعداد" style="width:100px;border:1px solid #d1d5db;border-radius:4px;padding:5px 8px;font-size:13px">
            <select @change="pending.item_id = $event.target.value; fillPriceFromItem()" data-searchable style="flex:1">
                <option value="">— جستجوی اجناس —</option>
                <template x-for="it in items" :key="it.id">
                    <option :value="it.id" x-text="it.name + ' (' + it.unit + ')'"></option>
                </template>
            </select>
        </div>

        <label class="flex items-center gap-2 text-sm mb-4">
            <input type="checkbox" name="update_cost" value="1" checked style="width:16px;height:16px">
            قیمت خرید جنس ها بروزرسانی شود
        </label>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="space-y-2 order-3 md:order-1">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">تخفیف:</span>
                    <input type="number" step="0.01" name="discount" x-model.number="discount" style="width:130px;border:1px solid #b9bfc6;border-radius:3px;padding:5px 8px;font-size:13px;text-align:left">
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">مصارف:</span>
                    <input type="number" step="0.01" name="expense" x-model.number="expense" style="width:130px;border:1px solid #b9bfc6;border-radius:3px;padding:5px 8px;font-size:13px;text-align:left">
                </div>
                <div class="flex justify-between text-sm font-bold border-t pt-2" style="border-color:#d7dce1"><span>مبلغ کل:</span><span x-text="grandTotal.toFixed(2)"></span></div>
                <div class="flex justify-between text-sm font-bold text-red-700"><span>الباقی حساب:</span><span x-text="remainingBalance.toFixed(2)"></span></div>
            </div>
            <div class="space-y-2 order-2">
                <div style="font-size:13px;color:#384451;text-align:center">حساب قبلی</div>
                <div style="border:1px solid #b9bfc6;border-radius:3px;padding:6px 10px;background:#f2f4f6;text-align:center" x-text="previousBalance.toFixed(2)"></div>
                <div class="flex justify-between items-center text-sm gap-2">
                    <span class="text-gray-600">پرداخت:</span>
                    <input type="number" step="0.01" name="paid_amount" x-model.number="paidAmount" style="width:130px;border:1px solid #b9bfc6;border-radius:3px;padding:5px 8px;font-size:13px;text-align:left">
                </div>
                <button type="button" class="btn3d" style="width:100%;justify-content:center" @click="paidAmount = grandTotal.toFixed(2)">
                    <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="10" cy="10" r="8"/><path d="M6 10l3 3 5-6"/></svg>
                    پرداخت نقدی (تکمیل)
                </button>
            </div>
            <div class="order-1 md:order-3 legacy-field" style="margin-bottom:0">
                <label>توضیحات فاکتور</label>
                <textarea name="notes" rows="6">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="grid-toolbar">
            <div class="grp">
                <button type="submit" class="btn3d" :disabled="lines.length === 0">
                    <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 4h10l2 2v10H4V4z"/><path d="M6 4v4h6V4M6 12h8"/></svg>
                    ذخیره
                </button>
                <button type="button" class="btn3d" onclick="window.print()">
                    <svg class="ic ic-gray" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 8V3h10v5M5 14h10v3H5v-3zM3 8h14v5h-3M3 8v4h2"/></svg>
                    چاپ
                </button>
                <a href="{{ route('purchase-invoices.index') }}" class="btn3d">
                    <svg class="ic ic-red" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="9"/><path d="M7 7l6 6M13 7l-6 6"/></svg>
                    خروج
                </a>
            </div>
        </div>
    </form>
</x-layouts.app>
