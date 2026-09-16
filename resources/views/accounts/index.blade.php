<x-layouts.app title="حساب ها">
    <x-ui.page-header title="تعریف حساب ها">
        <a href="{{ route('accounts.create') }}" class="px-4 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">+ حساب جدید</a>
    </x-ui.page-header>

    @if($type)
        <div class="mb-4 text-sm flex items-center gap-2">
            <span class="text-gray-500">فیلتر: {{ ['revenue' => 'عواید', 'expense' => 'مصارف'][$type] ?? $type }}</span>
            <a href="{{ route('accounts.index') }}" class="text-sky-700 hover:underline">(نمایش همه)</a>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">کد</th>
                    <th class="px-4 py-2">نام حساب</th>
                    <th class="px-4 py-2">حساب مادر</th>
                    <th class="px-4 py-2">نوعیت</th>
                    <th class="px-4 py-2">مانده جاری</th>
                    <th class="px-4 py-2">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($accounts as $account)
                    <tr class="{{ $account->is_group ? 'bg-gray-50 font-semibold' : '' }}">
                        <td class="px-4 py-2">{{ $account->code }}</td>
                        <td class="px-4 py-2">{{ $account->name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $account->parent?->name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $account->typeLabel() }}</td>
                        <td class="px-4 py-2">{{ $account->is_group ? '—' : number_format($account->balance(), 2) }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('accounts.edit', $account) }}" class="text-sky-700 hover:underline">ویرایش</a>
                            <form action="{{ route('accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('حذف شود؟')">
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
