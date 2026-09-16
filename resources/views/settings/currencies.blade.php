<x-layouts.app title="ارز ها">
    <div x-data="{ adding: false }">
        <x-ui.legacy-list title="ارز ها" create-inline table-id="currencies-grid">
            <table class="legacy-grid" id="currencies-grid">
                <thead>
                    <tr>
                        <th>کد</th>
                        <th>نام</th>
                        <th>علامت</th>
                        <th>پایه</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($currencies as $currency)
                        <tr data-row>
                            <td>{{ $currency->code }}</td>
                            <td>{{ $currency->name }}</td>
                            <td>{{ $currency->symbol }}</td>
                            <td>{{ $currency->is_base ? 'بلی' : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <form action="{{ route('settings.currencies.store') }}" method="POST" x-show="adding" x-cloak class="legacy-searchrow" style="margin-top:10px;gap:8px">
                @csrf
                <input name="code" placeholder="کد" required style="width:80px;border:none;background:transparent;padding:4px 8px;font-size:13px">
                <input name="name" placeholder="نام" required style="flex:1;border:none;background:transparent;padding:4px 8px;font-size:13px">
                <input name="symbol" placeholder="علامت" style="width:80px;border:none;background:transparent;padding:4px 8px;font-size:13px">
                <button class="btn3d" type="submit" style="min-height:30px">افزودن</button>
            </form>
        </x-ui.legacy-list>
    </div>
</x-layouts.app>
