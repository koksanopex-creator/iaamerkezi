<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        window.notificationApiUrls = {
            index: '{{ route("notifications.index") }}',
            unreadCount: '{{ route("notifications.unreadCount") }}',
            markAsRead: '{{ route("notifications.markAsRead") }}'
        };
    </script>

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>

<body class="font-sans antialiased bg-gray-50">

    <div class="min-h-screen">
        {{-- 1. ÜST MENÜ (Navigation) --}}
        @include('layouts.navigation')

        {{-- 2. SAYFA BAŞLIĞI (Opsiyonel Header) --}}
        @isset($header)
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- 3. ANA İÇERİK --}}
        <main>
            <div class="py-8">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </div>
        </main>

        {{-- 4. GLOBAL FOOTER --}}
        @include('layouts.footer')
    </div>

    @stack('scripts')

    @php
        $appUrl = config('app.url');
        $isLocal = str_contains($appUrl, 'localhost:8000') || app()->isLocal();
        $livewireUpdateUri = $isLocal ? '/livewire/update' : asset('livewire/update');
    @endphp

    <script>
        window.livewire_app_url = '{{ $appUrl }}';
        window.livewire_update_uri = '{{ $livewireUpdateUri }}';
    </script>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>
    @auth
        <livewire:global-chat-bot />
    @endauth
</body>

</html>