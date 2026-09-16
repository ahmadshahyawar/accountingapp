<x-layouts.app title="اشخاص">
    <x-ui.legacy-list title="تعریف حساب ها" :create-route="route('persons.create', ['type' => $filter === 'employee' ? 'employee' : null])" :create-label="$filter === 'employee' ? 'کارمند جدید' : 'شخص جدید'" table-id="persons-grid">
        <x-slot:subtabs>
            <x-ui.account-def-tabs :active="$filter === 'employee' ? 'employees' : 'persons'" />
        </x-slot:subtabs>

        <table class="legacy-grid" id="persons-grid">
            <thead>
                <tr>
                    <th>کد شخص</th>
                    <th>نام شخص</th>
                    <th>موبایل</th>
                    <th>آدرس</th>
                    <th>توضیحات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($persons as $person)
                    <tr data-row data-edit-url="{{ route('persons.edit', $person) }}" data-delete-form="delete-person-{{ $person->id }}">
                        <td>{{ $person->code() }}</td>
                        <td>{{ $person->name }}</td>
                        <td>{{ $person->mobile }}</td>
                        <td>{{ $person->address }}</td>
                        <td>{{ $person->notes }}</td>
                        <td class="hidden">
                            <form id="delete-person-{{ $person->id }}" action="{{ route('persons.destroy', $person) }}" method="POST">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
