<x-layouts.app title="اشخاص">
    <x-ui.page-header title="اشخاص (مشتریان، تامین‌کنندگان، کارمندان)">
        <a href="{{ route('persons.create') }}" class="px-4 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">+ شخص جدید</a>
    </x-ui.page-header>

    <div class="flex gap-2 mb-4 text-sm">
        @foreach(['all' => 'همه', 'customer' => 'مشتریان', 'supplier' => 'تامین‌کنندگان', 'employee' => 'کارمندان'] as $key => $label)
            <a href="{{ route('persons.index', ['type' => $key]) }}"
                class="px-3 py-1.5 rounded-md {{ $filter === $key ? 'bg-sky-700 text-white' : 'bg-white text-gray-600 border' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">کد</th>
                    <th class="px-4 py-2">نام</th>
                    <th class="px-4 py-2">نام پدر</th>
                    <th class="px-4 py-2">موبایل</th>
                    <th class="px-4 py-2">تلفن</th>
                    <th class="px-4 py-2">آدرس</th>
                    <th class="px-4 py-2">نوعیت</th>
                    <th class="px-4 py-2">مانده حساب</th>
                    <th class="px-4 py-2">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($persons as $person)
                    @php
                        $balance = $person->journalLines()->sum('base_debit') - $person->journalLines()->sum('base_credit');
                    @endphp
                    <tr>
                        <td class="px-4 py-2 text-gray-400">{{ $person->code() }}</td>
                        <td class="px-4 py-2">{{ $person->name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $person->father_name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $person->mobile }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $person->phone }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $person->address }}</td>
                        <td class="px-4 py-2 text-gray-500 text-xs">
                            @if($person->is_customer) <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded">مشتری</span> @endif
                            @if($person->is_supplier) <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded">تامین‌کننده</span> @endif
                            @if($person->is_employee) <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded">کارمند</span> @endif
                        </td>
                        <td class="px-4 py-2 {{ $balance >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ number_format($balance, 2) }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('persons.edit', $person) }}" class="text-sky-700 hover:underline">ویرایش</a>
                            <form action="{{ route('persons.destroy', $person) }}" method="POST" onsubmit="return confirm('حذف شود؟')">
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
