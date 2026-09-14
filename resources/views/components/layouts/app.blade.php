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
        <header class="bg-sky-900 text-white px-6 py-3 flex items-center justify-between shadow">
            <h1 class="text-lg font-bold">سیستم حسابداری</h1>
            <nav class="flex gap-4 text-sm">
                <a href="{{ route('dashboard') }}" class="hover:underline">داشبورد</a>
            </nav>
        </header>

        <main class="flex-1 p-6">
            {{ $slot }}
        </main>

        <footer class="px-6 py-2 text-xs text-gray-500 border-t bg-white">
            &copy; {{ date('Y') }} — نسخه در حال توسعه
        </footer>
    </div>
</body>
</html>
