<x-layouts.app title="صندوق">
    <x-ui.page-header title="دریافت و پرداخت نقدی">
        <a href="{{ route('cash-vouchers.create', ['type' => 'receipt']) }}" class="px-4 py-2 bg-green-700 text-white rounded-md text-sm hover:bg-green-800">+ دریافت نقدی</a>
        <a href="{{ route('cash-vouchers.create', ['type' => 'payment']) }}" class="px-4 py-2 bg-pink-700 text-white rounded-md text-sm hover:bg-pink-800">+ پرداخت نقدی</a>
    </x-ui.page-header>

    <div class="flex gap-2 mb-4 text-sm">
        @foreach(['all' => 'همه', 'receipt' => 'دریافت ها', 'payment' => 'پرداخت ها'] as $key => $label)
            <a href="{{ route('cash-vouchers.index', ['type' => $key]) }}"
                class="px-3 py-1.5 rounded-md {{ $type === $key ? 'bg-sky-700 text-white' : 'bg-white text-gray-600 border' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="bg-white rounded border border-gray-300 overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">شماره</th>
                    <th class="px-4 py-2">تاریخ</th>
                    <th class="px-4 py-2">نوعیت</th>
                    <th class="px-4 py-2">شخص</th>
                    <th class="px-4 py-2">صندوق/بانک</th>
                    <th class="px-4 py-2">مبلغ</th>
                    <th class="px-4 py-2">توضیحات</th>
                    <th class="px-4 py-2">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($vouchers as $voucher)
                    <tr>
                        <td class="px-4 py-2">{{ $voucher->number }}</td>
                        <td class="px-4 py-2">{{ shamsi($voucher->date) }}</td>
                        <td class="px-4 py-2">
                            <span class="{{ $voucher->type === 'receipt' ? 'text-green-700' : 'text-pink-700' }}">
                                {{ $voucher->type === 'receipt' ? 'دریافت' : 'پرداخت' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-gray-500">{{ $voucher->person?->name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $voucher->cashbox?->name ?? $voucher->bankAccount?->name }}</td>
                        <td class="px-4 py-2">{{ number_format($voucher->amount, 2) }} {{ $voucher->currency->code }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $voucher->description }}</td>
                        <td class="px-4 py-2">
                            <form action="{{ route('cash-vouchers.destroy', $voucher) }}" method="POST" onsubmit="return confirm('این رسید حذف شود؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.app>
