@props(['list', 'selected', 'createRoute', 'createLabel' => 'شخص جدید', 'createExtra' => []])
{{--
    Matches the real app's "جستجوی حساب ها" dialog (preturn_search_dialog.png):
    a real popup with its own grid, search box, and a "شخص جدید" button that
    creates a person inline without leaving the invoice — not just a plain
    dropdown. Reuses the parent form's own Alpine scope (the {{ $list }} /
    {{ $selected }} variables already declared there), so it must be placed
    inside that form's x-data element rather than isolated.
--}}
<div x-data="{ personModalOpen: false, personModalQuery: '', personModalCreating: false, newPersonName: '', newPersonPhone: '', newPersonMobile: '' }">
    <button type="button" class="btn3d" @click="personModalOpen = true; personModalQuery = ''; personModalCreating = false">
        <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="9" cy="9" r="6"/><path d="M14 14l4 4"/></svg>
        جستجوی حساب ها
    </button>

    <div x-show="personModalOpen" x-cloak style="position:fixed;inset:0;z-index:50;background:rgba(20,26,33,.45);display:flex;align-items:center;justify-content:center" @keydown.escape.window="personModalOpen = false">
        <div style="background:#fff;border:1px solid #7fb3e0;border-radius:6px;box-shadow:0 10px 40px rgba(0,0,0,.25);width:92%;max-width:520px;max-height:80vh;display:flex;flex-direction:column" @click.outside="personModalOpen = false">
            <div style="padding:10px 16px;background:#0072C6;color:#fff;font-weight:700;border-radius:6px 6px 0 0">جستجوی حساب ها</div>

            <template x-if="!personModalCreating">
                <div style="display:flex;flex-direction:column;flex:1;min-height:0">
                    <table class="legacy-grid" style="border:none;flex:1;overflow-y:auto;display:block;max-height:340px">
                        <thead><tr><th>کد حساب</th><th>نام حساب</th></tr></thead>
                        <tbody>
                            <template x-for="(p, idx) in {{ $list }}.filter(x => !personModalQuery || x.name.toLowerCase().includes(personModalQuery.toLowerCase()))" :key="p.id">
                                <tr @click="{{ $selected }} = p.id; personModalOpen = false" style="cursor:pointer" :class="{{ $selected }} == p.id ? 'is-selected' : ''">
                                    <td x-text="idx + 1"></td>
                                    <td x-text="p.name"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div class="legacy-searchrow" style="margin:10px 16px;gap:8px">
                        <input type="text" x-model="personModalQuery" placeholder="جستجو" style="flex:1;border:none;background:transparent;padding:4px 8px;font-size:13px" autofocus>
                    </div>
                    <div class="grid-toolbar" style="margin:0 16px 12px">
                        <div class="grp">
                            @if($createRoute)
                                <button type="button" class="btn3d" @click="personModalCreating = true; newPersonName = personModalQuery">
                                    <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="8"/><path d="M10 6v8M6 10h8"/></svg>
                                    {{ $createLabel }}
                                </button>
                            @endif
                        </div>
                        <div class="grp">
                            <a href="#" class="btn3d" @click.prevent="personModalOpen = false">
                                <svg class="ic ic-red" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="9"/><path d="M7 7l6 6M13 7l-6 6"/></svg>
                                انصراف
                            </a>
                        </div>
                    </div>
                </div>
            </template>

            @if($createRoute)
            <template x-if="personModalCreating">
                <div style="padding:16px">
                    <div class="legacy-field">
                        <label>نام<span class="text-red-600">*</span></label>
                        <input type="text" x-model="newPersonName">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="legacy-field">
                            <label>موبایل</label>
                            <input type="text" x-model="newPersonMobile">
                        </div>
                        <div class="legacy-field">
                            <label>تلفن</label>
                            <input type="text" x-model="newPersonPhone">
                        </div>
                    </div>
                    <div class="grid-toolbar" style="margin:12px 0 0">
                        <div class="grp">
                            <button type="button" class="btn3d" @click="
                                if (!newPersonName) return;
                                fetch('{{ $createRoute }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || document.querySelector('input[name=_token]').value },
                                    body: JSON.stringify({ name: newPersonName, mobile: newPersonMobile, phone: newPersonPhone, ...{{ Illuminate\Support\Js::from($createExtra) }} })
                                }).then(r => r.json()).then(p => {
                                    {{ $list }}.push(p);
                                    {{ $selected }} = p.id;
                                    personModalOpen = false;
                                });
                            ">
                                <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 4h10l2 2v10H4V4z"/><path d="M6 4v4h6V4M6 12h8"/></svg>
                                ذخیره
                            </button>
                        </div>
                        <div class="grp">
                            <a href="#" class="btn3d" @click.prevent="personModalCreating = false">
                                <svg class="ic ic-red" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="9"/><path d="M7 7l6 6M13 7l-6 6"/></svg>
                                انصراف
                            </a>
                        </div>
                    </div>
                </div>
            </template>
            @endif
        </div>
    </div>
</div>
