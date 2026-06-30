@push('pageTitle')
    Giriş Hareketleri | 
@endpush

<x-app-layout>
    <style>
        @keyframes pulse-glow {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            50% {
                transform: scale(1.2);
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }
        }
        .pulse-green {
            animation: pulse-glow 2s infinite ease-in-out;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px -4px rgba(99, 102, 241, 0.12);
            border-color: rgba(99, 102, 241, 0.25);
        }
        .table-row-hover {
            transition: all 0.2s ease;
        }
        .table-row-hover:hover {
            background-color: rgba(248, 250, 252, 0.8);
            transform: scale(1.002);
        }
        .form-select-custom {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
    </style>

    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <nav class="flex text-xs text-slate-400 mb-1.5" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1">
                        <li class="inline-flex items-center">
                            <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors font-medium">Dashboard</a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-slate-300 mx-1" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                                <span class="text-slate-500 font-semibold">Giriş Logları</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h2 class="font-black text-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 bg-clip-text text-transparent leading-none py-1">
                    {{ __('Kullanıcı Giriş & Aktivite Analizi') }}
                </h2>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl hover:text-indigo-600 hover:border-indigo-200 hover:shadow-md transition-all font-bold text-xs gap-2 shadow-sm">
                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Dashboard'a Dön
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Stat 1: Toplam Kişi -->
                <div class="glass-card rounded-2xl p-6 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Aktif Kullanıcılar</span>
                        <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-slate-800 tracking-tight">{{ $stats['total_users'] }}</span>
                        <span class="text-xs text-slate-400 font-semibold">toplam kişi</span>
                    </div>
                </div>

                <!-- Stat 2: Bugün -->
                <div class="glass-card rounded-2xl p-6 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Bugün Giriş Yapanlar</span>
                        <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-slate-800 tracking-tight">{{ $stats['today_active'] }}</span>
                        <span class="text-xs text-emerald-600 font-extrabold bg-emerald-50/70 border border-emerald-100/50 px-2 py-0.5 rounded-full">Bugün</span>
                    </div>
                </div>

                <!-- Stat 3: Çevrimiçi -->
                <div class="glass-card rounded-2xl p-6 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-teal-500/10 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Şu An Çevrimiçi</span>
                        <div class="p-1.5 bg-teal-50 text-teal-600 rounded-xl flex items-center gap-1.5 border border-teal-100/50">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 pulse-green"></span>
                            <span class="text-[9px] font-black tracking-widest uppercase">AKTİF</span>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-slate-800 tracking-tight">{{ $stats['online_now'] }}</span>
                        <span class="text-xs text-slate-400 font-semibold">kullanıcı aktif</span>
                    </div>
                </div>

                <!-- Stat 4: En Çok Vakit Geçiren -->
                <div class="glass-card rounded-2xl p-6 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-amber-500/10 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400 font-bold text-amber-800">Zirvedekiler</span>
                        <div class="p-2.5 bg-amber-50 text-amber-600 rounded-xl border border-amber-100/50 animate-bounce">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                    </div>
                    @if($stats['most_online_user'])
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-800 truncate" title="{{ $stats['most_online_user']->name }}">
                                {{ $stats['most_online_user']->name }}
                            </span>
                            <span class="text-xs text-amber-700 font-extrabold mt-0.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg>
                                @php
                                    $mMinutes = $stats['most_online_user']->total_online_minutes;
                                @endphp
                                @if($mMinutes >= 60)
                                    {{ floor($mMinutes / 60) }} sa {{ $mMinutes % 60 }} dk
                                @else
                                    {{ $mMinutes }} dk
                                @endif
                            </span>
                        </div>
                    @else
                        <span class="text-sm text-slate-400 italic">Kayıt bulunamadı</span>
                    @endif
                </div>
            </div>

            <!-- Main Panel -->
            <div class="bg-white overflow-hidden shadow-xl shadow-slate-100/50 rounded-3xl border border-slate-100/80 mb-8">
                <div class="p-6 sm:p-8">
                    
                    @php
                        $getHeaderUrl = function($column, $defaultDir = 'asc') use ($sortBy, $sortDir) {
                            $dir = ($sortBy === $column) ? ($sortDir === 'asc' ? 'desc' : 'asc') : $defaultDir;
                            return route('logs.login.index', array_merge(request()->except(['page']), [
                                'sort_by' => $column,
                                'sort_dir' => $dir,
                                'sort' => null
                            ]));
                        };
                    @endphp

                    <!-- Tabs Section -->
                    <div class="flex border-b border-slate-100 mb-6 gap-2">
                        <a href="{{ route('logs.login.index', ['tab' => 'personel']) }}" 
                           class="px-5 py-3 text-xs font-black uppercase tracking-wider border-b-2 transition-all flex items-center gap-2 {{ $tab !== 'musteri' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/10' : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-200' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Şirket Personelleri
                        </a>
                        <a href="{{ route('logs.login.index', ['tab' => 'musteri']) }}" 
                           class="px-5 py-3 text-xs font-black uppercase tracking-wider border-b-2 transition-all flex items-center gap-2 {{ $tab === 'musteri' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/10' : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-200' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Müşteriler
                        </a>
                    </div>

                    <!-- Filters section -->
                    <form action="{{ route('logs.login.index') }}" method="GET" class="mb-8 flex flex-col lg:flex-row gap-4 items-end bg-slate-50/50 p-6 rounded-2xl border border-slate-100">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <input type="hidden" name="sort_by" id="form_sort_by" value="{{ $sortBy }}">
                        <input type="hidden" name="sort_dir" id="form_sort_dir" value="{{ $sortDir }}">
                        
                        <!-- Search Bar -->
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Kullanıcı Ara</label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ $search }}"
                                    placeholder="İsim veya e-posta ile ara..."
                                    class="w-full pl-11 pr-11 py-3 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm text-sm text-slate-800 placeholder-slate-400">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                @if($search)
                                    <a href="{{ route('logs.login.index', array_merge(request()->except('search'))) }}"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Department or Company Filter -->
                        @if($tab === 'musteri')
                            <div class="w-full lg:w-64">
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Firma</label>
                                <select name="customer_id" onchange="this.form.submit()" class="w-full border border-slate-200 rounded-xl py-3 px-3.5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-sm text-sm text-slate-700 font-bold form-select-custom">
                                    <option value="">Tüm Firmalar</option>
                                    @foreach($firmalar as $firma)
                                        <option value="{{ $firma->id }}" {{ $customerId == $firma->id ? 'selected' : '' }}>
                                            {{ $firma->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="w-full lg:w-64">
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Bölüm</label>
                                <select name="bolum_id" onchange="this.form.submit()" class="w-full border border-slate-200 rounded-xl py-3 px-3.5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-sm text-sm text-slate-700 font-bold form-select-custom">
                                    <option value="">Tüm Bölümler</option>
                                    @foreach($bolumler as $bolum)
                                        <option value="{{ $bolum->id }}" {{ $bolumId == $bolum->id ? 'selected' : '' }}>
                                            {{ $bolum->ad }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Sorting Filter -->
                        <div class="w-full lg:w-72">
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Sıralama Seçeneği</label>
                            <select name="sort" onchange="document.getElementById('form_sort_by').value=''; document.getElementById('form_sort_dir').value=''; this.form.submit();" class="w-full border border-slate-200 rounded-xl py-3 px-3.5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-sm text-sm text-slate-700 font-bold form-select-custom">
                                <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>İsim (A - Z)</option>
                                <option value="latest_login" {{ $sort === 'latest_login' ? 'selected' : '' }}>En Son Giriş Yapanlar</option>
                                <option value="most_logins" {{ $sort === 'most_logins' ? 'selected' : '' }}>En Sık Giriş Yapanlar</option>
                                <option value="longest_online" {{ $sort === 'longest_online' ? 'selected' : '' }}>En Uzun Kalanlar (Toplam Süre)</option>
                                @if($sort === 'custom')
                                    <option value="custom" selected>Özel Sıralama Aktif</option>
                                @endif
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        @php
                            $hasActiveFilters = $search || ($tab === 'musteri' ? $customerId : $bolumId) || $sortBy !== 'name' || $sortDir !== 'asc';
                        @endphp
                        @if($hasActiveFilters)
                            <div class="w-full lg:w-auto">
                                <a href="{{ route('logs.login.index', ['clear_filter' => 1]) }}" class="w-full inline-flex items-center justify-center px-5 py-3 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl font-bold text-xs gap-2 transition-all shadow-sm">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Sıfırla
                                </a>
                            </div>
                        @endif
                    </form>

                    <!-- Active Filter Alert Banner -->
                    @if($hasActiveFilters)
                        <div class="mb-6 p-4 bg-indigo-50/60 border border-indigo-100/80 rounded-2xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm backdrop-blur-md">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-100 text-indigo-600 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">Aktif Filtreler Uygulanıyor</h4>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        @if($search)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-bold bg-white text-indigo-700 border border-indigo-100">
                                                Arama: "{{ $search }}"
                                            </span>
                                        @endif
                                        @if($tab === 'musteri' && $customerId)
                                            @php
                                                $selectedFirma = $firmalar->firstWhere('id', $customerId);
                                            @endphp
                                            @if($selectedFirma)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-bold bg-white text-indigo-700 border border-indigo-100">
                                                    Firma: {{ $selectedFirma->name }}
                                                </span>
                                            @endif
                                        @elseif($tab !== 'musteri' && $bolumId)
                                            @php
                                                $selectedBolum = $bolumler->firstWhere('id', $bolumId);
                                            @endphp
                                            @if($selectedBolum)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-bold bg-white text-indigo-700 border border-indigo-100">
                                                    Bölüm: {{ $selectedBolum->ad }}
                                                </span>
                                            @endif
                                        @endif
                                        @if($sortBy !== 'name' || $sortDir !== 'asc')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-bold bg-white text-indigo-700 border border-indigo-100">
                                                Sıralama: 
                                                @if($sortBy === 'name') Kullanıcı @endif
                                                @if($sortBy === 'bolum_firma') @if($tab === 'musteri') Firma @else Bölüm @endif @endif
                                                @if($sortBy === 'durum') Durum @endif
                                                @if($sortBy === 'son_giris') Son Giriş @endif
                                                @if($sortBy === 'son_sure') Seans Süresi @endif
                                                @if($sortBy === 'toplam_giris') Toplam Giriş @endif
                                                @if($sortBy === 'toplam_sure') Toplam Süre @endif
                                                @if($sortBy === 'ip') IP Adresi @endif
                                                ({{ $sortDir === 'asc' ? 'A-Z / Artan' : 'Z-A / Azalan' }})
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('logs.login.index', ['clear_filter' => 1]) }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs gap-1.5 transition-all shadow-md shadow-indigo-200 self-start sm:self-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Filtreleri Temizle
                            </a>
                        </div>
                    @endif

                    <!-- Table content -->
                    @php
                        $renderHeader = function($column, $label) use ($sortBy, $sortDir, $getHeaderUrl) {
                            $isActive = $sortBy === $column;
                            $url = $getHeaderUrl($column);
                            $icon = '';
                            if ($isActive) {
                                $icon = $sortDir === 'asc' 
                                    ? '<svg class="w-3.5 h-3.5 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>'
                                    : '<svg class="w-3.5 h-3.5 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>';
                            } else {
                                $icon = '<svg class="w-3.5 h-3.5 text-slate-300 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 10l5-5 5 5M7 14l5 5 5-5"/></svg>';
                            }
                            return '<a href="' . $url . '" class="group inline-flex items-center gap-1 hover:text-indigo-600 transition-colors select-none">' . e($label) . $icon . '</a>';
                        };
                    @endphp

                    <!-- Table content (Desktop only) -->
                    <div class="hidden md:block overflow-hidden rounded-2xl border border-slate-100 shadow-sm bg-white mb-6">
                        <table class="w-full text-sm text-left text-slate-500">
                            <thead class="text-[10px] text-slate-400 uppercase tracking-widest bg-slate-50/80 backdrop-blur-sm border-b border-slate-100">
                                <tr>
                                    <th class="px-3 py-3 font-black">{!! $renderHeader('name', 'Kullanıcı') !!}</th>
                                    <th class="px-3 py-3 font-black">{!! $renderHeader('bolum_firma', $tab === 'musteri' ? 'Firma' : 'Bölüm') !!}</th>
                                    <th class="px-3 py-3 font-black">{!! $renderHeader('durum', 'Durum') !!}</th>
                                    <th class="px-3 py-3 font-black">{!! $renderHeader('son_giris', 'Son Giriş / Aktivite') !!}</th>
                                    <th class="px-3 py-3 font-black">{!! $renderHeader('son_sure', 'Son Seans Süresi') !!}</th>
                                    <th class="px-3 py-3 font-black">{!! $renderHeader('toplam_giris', 'Toplam Giriş & Süre') !!}</th>
                                    <th class="px-3 py-3 font-black">{!! $renderHeader('ip', 'IP Adresi') !!}</th>
                                    <th class="px-3 py-3 text-right font-black">İşlem</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($users as $user)
                                    @php
                                        $isOnline = $user->last_activity_at && \Carbon\Carbon::parse($user->last_activity_at)->isAfter(now()->subMinutes(5));
                                    @endphp
                                    <tr class="bg-white table-row-hover transition-all">
                                        <!-- Kullanıcı Bilgisi -->
                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="relative flex-shrink-0">
                                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-sm shadow-md border border-white">
                                                        {{ mb_substr($user->name, 0, 1, 'UTF-8') }}
                                                    </div>
                                                    <!-- Çevrimiçi Glow Dot -->
                                                    @if($isOnline)
                                                        <span class="absolute bottom-0 right-0 block h-3.5 w-3.5 rounded-full bg-emerald-500 border-2 border-white pulse-green" title="Çevrimiçi"></span>
                                                    @else
                                                        <span class="absolute bottom-0 right-0 block h-3.5 w-3.5 rounded-full bg-slate-300 border-2 border-white" title="Çevrimdışı"></span>
                                                    @endif
                                                </div>
                                                <div class="flex flex-col min-w-0">
                                                    <span class="text-sm font-bold text-slate-800 leading-normal truncate" title="{{ $user->name }}">{{ $user->name }}</span>
                                                    <span class="text-[11px] text-slate-400 font-medium leading-none truncate" title="{{ $user->email }}">{{ $user->email }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Bölüm / Firma -->
                                        <td class="px-3 py-3">
                                            @if($tab === 'musteri')
                                                <span class="inline-block px-2 py-1 rounded-lg text-xs font-bold bg-amber-50/50 border border-amber-100/30 text-amber-700 max-w-[140px] break-words text-center" title="{{ $user->firma_adlari }}">
                                                    {{ $user->firma_adlari ?: '-' }}
                                                </span>
                                            @else
                                                <span class="inline-block px-2 py-1 rounded-lg text-xs font-bold bg-indigo-50/50 border border-indigo-100/30 text-indigo-700 max-w-[140px] break-words text-center" title="{{ $user->bolum->ad ?? '-' }}">
                                                    {{ $user->bolum->ad ?? '-' }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Çevrimiçi Durum Badge -->
                                        <td class="px-3 py-3">
                                            @if($isOnline)
                                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Çevrimiçi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[10px] font-black bg-slate-50 text-slate-400 border border-slate-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                    Çevrimdışı
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Son Giriş ve Aktivite Saatleri -->
                                        <td class="px-3 py-3">
                                            @if($user->last_login_at)
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-bold text-slate-700">
                                                        {{ \Carbon\Carbon::parse($user->last_login_at)->translatedFormat('d M H:i') }}
                                                    </span>
                                                    @if($user->last_activity_at)
                                                        <span class="text-[10px] text-slate-400 font-semibold">
                                                            Son İşlem: {{ \Carbon\Carbon::parse($user->last_activity_at)->translatedFormat('H:i') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400 italic">Kayıt yok</span>
                                            @endif
                                        </td>

                                        <!-- Son Seans Süresi -->
                                        <td class="px-3 py-3">
                                            @if($user->last_login_at)
                                                @php 
                                                    $diff = $user->last_login_at && $user->last_activity_at 
                                                        ? \Carbon\Carbon::parse($user->last_login_at)->diffInMinutes(\Carbon\Carbon::parse($user->last_activity_at)) 
                                                        : null;
                                                @endphp
                                                @if(!is_null($diff))
                                                    <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $diff > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-100/50' : 'bg-slate-50 text-slate-400 border border-slate-100' }}">
                                                        @if($diff >= 60)
                                                            {{ floor($diff / 60) }} sa {{ $diff % 60 }} dk
                                                        @elseif($diff > 0)
                                                            {{ $diff }} dk
                                                        @else
                                                            < 1 dk
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-xs text-slate-300 italic">-</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <!-- Toplam Giriş & Toplam Süre -->
                                        <td class="px-3 py-3">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-black text-slate-700">
                                                    {{ $user->login_activities_count }} Giriş
                                                </span>
                                                <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">
                                                    Süre: 
                                                    @if($user->total_online_minutes >= 60)
                                                        {{ floor($user->total_online_minutes / 60) }} sa {{ $user->total_online_minutes % 60 }} dk
                                                    @else
                                                        {{ $user->total_online_minutes }} dk
                                                    @endif
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Son IP -->
                                        <td class="px-3 py-3">
                                            @if($user->last_ip)
                                                <span class="text-xs font-mono text-slate-500 bg-slate-50 border border-slate-100 px-2 py-0.5 rounded-md">
                                                    {{ $user->last_ip }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <!-- Detay Butonu -->
                                        <td class="px-3 py-3 text-right">
                                            <a href="{{ route('logs.login.show', $user->id) }}"
                                                class="inline-flex items-center px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl hover:scale-[1.03] active:scale-[0.98] transition-all shadow-md shadow-indigo-200">
                                                Geçmişi Gör
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-3 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center gap-3">
                                                <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300">
                                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <span class="text-slate-400 font-bold text-sm">Filtrelere Uygun Kullanıcı Bulunamadı.</span>
                                                <span class="text-slate-300 text-xs font-semibold">Farklı bir arama terimi veya bölüm seçmeyi deneyebilirsiniz.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Card list content (Mobile only) -->
                    <div class="block md:hidden space-y-4 mb-6">
                        @forelse($users as $user)
                            @php
                                $isOnline = $user->last_activity_at && \Carbon\Carbon::parse($user->last_activity_at)->isAfter(now()->subMinutes(5));
                            @endphp
                            <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm space-y-4">
                                <!-- User Header (Avatar + Name/Email + Status) -->
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="relative flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-sm shadow-md border border-white">
                                                {{ mb_substr($user->name, 0, 1, 'UTF-8') }}
                                            </div>
                                            <!-- Çevrimiçi Glow Dot -->
                                            @if($isOnline)
                                                <span class="absolute bottom-0 right-0 block h-3.5 w-3.5 rounded-full bg-emerald-500 border-2 border-white pulse-green" title="Çevrimiçi"></span>
                                            @else
                                                <span class="absolute bottom-0 right-0 block h-3.5 w-3.5 rounded-full bg-slate-300 border-2 border-white" title="Çevrimdışı"></span>
                                            @endif
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="text-sm font-bold text-slate-800 leading-normal truncate" title="{{ $user->name }}">{{ $user->name }}</span>
                                            <span class="text-[11px] text-slate-400 font-medium leading-none truncate" title="{{ $user->email }}">{{ $user->email }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if($isOnline)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[9px] font-black bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                Çevrimiçi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[9px] font-black bg-slate-50 text-slate-400 border border-slate-200">
                                                Çevrimdışı
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Details Section -->
                                <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-50 text-xs">
                                    <!-- Bölüm / Firma -->
                                    <div>
                                        <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">
                                            @if($tab === 'musteri') Firma @else Bölüm @endif
                                        </span>
                                        @if($tab === 'musteri')
                                            <span class="inline-block px-2.5 py-1 rounded-lg font-bold bg-amber-50/50 border border-amber-100/30 text-amber-700 text-[11px] max-w-[140px] truncate" title="{{ $user->firma_adlari }}">
                                                {{ $user->firma_adlari ?: '-' }}
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-1 rounded-lg font-bold bg-indigo-50/50 border border-indigo-100/30 text-indigo-700 text-[11px] max-w-[140px] truncate" title="{{ $user->bolum->ad ?? '-' }}">
                                                {{ $user->bolum->ad ?? '-' }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- IP Adresi -->
                                    <div>
                                        <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">IP Adresi</span>
                                        @if($user->last_ip)
                                            <span class="font-mono text-slate-500 bg-slate-50 border border-slate-100 px-2 py-0.5 rounded-md text-[11px]">
                                                {{ $user->last_ip }}
                                            </span>
                                        @else
                                            <span class="text-slate-300 font-bold">-</span>
                                        @endif
                                    </div>

                                    <!-- Son Giriş -->
                                    <div>
                                        <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Son Giriş</span>
                                        @if($user->last_login_at)
                                            <div class="flex flex-col">
                                                <span class="font-bold text-slate-700 text-[11px]">
                                                    {{ \Carbon\Carbon::parse($user->last_login_at)->translatedFormat('d M H:i') }}
                                                </span>
                                                @if($user->last_activity_at)
                                                    <span class="text-[9px] text-slate-400 font-semibold">
                                                        Son İşlem: {{ \Carbon\Carbon::parse($user->last_activity_at)->translatedFormat('H:i') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Kayıt yok</span>
                                        @endif
                                    </div>

                                    <!-- Son Seans Süresi -->
                                    <div>
                                        <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Son Seans</span>
                                        @if($user->last_login_at)
                                            @php 
                                                $diff = $user->last_login_at && $user->last_activity_at 
                                                    ? \Carbon\Carbon::parse($user->last_login_at)->diffInMinutes(\Carbon\Carbon::parse($user->last_activity_at)) 
                                                    : null;
                                            @endphp
                                            @if(!is_null($diff))
                                                <span class="inline-block px-2 py-0.5 rounded-lg font-bold text-[11px] {{ $diff > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-100/50' : 'bg-slate-50 text-slate-400 border border-slate-100' }}">
                                                    @if($diff >= 60)
                                                        {{ floor($diff / 60) }} sa {{ $diff % 60 }} dk
                                                    @elseif($diff > 0)
                                                        {{ $diff }} dk
                                                    @else
                                                        < 1 dk
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-slate-300 font-bold">-</span>
                                            @endif
                                        @else
                                            <span class="text-slate-300 font-bold">-</span>
                                        @endif
                                    </div>

                                    <!-- Toplam Giriş & Toplam Süre -->
                                    <div class="col-span-2 pt-1">
                                        <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Toplam Giriş & Süre</span>
                                        <div class="flex items-center gap-3">
                                            <span class="font-bold text-slate-700 text-[11px]">
                                                {{ $user->login_activities_count }} Giriş
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">
                                                Süre: 
                                                @if($user->total_online_minutes >= 60)
                                                    {{ floor($user->total_online_minutes / 60) }} sa {{ $user->total_online_minutes % 60 }} dk
                                                @else
                                                    {{ $user->total_online_minutes }} dk
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div class="pt-3 border-t border-slate-50">
                                    <a href="{{ route('logs.login.show', $user->id) }}"
                                        class="w-full flex items-center justify-center py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl active:scale-[0.98] transition-all shadow-md shadow-indigo-200">
                                        Geçmişi Gör
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-2xl border border-slate-100 p-8 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <span class="text-slate-400 font-bold text-sm">Filtrelere Uygun Kullanıcı Bulunamadı.</span>
                                </div>
                            </div>
                        @endforelse
                    </div>


                    <!-- Pagination -->
                    @if($users->hasPages())
                        <div class="mt-6">
                            {{ $users->links() }}
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>