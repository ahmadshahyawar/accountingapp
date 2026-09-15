<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ورود — سیستم حسابداری</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-sky-900 text-gray-900 antialiased min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-sm">
        <h1 class="text-xl font-bold text-center mb-1">سیستم حسابداری یونیک</h1>
        <p class="text-sm text-gray-500 text-center mb-6">برای ادامه وارد شوید</p>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-300 text-red-800 text-sm rounded-md px-4 py-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">ایمیل</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">رمز عبور</label>
                <input type="password" name="password" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border">
            </div>
            <label class="flex items-center gap-1 text-sm text-gray-600 mb-6">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                مرا به خاطر بسپار
            </label>
            <button type="submit" class="w-full py-2 bg-sky-700 hover:bg-sky-800 text-white rounded-md text-sm font-medium">
                ورود
            </button>
        </form>
    </div>
</body>
</html>
