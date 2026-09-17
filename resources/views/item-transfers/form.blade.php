<x-layouts.app title="انتقال اجناس">
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
        }">
        @csrf

        <div class="grid-toolbar" style="border-top:none;border-bottom:1px solid #d7dce1;padding:8px 2px 12px;margin-bottom:0">
            <div class="grp" style="flex:1">
                <div style="width:110px">
                    <label style="font-size:12px;color:#586470;display:block">شماره فاکتور:</label>
                    <input type="text" value="{{ $nextNumber }}" readonly style="width:100%;border:1px solid #b9bfc6;border-radius:3px;padding:5px 8px;font-size:13px;background:#f2f4f6;color:#586470">
                </div>
            </div>
            <div class="legacy-form-title" style="flex:2;padding:0">فاکتور انتقال اجناس</div>
            <div class="grp" style="flex:1;justify-content:flex-end">
                <div style="width:190px">
                    <label style="font-size:12px;color:#586470;display:block">تاریخ:</label>
                    <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required style="width:100%;border:1px solid #b9bfc6;border-radius:3px;padding:5px 8px;font-size:13px">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-4">
            <x-ui.field label="از انبار" name="from_warehouse_id" type="select" required :options="$warehouses->pluck('name', 'id')->all()" />
            <x-ui.field label="به انبار" name="to_warehouse_id" type="select" required :options="$warehouses->pluck('name', 'id')->all()" />
        </div>

        <table class="legacy-grid" style="margin-bottom:8px">
            <thead>
                <tr><th>کد جنس</th><th>مشخصات جنس</th><th>تعداد</th><th>واحد</th><th></th></tr>
            </thead>
            <tbody>
                <template x-for="(line, index) in lines" :key="index">
                    <tr>
                        <td x-text="line.code"></td>
                        <td x-text="line.name"></td>
                        <td x-text="line.quantity"></td>
                        <td x-text="line.unit"></td>
                        <td>
                            <button type="button" @click="removeLine(index)" class="text-red-600">✕</button>
                            <input type="hidden" :name="`lines[${index}][item_id]`" :value="line.item_id">
                            <input type="hidden" :name="`lines[${index}][quantity]`" :value="line.quantity">
                        </td>
                    </tr>
                </template>
                <tr x-show="lines.length === 0"><td colspan="5" style="text-align:center;color:#9aa3ab;padding:20px">هیچ جنسی اضافه نشده است</td></tr>
            </tbody>
        </table>

        <div class="legacy-searchrow" style="gap:8px;margin-bottom:16px">
            <select @change="pending.item_id = $event.target.value" data-searchable style="flex:1">
                <option value="">— جستجوی اجناس —</option>
                <template x-for="it in items" :key="it.id">
                    <option :value="it.id" x-text="it.name + ' (' + it.unit + ')'"></option>
                </template>
            </select>
            <input type="number" step="0.01" x-model.number="pending.quantity" @keydown.enter.prevent="addLine()" placeholder="تعداد" style="width:100px;border:1px solid #d1d5db;border-radius:4px;padding:5px 8px;font-size:13px">
            <button type="button" @click="addLine()" class="btn3d">
                <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="8"/><path d="M10 6v8M6 10h8"/></svg>
                افزودن
            </button>
        </div>

        <x-ui.field label="توضیحات" name="notes" type="textarea" />

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
                <a href="{{ route('item-transfers.index') }}" class="btn3d">
                    <svg class="ic ic-red" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="9"/><path d="M7 7l6 6M13 7l-6 6"/></svg>
                    خروج
                </a>
            </div>
        </div>
    </form>
</x-layouts.app>
