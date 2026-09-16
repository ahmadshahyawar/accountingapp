<x-layouts.app title="انبار ها">
    <div x-data="{ adding: false }">
        <x-ui.legacy-list title="تعریف انبار ها" create-inline table-id="warehouses-grid">
            <table class="legacy-grid" id="warehouses-grid">
                <thead>
                    <tr>
                        <th>نام گدام</th>
                        <th>آدرس</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($warehouses as $warehouse)
                        <tr data-row data-delete-form="delete-warehouse-{{ $warehouse->id }}">
                            <td>{{ $warehouse->name }}</td>
                            <td>{{ $warehouse->address }}</td>
                            <td class="hidden">
                                <form id="delete-warehouse-{{ $warehouse->id }}" action="{{ route('settings.warehouses.destroy', $warehouse) }}" method="POST">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if($warehouses->isEmpty())
                        <tr><td colspan="2" style="text-align:center;color:#9aa3ab;padding:20px">گدامی تعریف نشده است.</td></tr>
                    @endif
                </tbody>
            </table>

            <form action="{{ route('settings.warehouses.store') }}" method="POST" x-show="adding" x-cloak class="legacy-searchrow" style="margin-top:10px">
                @csrf
                <input name="name" placeholder="نام گدام" required style="flex:1;border:none;background:transparent;padding:4px 8px;font-size:13px">
                <input name="address" placeholder="آدرس" style="flex:1;border:none;background:transparent;padding:4px 8px;font-size:13px">
                <button class="btn3d" type="submit" style="min-height:30px">افزودن</button>
            </form>
        </x-ui.legacy-list>
    </div>
</x-layouts.app>
