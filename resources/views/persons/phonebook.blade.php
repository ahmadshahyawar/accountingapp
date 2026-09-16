<x-layouts.app title="دفتر تلفن">
    <x-ui.legacy-list title="دفتر تلفن" report-only table-id="phonebook-grid">
        <table class="legacy-grid" id="phonebook-grid">
            <thead>
                <tr><th>نام</th><th>تلفن</th><th>موبایل</th><th>آدرس</th></tr>
            </thead>
            <tbody>
                @foreach($persons as $person)
                    <tr data-row>
                        <td>{{ $person->name }}</td>
                        <td>{{ $person->phone ?: '—' }}</td>
                        <td>{{ $person->mobile ?: '—' }}</td>
                        <td>{{ $person->address ?: '—' }}</td>
                    </tr>
                @endforeach
                @if($persons->isEmpty())
                    <tr><td colspan="4" style="text-align:center;color:#9aa3ab;padding:20px">شخصی با شماره تماس ثبت نشده است.</td></tr>
                @endif
            </tbody>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
