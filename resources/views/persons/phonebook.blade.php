<x-layouts.app title="دفتر تلفن">
    <x-ui.page-header title="دفتر تلفن" :back-route="route('persons.index')" />

    <div class="bg-white rounded border border-gray-300 overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr><th class="px-4 py-2">نام</th><th>تلفن</th><th>موبایل</th><th>آدرس</th></tr>
            </thead>
            <tbody class="divide-y">
                @foreach($persons as $person)
                    <tr>
                        <td class="px-4 py-2">{{ $person->name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $person->phone ?: '—' }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $person->mobile ?: '—' }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $person->address ?: '—' }}</td>
                    </tr>
                @endforeach
                @if($persons->isEmpty())
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">شخصی با شماره تماس ثبت نشده است.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</x-layouts.app>
