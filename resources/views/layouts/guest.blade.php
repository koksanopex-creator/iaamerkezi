<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Köksan Portal') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

@props(['wrapperClass' => 'max-w-sm lg:w-96'])

<body class="h-full">
    <div class="min-h-screen flex flex-col lg:flex-row">

        {{-- SOL TARAF (RESİM & MARKA ALANI) - DESKTOP --}}
        <div class="hidden lg:flex w-1/2 relative bg-gray-900 overflow-hidden text-white">
            {{-- Arka Plan --}}
            <div class="absolute inset-0 bg-cover bg-center opacity-40 mix-blend-overlay"
                style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=2301&auto=format&fit=crop');">
            </div>
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 to-slate-900/90"></div>

            {{-- İçerik --}}
            <div class="relative z-10 w-full flex flex-col justify-between p-16">
                <div>
                    {{-- Logo --}}
                    <div class="flex justify-center w-full mb-8">
                        <a href="{{ url('/') }}"
                            class="bg-white/10 p-6 rounded-3xl inline-block backdrop-blur-sm border border-white/20 hover:bg-white/20 transition-all duration-300 shadow-xl">
                            <img src="{{ asset('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png') }}"
                                alt="Köksan Logo" class="h-20 w-auto brightness-0 invert">
                        </a>
                    </div>

                    <h1 class="text-5xl font-extrabold leading-tight tracking-tight mb-6 text-center">
                        Kurumsal <br>
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-emerald-300">Yönetim
                            Sistemi</span>
                    </h1>

                    <p class="text-lg text-blue-100 max-w-lg leading-relaxed font-light text-center mx-auto">
                        Müşteri şikayetleri, iyileştirmeye açık alanlar, disiplin süreçleri ve öneri sistemi ile
                        şirketimizin kalitesini birlikte yükseltiyoruz.
                    </p>
                </div>

                <div class="flex gap-4 text-sm text-blue-200/60 font-medium">
                    <span>© {{ date('Y') }} Köksan A.Ş.</span>
                </div>
            </div>

            {{-- Dekoratif --}}
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl"></div>
        </div>

        {{-- MOBİL HEAD (Responsive Görünüm) --}}
        <div
            class="lg:hidden bg-gray-900 text-white p-6 relative overflow-hidden flex flex-col items-center justify-center text-center">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 to-slate-900/90 z-0"></div>
            <div class="relative z-10 w-full">
                <a href="{{ url('/') }}" class="inline-block mb-4">
                    <img src="{{ asset('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png') }}"
                        alt="Köksan Logo" class="h-16 w-auto brightness-0 invert drop-shadow-lg">
                </a>
                <h2 class="text-2xl font-bold">Kurumsal Yönetim Sistemi</h2>
                <p class="text-sm text-blue-100 mt-2 opacity-80">Müşteri şikayetleri, disiplin ve öneri süreçleri.</p>
            </div>
        </div>

        {{-- SAĞ TARAF (FORM ALANI) --}}
        <div
            class="flex-1 flex flex-col justify-center py-10 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white relative w-full lg:w-1/2">
            <div class="mx-auto w-full {{ $wrapperClass }}">
                {{ $slot }}
            </div>
        </div>

    </div>

    {{-- Scripts --}}

</body>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')

</html>