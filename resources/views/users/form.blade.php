<x-layouts.app title="کاربر">
    <form action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" method="POST">
        @csrf
        @if($user->exists) @method('PUT') @endif

        <x-ui.legacy-form :title="$user->exists ? 'ویرایش کاربر' : 'کاربر جدید'" :back-route="route('users.index')">
            <x-ui.field label="نام" name="name" :value="$user->name" required />
            <x-ui.field label="ایمیل" name="email" type="email" :value="$user->email" required />
            <x-ui.field :label="$user->exists ? 'رمز عبور جدید (اختیاری)' : 'رمز عبور'" name="password" type="password" :required="! $user->exists" />
            <x-ui.field label="نقش" name="role" type="select" required
                :options="['user' => 'کاربر', 'admin' => 'مدیر سیستم']" :value="$user->role ?: 'user'" />

            @if($user->exists)
                <div class="legacy-field flex items-center gap-2">
                    <x-ui.field label="فعال" name="is_active" type="checkbox" :value="$user->is_active" />
                </div>
            @endif
        </x-ui.legacy-form>
    </form>
</x-layouts.app>
