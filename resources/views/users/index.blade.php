<x-layouts.app title="مدیریت کاربر ها">
    <x-ui.legacy-list title="مدیریت کاربر ها" :create-route="route('users.create')" create-label="کاربر جدید" table-id="users-grid">
        <table class="legacy-grid" id="users-grid">
            <thead>
                <tr>
                    <th>نام</th>
                    <th>ایمیل</th>
                    <th>نقش</th>
                    <th>وضعیت</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr data-row data-edit-url="{{ route('users.edit', $u) }}" @if($u->id !== auth()->id()) data-delete-form="delete-user-{{ $u->id }}" @endif>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td class="{{ $u->role === 'admin' ? 'text-sky-700 font-semibold' : '' }}">
                            {{ $u->role === 'admin' ? 'مدیر سیستم' : 'کاربر' }}
                        </td>
                        <td class="{{ $u->is_active ? 'text-green-700' : 'text-red-700' }}">
                            {{ $u->is_active ? 'فعال' : 'غیرفعال' }}
                        </td>
                        @if($u->id !== auth()->id())
                            <td class="hidden">
                                <form id="delete-user-{{ $u->id }}" action="{{ route('users.destroy', $u) }}" method="POST">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
