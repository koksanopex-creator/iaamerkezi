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

    <title>@stack('pageTitle'){{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- EKLEME 1: Livewire stillerini buraya ekledik --}}
    @livewireStyles

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">
</head>

<body class="font-sans antialiased bg-gray-50">
    {{-- Mevcut yapı korundu: Gözlemci/Gölgeleme barı --}}
    @include('layouts.partials.shadow-bar')

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

        {{-- GLOBAL PROFILE WARNING --}}
        @auth
            @php
                $user = Auth::user();
                $hideWarning = $user->hasRole('Superadmin') || !empty($user->customer_id);
                $missingInfo = !$user->dogum_tarihi || !$user->telefon || !$user->profile_photo_path;
            @endphp
            @if(!$hideWarning && $missingInfo && !session()->has('dismiss_profile_warning'))
                <div x-data="{ show: true }" x-show="show" x-transition 
                     class="bg-rose-600 border-b border-rose-700 py-3 px-4 sm:px-6 lg:px-8 text-white relative z-40">
                    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="flex p-2 rounded-lg bg-rose-500 shadow-sm">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <p class="font-medium text-sm sm:text-base">
                                <span class="hidden md:inline">Kişisel bilgilerinizi (Doğum tarihi, telefon, fotoğraf) tamamlamak için lütfen profilinizi güncelleyiniz. Aksi takdirde her girişinizde bu hatırlatmayı göreceksiniz.</span>
                                <span class="md:hidden">Profil bilgilerinizi (doğum tarihi, telefon, vb.) güncelleyin.</span>
                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto justify-end">
                            <a href="{{ route('profile.edit', ['tab' => 'settings']) }}" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-rose-600 bg-white hover:bg-rose-50 transition-colors whitespace-nowrap">
                                Profili Güncelle
                            </a>
                            <form action="{{ route('profile.dismiss.warning') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" @click="show = false" class="p-2 ml-1 hover:bg-rose-500 rounded-lg transition-colors" title="Şimdilik yoksay">
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endauth

        {{-- 3. ANA İÇERİK --}}
        <main>
            <div class="py-8">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    @if(isset($slot))
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endif
                </div>
            </div>
        </main>

        {{-- 4. GLOBAL FOOTER --}}
        @include('layouts.footer')
    </div>

    @stack('scripts')

    {{-- EKLEME 2: Çalışmayan koddan (Kendi çalışan yapınıza döndürüldü ancak TAMAMEN DİNAMİK) --}}
    @php
        $appUrl = config('app.url');
        // APP_URL değerinden alt dizini otomatik algılar (.env'de APP_URL ne ise onu kullanır)
        // Örnek: https://kys.koksan.com/uuu ise $prefix = '/uuu' olur. 
        // localhost ise $prefix = '' olur.
        $prefix = rtrim(parse_url($appUrl, PHP_URL_PATH) ?? '', '/');
        
        $livewireUpdateUri = $prefix . '/livewire/update'; 
    @endphp

    <script>
        window.livewire_app_url = '{{ $appUrl }}';
        window.livewire_update_uri = '{{ $livewireUpdateUri }}';
    </script>

    {{-- MEVCUT YAPI KORUNDU: Livewire Manuel Config (Dinamik Subfolder için kritik) --}}
    <script>
        window.livewireScriptConfig = {
            uri: '{{ $livewireUpdateUri }}',
            asset_url: '{{ $prefix }}',
            csrf: '{{ csrf_token() }}',
            updateUri: '{{ $livewireUpdateUri }}',
            progressBar: '',
            nonce: ''
        };
    </script>

    {{-- MEVCUT YAPI KORUNDU: Livewire Script Dosyası (Dinamik) --}}
    <script src="{{ $prefix }}/vendor/livewire/livewire.js" data-navigate-once></script>

    {{-- ARAMA FONKSİYONLARI --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.Livewire) {
                Livewire.start();
            }

            const searchButton = document.getElementById('globalSearchButton');
            const searchContainer = document.getElementById('globalSearchContainer');
        });
    </script>

    {{-- MEVCUT YAPI KORUNDU: Sortable Plugin --}}
    <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>


    @auth
        {{-- EKLEME 3: Chat Bot Bileşeni --}}
        <livewire:global-chat-bot />

        {{-- Mevcut yapı korundu: Shadowing/POST koruması --}}
        @if(Auth::user()->isShadowing())
            <script>
                document.addEventListener('livewire:init', () => {
                    Livewire.hook('request', ({
                        fail
                    }) => {
                        fail(({
                            status,
                            preventDefault
                        }) => {
                            if (status === 403) {
                                preventDefault(); // 403 hatasının modal açmasını engeller
                                console.warn('Gözlemci modu: Yazma işlemi (POST) engellendi.');
                            }
                        });
                    });
                });
            </script>
        @endif
    @endauth

    {{-- Global Bildirimler --}}
    <x-flash-notifications />
</body>

</html>