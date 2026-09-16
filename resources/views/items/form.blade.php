<x-layouts.app title="جنس">
    <x-ui.page-header :title="$item->exists ? 'ویرایش جنس' : 'جنس جدید'" :back-route="route('items.index')" />

    <form action="{{ $item->exists ? route('items.update', $item) : route('items.store') }}" method="POST"
        enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 max-w-3xl grid grid-cols-1 md:grid-cols-3 gap-6"
        x-data="{ removePhoto: false, previewUrl: @js($item->photoUrl()) }">
        @csrf
        @if($item->exists) @method('PUT') @endif

        {{-- Fields — right side, matching the old app's تعریف جنس layout --}}
        <div class="md:col-span-2 md:order-2">
            <x-ui.field label="کد جنس" name="code" :value="$item->code" required />
            <x-ui.field label="نام جنس" name="name" :value="$item->name" required />

            <x-ui.field label="واحد" name="unit_id" type="select" :value="$item->unit_id" required
                :options="$units->pluck('name', 'id')->all()" />

            <x-ui.field label="گدام پیش‌فرض" name="warehouse_id" type="select" :value="$item->warehouse_id"
                :options="['' => '— ندارد —'] + $warehouses->pluck('name', 'id')->all()" />

            <x-ui.field label="قیمت خرید" name="cost_price" type="number" step="0.01" :value="$item->cost_price" required />
            <x-ui.field label="قیمت فروش" name="sale_price" type="number" step="0.01" :value="$item->sale_price" required />
            <x-ui.field label="حد اقل موجودی (هشدار)" name="reorder_level" type="number" step="0.01" :value="$item->reorder_level" />

            <button type="submit" class="px-6 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">ذخیره</button>
        </div>

        {{-- Barcode + photo — left side, matching the old app's باکرد / عکس panels --}}
        <div class="md:order-1 space-y-6">
            @if($item->exists)
                <div>
                    <div class="text-sm font-medium text-gray-700 mb-1">بارکد:</div>
                    <div class="border rounded-md p-3 flex flex-col items-center bg-white">
                        <svg id="item-barcode" data-code="{{ $item->code }}"></svg>
                    </div>
                    <button type="button" onclick="printItemBarcode()" class="mt-2 w-full px-4 py-2 bg-gray-100 border rounded-md text-sm hover:bg-gray-200">
                        چاپ بارکد
                    </button>
                </div>
            @endif

            <div>
                <div class="text-sm font-medium text-gray-700 mb-1">عکس:</div>
                <div class="border rounded-md h-40 flex items-center justify-center bg-gray-50 overflow-hidden">
                    <img x-show="previewUrl && !removePhoto" :src="previewUrl" class="max-h-full max-w-full object-contain">
                    <span x-show="!previewUrl || removePhoto" class="text-gray-400 text-sm">...</span>
                </div>
                <input type="file" name="photo" accept="image/*"
                    @change="removePhoto = false; previewUrl = URL.createObjectURL($event.target.files[0])"
                    class="mt-2 w-full text-sm">
                @if($item->photo_path)
                    <label class="flex items-center gap-2 mt-2 text-sm text-red-600">
                        <input type="checkbox" name="remove_photo" value="1" x-model="removePhoto" class="rounded border-gray-300 text-red-600">
                        حذف عکس
                    </label>
                @endif
            </div>
        </div>
    </form>

    @if($item->exists)
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const el = document.getElementById('item-barcode');
                if (el && window.JsBarcode) {
                    window.JsBarcode(el, el.dataset.code, { format: 'CODE128', height: 50, fontSize: 14, margin: 4 });
                }
            });

            function printItemBarcode() {
                const svg = document.getElementById('item-barcode').outerHTML;
                const win = window.open('', '_blank', 'width=400,height=300');
                win.document.write('<html><body style="display:flex;align-items:center;justify-content:center;margin:0">' + svg + '</body></html>');
                win.document.close();
                win.focus();
                win.print();
            }
        </script>
        @endpush
    @endif
</x-layouts.app>
