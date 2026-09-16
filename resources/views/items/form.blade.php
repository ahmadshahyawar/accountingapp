<x-layouts.app title="جنس">
    <form action="{{ $item->exists ? route('items.update', $item) : route('items.store') }}" method="POST"
        enctype="multipart/form-data"
        x-data="{ removePhoto: false, previewUrl: @js($item->photoUrl()) }">
        @csrf
        @if($item->exists) @method('PUT') @endif

        <x-ui.legacy-form :title="$item->exists ? 'ویرایش جنس' : 'تعریف جنس'" :back-route="route('items.index')">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
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
                </div>

                {{-- Barcode + photo — left side, matching the old app's باکرد / عکس panels --}}
                <div class="md:order-1 space-y-6">
                    @if($item->exists)
                        <div>
                            <div style="font-size:13px;color:#384451;margin-bottom:3px">بارکد:</div>
                            <div style="border:1px solid #b9bfc6;border-radius:3px;padding:12px;display:flex;flex-direction:column;align-items:center;background:#fff">
                                <svg id="item-barcode" data-code="{{ $item->code }}"></svg>
                            </div>
                            <button type="button" onclick="printItemBarcode()" class="btn3d" style="width:100%;justify-content:center;margin-top:8px">
                                <svg class="ic ic-gray" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 8V3h10v5M5 14h10v3H5v-3zM3 8h14v5h-3M3 8v4h2"/></svg>
                                چاپ بارکد
                            </button>
                        </div>
                    @endif

                    <div>
                        <div style="font-size:13px;color:#384451;margin-bottom:3px">عکس:</div>
                        <div style="border:1px solid #b9bfc6;border-radius:3px;height:160px;display:flex;align-items:center;justify-content:center;background:#fafbfc;overflow:hidden">
                            <img x-show="previewUrl && !removePhoto" :src="previewUrl" class="max-h-full max-w-full object-contain">
                            <span x-show="!previewUrl || removePhoto" class="text-gray-400 text-sm">...</span>
                        </div>
                        <input type="file" name="photo" accept="image/*"
                            @change="removePhoto = false; previewUrl = URL.createObjectURL($event.target.files[0])"
                            class="mt-2 w-full text-sm">
                        @if($item->photo_path)
                            <label class="flex items-center gap-2 mt-2 text-sm text-red-600">
                                <input type="checkbox" name="remove_photo" value="1" x-model="removePhoto">
                                حذف عکس
                            </label>
                        @endif
                    </div>
                </div>
            </div>
        </x-ui.legacy-form>
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
