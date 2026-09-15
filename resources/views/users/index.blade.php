<x-layouts.app title="مدیریت کاربر ها">
    <x-ui.page-header title="مدیریت کاربر ها">
        <a href="{{ route('users.create') }}" class="px-4 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">+ کاربر جدید</a>
    </x-ui.page-header>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">نام</th>
                    <th class="px-4 py-2">ایمیل</th>
                    <th class="px-4 py-2">نقش</th>
                    <th class="px-4 py-2">وضعیت</th>
                    <th class="px-4 py-2">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($users as $u)
                    <tr>
                        <td class="px-4 py-2">{{ $u->name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $u->email }}</td>
                        <td class="px-4 py-2">
                            <span class="{{ $u->role === 'admin' ? 'text-sky-700 font-semibold' : 'text-gray-600' }}">
                                {{ $u->role === 'admin' ? 'مدیر سیستم' : 'کاربر' }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <span class="{{ $u->is_active ? 'text-green-700' : 'text-red-700' }}">
                                {{ $u->is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 flex gap-3">
                            <a href="{{ route('users.edit', $u) }}" class="text-sky-700 hover:underline">ویرایش</a>
                            @if($u->id !== auth()->id())
                                <form action="{{ route('users.destroy', $u) }}" method="POST" onsubmit="return confirm('این کاربر حذف شود؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">حذف</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.app>
