<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ورود — سیستم حسابداری</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-gray-900 antialiased min-h-screen flex items-center justify-center" style="background:#E1E1E1">
    <div style="background:#fff;border:1px solid #c7d1db;box-shadow:0 2px 10px rgba(0,0,0,.15);padding:0;width:100%;max-width:360px">
        <div style="background:#0072C6;color:#fff;padding:10px 16px;font-size:14px;font-weight:700">سیستم حسابداری یونیک</div>
        <div style="padding:24px">
            <p class="text-sm text-gray-500 text-center mb-6">برای ادامه وارد شوید</p>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-300 text-red-800 text-sm rounded-md px-4 py-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="legacy-field">
                    <label>ایمیل</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="legacy-field">
                    <label>رمز عبور</label>
                    <input type="password" name="password" required>
                </div>
                <label class="flex items-center gap-1 text-sm text-gray-600 mb-4">
                    <input type="checkbox" name="remember" style="width:16px;height:16px">
                    مرا به خاطر بسپار
                </label>
                <button type="submit" class="btn3d" style="width:100%;justify-content:center">
                    <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M8 10h9M13 6l4 4-4 4M11 4H5a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h6"/></svg>
                    ورود
                </button>
            </form>
        </div>
    </div>
</body>
</html>
