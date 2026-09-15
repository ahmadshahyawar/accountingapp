<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'حسابداری' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col">
        <header class="bg-sky-900 text-white px-6 py-3 flex items-center justify-between shadow flex-wrap gap-2">
            <h1 class="text-lg font-bold">سیستم حسابداری</h1>
            <nav class="flex gap-4 text-sm flex-wrap">
                <a href="{{ route('dashboard') }}" class="hover:underline">داشبورد</a>
                <a href="{{ route('sales-invoices.index') }}" class="hover:underline">فروش</a>
                <a href="{{ route('purchase-invoices.index') }}" class="hover:underline">خرید</a>
                <a href="{{ route('cash-vouchers.index') }}" class="hover:underline">صندوق</a>
                <a href="{{ route('persons.index') }}" class="hover:underline">اشخاص</a>
                <a href="{{ route('items.index') }}" class="hover:underline">اجناس</a>
                <a href="{{ route('accounts.index') }}" class="hover:underline">حساب ها</a>
                <a href="{{ route('reports.trial-balance') }}" class="hover:underline">گزارشات</a>
                <a href="{{ route('settings.index') }}" class="hover:underline">تنظیمات</a>
            </nav>
        </header>

        <main class="flex-1 p-6">
            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-300 text-emerald-800 text-sm rounded-md px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-300 text-red-800 text-sm rounded-md px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>

        <footer class="px-6 py-2 text-xs text-gray-500 border-t bg-white">
            &copy; {{ date('Y') }} — نسخه در حال توسعه
        </footer>
    </div>
</body>
</html>
