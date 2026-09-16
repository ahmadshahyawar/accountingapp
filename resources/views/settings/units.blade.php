<x-layouts.app title="واحد ها">
    <div x-data="{ adding: false }">
        <x-ui.legacy-list title="تعریف واحد ها" create-inline table-id="units-grid">
            <table class="legacy-grid" id="units-grid">
                <thead>
                    <tr>
                        <th>نام واحد</th>
                        <th>علامت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($units as $unit)
                        <tr data-row data-delete-form="delete-unit-{{ $unit->id }}">
                            <td>{{ $unit->name }}</td>
                            <td>{{ $unit->symbol }}</td>
                            <td class="hidden">
                                <form id="delete-unit-{{ $unit->id }}" action="{{ route('settings.units.destroy', $unit) }}" method="POST">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if($units->isEmpty())
                        <tr><td colspan="2" style="text-align:center;color:#9aa3ab;padding:20px">واحدی تعریف نشده است.</td></tr>
                    @endif
                </tbody>
            </table>

            <form action="{{ route('settings.units.store') }}" method="POST" x-show="adding" x-cloak class="legacy-searchrow" style="margin-top:10px">
                @csrf
                <input name="name" placeholder="نام واحد" required style="flex:1;border:none;background:transparent;padding:4px 8px;font-size:13px">
                <input name="symbol" placeholder="علامت" style="width:100px;border:none;background:transparent;padding:4px 8px;font-size:13px">
                <button class="btn3d" type="submit" style="min-height:30px">افزودن</button>
            </form>
        </x-ui.legacy-list>
    </div>
</x-layouts.app>
