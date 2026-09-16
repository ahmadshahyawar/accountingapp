<x-layouts.app title="حساب ها">
    <x-ui.legacy-list title="تعریف حساب ها" :create-route="route('accounts.create', ['type' => $type])" create-label="حساب جدید" table-id="accounts-grid">
        <x-slot:subtabs>
            <x-ui.account-def-tabs :active="$type === 'revenue' ? 'revenue' : 'expense'" />
        </x-slot:subtabs>

        <table class="legacy-grid" id="accounts-grid">
            <thead>
                <tr>
                    <th>کد حساب</th>
                    <th>نام حساب</th>
                    <th>مانده جاری</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accounts as $account)
                    <tr data-row data-edit-url="{{ route('accounts.edit', $account) }}" data-delete-form="delete-account-{{ $account->id }}">
                        <td>{{ $account->code }}</td>
                        <td>{{ $account->name }}</td>
                        <td>{{ $account->is_group ? '—' : number_format($account->balance(), 2) }}</td>
                        <td class="hidden">
                            <form id="delete-account-{{ $account->id }}" action="{{ route('accounts.destroy', $account) }}" method="POST">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
