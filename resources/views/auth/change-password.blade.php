<x-layouts.app title="تغییر رمز عبور">
    <form action="{{ route('profile.password.update') }}" method="POST">
        @csrf
        @method('PUT')

        <x-ui.legacy-form title="تغییر رمز عبور" :back-route="route('dashboard')">
            <x-ui.field label="رمز عبور فعلی" name="current_password" type="password" required />
            <x-ui.field label="رمز عبور جدید" name="password" type="password" required />
            <x-ui.field label="تکرار رمز عبور جدید" name="password_confirmation" type="password" required />
        </x-ui.legacy-form>
    </form>
</x-layouts.app>
