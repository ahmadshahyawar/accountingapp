<x-layouts.app title="صندوق">
    <x-ui.legacy-list title="دریافت و پرداخت نقدی" table-id="cash-vouchers-grid">
        <x-slot:subtabs>
            <div class="flex justify-end gap-1 mb-2 text-sm">
                @foreach(['all' => 'همه', 'receipt' => 'دریافت ها', 'payment' => 'پرداخت ها'] as $key => $label)
                    <a href="{{ route('cash-vouchers.index', ['type' => $key]) }}"
                        class="px-4 py-1.5 border-t border-x rounded-t {{ $type === $key ? 'bg-white border-gray-300 font-semibold text-sky-800' : 'bg-gray-100 border-transparent text-gray-500 hover:text-sky-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </x-slot:subtabs>

        <table class="legacy-grid" id="cash-vouchers-grid">
            <thead>
                <tr>
                    <th>شماره</th>
                    <th>تاریخ</th>
                    <th>نوعیت</th>
                    <th>شخص</th>
                    <th>صندوق/بانک</th>
                    <th>مبلغ</th>
                    <th>توضیحات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vouchers as $voucher)
                    <tr data-row data-delete-form="delete-voucher-{{ $voucher->id }}">
                        <td>{{ $voucher->number }}</td>
                        <td>{{ shamsi($voucher->date) }}</td>
                        <td class="{{ $voucher->type === 'receipt' ? 'text-green-700' : 'text-pink-700' }}">
                            {{ $voucher->type === 'receipt' ? 'دریافت' : 'پرداخت' }}
                        </td>
                        <td>{{ $voucher->person?->name }}</td>
                        <td>{{ $voucher->cashbox?->name ?? $voucher->bankAccount?->name }}</td>
                        <td>{{ number_format($voucher->amount, 2) }} {{ $voucher->currency->code }}</td>
                        <td>{{ $voucher->description }}</td>
                        <td class="hidden">
                            <form id="delete-voucher-{{ $voucher->id }}" action="{{ route('cash-vouchers.destroy', $voucher) }}" method="POST">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="grid-toolbar" style="border-top:none;padding-top:0;margin-top:-6px;justify-content:flex-start">
            <div class="grp">
                <a href="{{ route('cash-vouchers.create', ['type' => 'receipt']) }}" class="btn3d">
                    <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="8"/><path d="M10 6v8M6 10h8"/></svg>
                    دریافت نقدی
                </a>
                <a href="{{ route('cash-vouchers.create', ['type' => 'payment']) }}" class="btn3d">
                    <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="8"/><path d="M10 6v8M6 10h8"/></svg>
                    پرداخت نقدی
                </a>
            </div>
        </div>
    </x-ui.legacy-list>
</x-layouts.app>
