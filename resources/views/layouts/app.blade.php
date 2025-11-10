<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        @stack('styles')
        
        {{-- 1. @vite CSS/JS yüklemesi (app.js'niz Livewire/Alpine içermemeli) --}}
        @vite(['resources/css/app.css', 'resources/js/app.js']) 
        
        {{-- 2. Livewire Stilleri (Otomatik) --}}
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- 3. @stack('scripts') (Livewire'dan önce) --}}
        @stack('scripts')

        {{-- 🚨 4. KRİTİK DÜZELTME: Livewire'a Base URL'i Bildirme --}}
        @php
            $appUrl = config('app.url'); // .env'den (Lokal: http://localhost:8000)
            $isLocal = str_contains($appUrl, 'localhost:8000') || app()->isLocal();
            
            // Lokalde: /livewire/update
            // Sunucuda: /iaa/livewire/update
            $livewireUpdateUri = $isLocal ? '/livewire/update' : asset('livewire/update');
        @endphp
        
        {{-- Livewire v3 için Ajax rotasını (URI) manuel olarak ayarla --}}
        <script>
            window.livewire_app_url = '{{ $appUrl }}';
            window.livewire_update_uri = '{{ $livewireUpdateUri }}';
        </script>
        
        {{-- 5. Livewire Scriptleri (Otomatik) --}}
        @livewireScripts
        
        {{-- 6. Sortable (Livewire'dan SONRA) --}}
        <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>
        
    </body>
</html>