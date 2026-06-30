@push('pageTitle')
    Mavi Yaka Personel | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mavi Yaka Personel') }}
        </h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb & Header --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('dashboard') }}"
                                    class="inline-flex items-center text-gray-700 hover:text-indigo-600 font-medium transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                        </path>
                                    </svg>
                                    Dashboard
                                </a>
                            </li>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-1 text-gray-500 md:ml-2 font-medium">Mavi Yaka Personel</h4>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        Mavi Yaka Personel
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">Saha personellerinin listesi ve yönetimi</p>
                </div>
                <div class="flex gap-2">
                    @if(Auth::user()->hasRole('Superadmin'))
                    <a href="{{ route('admin.mavi-yaka.download-template') }}"
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-50 border border-indigo-100 rounded-xl font-semibold text-xs text-indigo-700 uppercase tracking-widest hover:bg-indigo-100 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Taslak İndir
                    </a>
                    <a href="{{ route('admin.mavi-yaka.import') }}"
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 rounded-xl font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 active:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4-4m4 4v12"></path>
                        </svg>
                        Toplu İçe Aktar
                    </a>
                    @endif
                    <a href="{{ route('admin.mavi-yaka.export', request()->all()) }}"
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-50 border border-emerald-100 rounded-xl font-semibold text-xs text-emerald-700 uppercase tracking-widest hover:bg-emerald-100 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Excel'e Aktar
                    </a>
                    {{-- MERKEZİ SİSTEM GEÇİŞİ: Mavi Yaka ekleme Merkezden yönetiliyor.
                    <a href="{{ route('admin.mavi-yaka.create') }}"
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Yeni Personel Ekle
                    </a>
                    --}}
                    <span class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-xl font-bold text-[11px] text-gray-500 uppercase tracking-widest shadow-sm">
                        Mavi Yaka Merkezden Eklenir
                    </span>
                </div>
            </div>

            {{-- İstatistik Kartları --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5 transition-all hover:shadow-md">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Aktif Personel</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['active'] }}</h3>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5 transition-all hover:shadow-md">
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">İşten Çıkanlar</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['resigned'] }}</h3>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm animate-fade-in-up">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Modern Sekmeler (Pill/Badge Tasarımı) --}}
            <div class="mb-6 flex items-center p-1.5 bg-gray-100/80 rounded-2xl w-fit border border-gray-200 shadow-inner">
                <a href="{{ route('admin.mavi-yaka.index', ['status' => 'active']) }}" 
                   class="relative flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-bold rounded-xl transition-all duration-300 {{ $status === 'active' ? 'bg-white text-indigo-700 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-200/50' }}">
                    Aktif Personeller
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 text-xs font-bold rounded-full {{ $status === 'active' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-200 text-gray-700' }}">
                        {{ $stats['active'] ?? 0 }}
                    </span>
                </a>
                <a href="{{ route('admin.mavi-yaka.index', ['status' => 'resigned']) }}" 
                   class="relative flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-bold rounded-xl transition-all duration-300 {{ $status === 'resigned' ? 'bg-white text-orange-600 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-200/50' }}">
                    İşten Çıkanlar
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 text-xs font-bold rounded-full {{ $status === 'resigned' ? 'bg-orange-100 text-orange-800' : 'bg-gray-200 text-gray-700' }}">
                        {{ $stats['resigned'] ?? 0 }}
                    </span>
                </a>
            </div>

            {{-- Filtreler --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
                <form action="{{ route('admin.mavi-yaka.index') }}" method="GET"
                    class="flex flex-col md:flex-row gap-4 items-end">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="w-full md:w-1/3">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Arama</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                class="block w-full pl-10 px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                placeholder="İsim, TC, Sicil No...">
                        </div>
                    </div>

                    @if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi']))
                        <div class="w-full md:w-1/4">
                            <label for="bolum" class="block text-sm font-medium text-gray-700 mb-1">Bölüm</label>
                            <select name="bolum" id="bolum"
                                class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="">Tümü</option>
                                @foreach($bolumler as $bolum)
                                    <option value="{{ $bolum->id }}" {{ request('bolum') == $bolum->id ? 'selected' : '' }}>
                                        {{ $bolum->ad }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="flex gap-2">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded-xl text-sm font-medium hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors shadow-sm">
                            Filtrele
                        </button>
                        @if(request()->hasAny(['search', 'bolum']))
                            <a href="{{ route('admin.mavi-yaka.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Temizle
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tablo --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Personel</th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    T.C. / Sicil No</th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Bölüm & Unvan</th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    İşe Giriş / Çıkış</th>
                                @if(!Auth::user()->hasRole(['Yonetim', 'Yönetim']))
                                <th scope="col"
                                    class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    İşlemler</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($kullanicilar as $kisi)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                @if($kisi->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $kisi->profile_photo_path) }}" 
                                                         alt="{{ $kisi->name }}" 
                                                         class="h-10 w-10 rounded-full object-cover ring-2 ring-white shadow-sm">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm border border-indigo-200 shadow-sm">
                                                        {{ strtoupper(substr($kisi->name, 0, 2)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $kisi->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $kisi->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="text-gray-900">{{ $kisi->tc_kimlik_no }}</div>
                                        <div class="text-xs">{{ $kisi->sicil_no ?? 'Sicil No Yok' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($kisi->bolum)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $kisi->bolum->ad }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 animate-pulse border border-red-200" title="Merkez API'den gelmiş olabilir. Lütfen düzenleyerek bölüm atayınız.">
                                                ⚠️ Bölüm Seçilmemiş! (Merkezden)
                                            </span>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">{{ $kisi->unvan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                <span class="text-xs uppercase text-gray-400">Giriş:</span>
                                                <span>{{ $kisi->hire_date ? $kisi->hire_date->format('d.m.Y') : '-' }}</span>
                                            </div>
                                            @if($kisi->termination_date || $status === 'resigned')
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                                <span class="text-xs uppercase text-gray-400">Çıkış:</span>
                                                <span>{{ $kisi->termination_date ? $kisi->termination_date->format('d.m.Y') : '-' }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    @if(!Auth::user()->hasRole(['Yonetim', 'Yönetim']))
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.mavi-yaka.edit', $kisi) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-indigo-600 hover:bg-indigo-50 hover:text-indigo-900 transition-colors"
                                                title="Düzenle">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg>
                                            </a>
                                            
                                            @if($status === 'resigned')
                                                {{-- Geri Al (Restore) --}}
                                                <form action="{{ route('admin.mavi-yaka.restore', $kisi->id) }}" method="POST"
                                                    class="inline-block"
                                                    onsubmit="return confirm('Bu personeli tekrar AKTİF yapmak istediğinize emin misiniz? Personel ana listeye dönecektir.');">
                                                    @csrf
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center px-4 h-8 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition-colors text-xs font-bold shadow-sm"
                                                        title="Geri Al (Aktif Yap)">
                                                        GERİ AL
                                                    </button>
                                                </form>
                                            @else
                                                {{-- İşten Çıktı (Soft Delete) --}}
                                                <form action="{{ route('admin.mavi-yaka.resign', $kisi) }}" method="POST"
                                                    class="inline-block"
                                                    onsubmit="return confirm('Bu personeli İŞTEN ÇIKTI olarak işaretlemek istediğinize emin misiniz? Personel listeden kaldırılacaktır.');">
                                                    @csrf
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center px-2 h-8 rounded-lg text-orange-600 hover:bg-orange-50 transition-colors text-xs font-bold border border-orange-200"
                                                        title="İşten Çıktı (Pasif Yap)">
                                                        İŞTEN ÇIKTI
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.mavi-yaka.destroy', $kisi) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('Bu personeli TAMAMEN SİLMEK istediğinize emin misiniz? Bu işlem geri alınamaz!');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-900 transition-colors"
                                                    title="Tamamen Sil">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->hasRole(['Yonetim', 'Yönetim']) ? 4 : 5 }}" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            <p class="text-sm text-gray-500">Mavi yaka personel bulunamadı.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($kullanicilar->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $kullanicilar->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
</x-app-layout>