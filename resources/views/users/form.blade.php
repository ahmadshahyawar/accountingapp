<x-layouts.app title="کاربر">
    <x-ui.page-header :title="$user->exists ? 'ویرایش کاربر' : 'کاربر جدید'" :back-route="route('users.index')" />

    <form action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 max-w-lg">
        @csrf
        @if($user->exists) @method('PUT') @endif

        <x-ui.field label="نام" name="name" :value="$user->name" required />
        <x-ui.field label="ایمیل" name="email" type="email" :value="$user->email" required />
        <x-ui.field :label="$user->exists ? 'رمز عبور جدید (اختیاری)' : 'رمز عبور'" name="password" type="password" :required="! $user->exists" />
        <x-ui.field label="نقش" name="role" type="select" required
            :options="['user' => 'کاربر', 'admin' => 'مدیر سیستم']" :value="$user->role ?: 'user'" />

        @if($user->exists)
            <x-ui.field label="فعال" name="is_active" type="checkbox" :value="$user->is_active" />
        @endif

        <button type="submit" class="px-6 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">ذخیره</button>
    </form>
</x-layouts.app>
