<x-app-layout>
    @push('pageTitle', 'Yeni Arabuluculuk Dosyası | ')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .employee-card {
            transition: all 0.2s ease;
        }

        .employee-card:hover {
            transform: translateY(-2px);
        }

        .employee-card.selected {
            ring: 2px;
        }
    </style>

    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.arabuluculuk.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Yeni Arabuluculuk Dosyası</h2>
                <p class="text-sm text-gray-500">Dosya bilgilerini doldurarak süreci başlatın</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- HATA GÖSTERİM ALANI --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-5 flex items-start gap-4 shadow-sm">
                    <div class="bg-red-100 p-2 rounded-lg text-red-600 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-red-800 mb-1">Lütfen aşağıdaki hataları düzeltin</p>
                        <ul class="list-disc ml-5 text-sm text-red-600 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.arabuluculuk.store') }}" method="POST" enctype="multipart/form-data"
                x-data="arabuluculukForm()">
                @csrf

                @push('scripts')
                    <script>
                        function arabuluculukForm() {
                            return {
                                type: '{{ old("type", "ihtiyari") }}',
                                selectedUserId: '{{ old("calisan_user_id", "") }}',
                                selectedUserName: '',
                                selectedUserEmail: '',
                                selectedUserType: '',
                                searchQuery: '',
                                yakaTab: 'beyaz',
                                showDropdown: false,
                                init() {
                                    if (this.selectedUserId) {
                                        const el = document.querySelector('[data-user-id="' + this.selectedUserId + '"]');
                                        if (el) {
                                            this.selectedUserName = el.dataset.userName;
                                            this.selectedUserEmail = el.dataset.userEmail;
                                            this.selectedUserType = el.dataset.userType;
                                        }
                                    }
                                },
                                selectUser(id, name, email, type) {
                                    this.selectedUserId = id;
                                    this.selectedUserName = name;
                                    this.selectedUserEmail = email;
                                    this.selectedUserType = type;
                                    this.showDropdown = false;
                                    this.searchQuery = '';
                                },
                                clearUser() {
                                    this.selectedUserId = '';
                                    this.selectedUserName = '';
                                    this.selectedUserEmail = '';
                                    this.selectedUserType = '';
                                }
                            }
                        }
                    </script>
                @endpush

                <div class="space-y-6">

                    {{-- ==================== ADIM 1: SÜREÇ TÜRÜ ==================== --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                            <div class="flex items-center gap-3">
                                <div class="bg-indigo-100 p-2 rounded-xl text-indigo-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">Süreç Türü</h3>
                                    <p class="text-xs text-gray-500">Arabuluculuk sürecinin türünü belirleyin</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @can('arabuluculuk.create_ihtiyari')
                                    <label
                                        class="relative border-2 rounded-2xl p-5 cursor-pointer transition-all group employee-card"
                                        :class="type === 'ihtiyari' ? 'border-green-500 bg-green-50/70 shadow-md shadow-green-100' : 'border-gray-200 hover:border-green-300 hover:bg-green-50/30'">
                                        <div class="flex items-start gap-4">
                                            <div class="flex items-center h-5 mt-1">
                                                <input type="radio" name="type" value="ihtiyari" x-model="type"
                                                    class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300">
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1.5">
                                                    <div class="p-1.5 rounded-lg"
                                                        :class="type === 'ihtiyari' ? 'bg-green-200 text-green-700' : 'bg-gray-100 text-gray-400'">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                    </div>
                                                    <span class="font-bold text-gray-900 text-base">İhtiyari
                                                        (Gönüllü)</span>
                                                </div>
                                                <p class="text-sm text-gray-500 leading-relaxed">Personel birimi yönetir.
                                                    Taraflar gönüllü olarak masaya oturur.</p>
                                            </div>
                                        </div>
                                        <div x-show="type === 'ihtiyari'" class="absolute top-3 right-3" x-transition>
                                            <span
                                                class="flex h-6 w-6 items-center justify-center rounded-full bg-green-500 text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </span>
                                        </div>
                                    </label>
                                @endcan

                                @can('arabuluculuk.create_zorunlu')
                                    <label
                                        class="relative border-2 rounded-2xl p-5 cursor-pointer transition-all group employee-card"
                                        :class="type === 'zorunlu' ? 'border-red-500 bg-red-50/70 shadow-md shadow-red-100' : 'border-gray-200 hover:border-red-300 hover:bg-red-50/30'">
                                        <div class="flex items-start gap-4">
                                            <div class="flex items-center h-5 mt-1">
                                                <input type="radio" name="type" value="zorunlu" x-model="type"
                                                    class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300">
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1.5">
                                                    <div class="p-1.5 rounded-lg"
                                                        :class="type === 'zorunlu' ? 'bg-red-200 text-red-700' : 'bg-gray-100 text-gray-400'">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                                        </svg>
                                                    </div>
                                                    <span class="font-bold text-gray-900 text-base">Zorunlu (Dava
                                                        Şartı)</span>
                                                </div>
                                                <p class="text-sm text-gray-500 leading-relaxed">Hukuk birimi yönetir.
                                                    Mahkeme öncesi zorunlu adımdır.</p>
                                            </div>
                                        </div>
                                        <div x-show="type === 'zorunlu'" class="absolute top-3 right-3" x-transition>
                                            <span
                                                class="flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </span>
                                        </div>
                                    </label>
                                @endcan

                                @if(!auth()->user()->can('arabuluculuk.create_ihtiyari') && !auth()->user()->can('arabuluculuk.create_zorunlu'))
                                    <div
                                        class="col-span-2 text-center text-red-500 font-bold p-6 bg-red-50 rounded-2xl border border-red-200">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-red-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        Dosya oluşturma yetkiniz bulunmamaktadır.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ==================== ADIM 2: ÇALIŞAN SEÇİMİ ==================== --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-100 p-2 rounded-xl text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">İlgili Çalışan <span
                                            class="text-red-500">*</span></h3>
                                    <p class="text-xs text-gray-500">Arabuluculuk sürecindeki çalışanı seçin</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <input type="hidden" name="calisan_user_id" :value="selectedUserId">

                            {{-- Seçili Kullanıcı Kartı --}}
                            <div x-show="selectedUserId" x-transition class="mb-4">
                                <div
                                    class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg"
                                            :class="selectedUserType === 'mavi' ? 'bg-blue-200 text-blue-800' : 'bg-indigo-200 text-indigo-800'">
                                            <span
                                                x-text="selectedUserName ? selectedUserName.substring(0, 2).toUpperCase() : ''"></span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900" x-text="selectedUserName"></div>
                                            <div class="text-sm text-gray-500" x-text="selectedUserEmail"></div>
                                        </div>
                                        <span class="ml-2 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                            :class="selectedUserType === 'mavi' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-indigo-100 text-indigo-700 border border-indigo-200'"
                                            x-text="selectedUserType === 'mavi' ? 'Mavi Yaka' : 'Beyaz Yaka'"></span>
                                    </div>
                                    <button type="button" @click="clearUser()"
                                        class="text-gray-400 hover:text-red-600 transition p-2 hover:bg-red-50 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Çalışan Seçici --}}
                            <div x-show="!selectedUserId" x-transition>
                                {{-- Sekmeler --}}
                                <div class="flex bg-gray-100 rounded-xl p-1 gap-1 mb-4">
                                    <button type="button" @click="yakaTab = 'beyaz'"
                                        :class="yakaTab === 'beyaz' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 px-4 py-2.5 rounded-lg text-sm transition flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Beyaz Yaka
                                        <span
                                            class="bg-indigo-100 text-indigo-600 text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $users->where('is_mavi_yaka', false)->count() }}</span>
                                    </button>
                                    <button type="button" @click="yakaTab = 'mavi'"
                                        :class="yakaTab === 'mavi' ? 'bg-white text-blue-700 shadow-sm font-bold' : 'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 px-4 py-2.5 rounded-lg text-sm transition flex items-center justify-center gap-2">
                                        <span
                                            class="inline-flex items-center justify-center w-5 h-5 bg-blue-500 rounded-full text-white text-[9px] font-black">MY</span>
                                        Mavi Yaka
                                        <span
                                            class="bg-blue-100 text-blue-600 text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $users->where('is_mavi_yaka', true)->count() }}</span>
                                    </button>
                                </div>

                                {{-- Arama --}}
                                <div class="relative mb-4">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" x-model="searchQuery"
                                        class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        placeholder="İsim veya e-posta ile arayın...">
                                    <button type="button" x-show="searchQuery" @click="searchQuery = ''"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Beyaz Yaka Listesi --}}
                                <div x-show="yakaTab === 'beyaz'"
                                    class="max-h-72 overflow-y-auto rounded-xl border border-gray-200 divide-y divide-gray-100">
                                    @forelse($users->where('is_mavi_yaka', false) as $user)
                                        <div class="flex items-center gap-3 px-4 py-3 hover:bg-indigo-50/50 cursor-pointer transition group"
                                            data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}"
                                            data-user-email="{{ $user->email }}" data-user-type="beyaz"
                                            x-show="!searchQuery || '{{ strtolower($user->name) }} {{ strtolower($user->email) }}'.includes(searchQuery.toLowerCase())"
                                            @click="selectUser('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->email }}', 'beyaz')">
                                            <div
                                                class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm flex-shrink-0 group-hover:bg-indigo-200 transition">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div
                                                    class="font-semibold text-gray-800 text-sm truncate group-hover:text-indigo-700 transition">
                                                    {{ $user->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 truncate">{{ $user->email }}</div>
                                            </div>
                                            @if($user->unvan)
                                                <span
                                                    class="text-[10px] text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full flex-shrink-0">{{ $user->unvan }}</span>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-center py-8 text-gray-400 text-sm">Beyaz yaka personel bulunamadı.
                                        </div>
                                    @endforelse
                                </div>

                                {{-- Mavi Yaka Listesi --}}
                                <div x-show="yakaTab === 'mavi'" style="display:none;"
                                    class="max-h-72 overflow-y-auto rounded-xl border border-blue-200 divide-y divide-blue-50">
                                    @forelse($users->where('is_mavi_yaka', true) as $user)
                                        <div class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50/50 cursor-pointer transition group"
                                            data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}"
                                            data-user-email="{{ $user->email }}" data-user-type="mavi"
                                            x-show="!searchQuery || '{{ strtolower($user->name) }} {{ strtolower($user->email) }}'.includes(searchQuery.toLowerCase())"
                                            @click="selectUser('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->email }}', 'mavi')">
                                            <div
                                                class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm flex-shrink-0 group-hover:bg-blue-200 transition">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div
                                                    class="font-semibold text-gray-800 text-sm truncate group-hover:text-blue-700 transition">
                                                    {{ $user->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 truncate">{{ $user->email }}</div>
                                            </div>
                                            @if($user->unvan)
                                                <span
                                                    class="text-[10px] text-blue-500 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100 flex-shrink-0">{{ $user->unvan }}</span>
                                            @endif
                                            @if($user->sicil_no)
                                                <span
                                                    class="text-[10px] text-gray-400 flex-shrink-0">{{ $user->sicil_no }}</span>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                                            <span class="text-3xl mb-2">👷</span>
                                            <p class="text-sm">Mavi yaka personel bulunamadı.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ==================== ADIM 3: TALEP TUTARI ==================== --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                            <div class="flex items-center gap-3">
                                <div class="bg-emerald-100 p-2 rounded-xl text-emerald-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">Talep Edilen Tutar</h3>
                                    <p class="text-xs text-gray-500">Çalışanın talep ettiği tutarı girin (opsiyonel)</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="relative max-w-md">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-emerald-500 font-bold text-lg">₺</span>
                                </div>
                                <input type="number" step="0.01" name="talep_tutari" value="{{ old('talep_tutari') }}"
                                    class="w-full pl-10 pr-16 py-3 bg-gray-50 border border-gray-200 rounded-xl text-lg font-semibold text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                                    placeholder="0.00">
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <span class="text-gray-400 text-sm font-medium">TL</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ==================== ADIM 4: ZORUNLU DETAYLARI ==================== --}}
                    @can('arabuluculuk.create_zorunlu')
                        <div x-show="type === 'zorunlu'" x-transition x-cloak>
                            <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
                                <div class="px-6 py-4 border-b border-red-100 bg-gradient-to-r from-red-50 to-orange-50">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-red-100 p-2 rounded-xl text-red-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-800">Resmi Dosya Bilgileri</h3>
                                            <p class="text-xs text-gray-500">Zorunlu arabuluculuk süreci için ek bilgiler
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {{-- Dosya No --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Dosya No <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                                    </svg>
                                                </div>
                                                <input type="text" name="dosya_no" value="{{ old('dosya_no') }}"
                                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                                    placeholder="Örn: 2024/123">
                                            </div>
                                        </div>

                                        {{-- Arabulucu --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Atanan Arabulucu
                                                <span class="text-red-500">*</span></label>
                                            <select name="arabulucu_id"
                                                class="w-full py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                                                <option value="">Seçiniz...</option>
                                                @foreach($arabulucular as $arabulucu)
                                                    <option value="{{ $arabulucu->id }}" {{ old('arabulucu_id') == $arabulucu->id ? 'selected' : '' }}>{{ $arabulucu->name }} ({{ $arabulucu->sehir }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- İç Avukat --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Şirket İçi
                                                Avukat</label>
                                            <select name="internal_lawyer_id"
                                                class="w-full py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                                                <option value="">Seçiniz...</option>
                                                @foreach($internalLawyers as $lawyer)
                                                    <option value="{{ $lawyer->id }}" {{ old('internal_lawyer_id') == $lawyer->id ? 'selected' : '' }}>{{ $lawyer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Dış Avukat --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Dış Avukat
                                                (Opsiyonel)</label>
                                            <select name="external_lawyer_id"
                                                class="w-full py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                                                <option value="">Atanmayacak</option>
                                                @foreach($externalLawyers as $lawyer)
                                                    <option value="{{ $lawyer->id }}" {{ old('external_lawyer_id') == $lawyer->id ? 'selected' : '' }}>{{ $lawyer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan

                </div>

                {{-- BUTONLAR --}}
                <div class="mt-8 flex items-center justify-between">
                    <a href="{{ route('admin.arabuluculuk.index') }}"
                        class="flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 rounded-xl font-semibold text-gray-600 hover:bg-gray-50 hover:text-gray-800 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        İptal
                    </a>
                    <button type="submit"
                        class="flex items-center gap-2 px-8 py-3 rounded-xl font-bold text-white shadow-lg transition-all hover:shadow-xl active:scale-[0.98]"
                        :class="type === 'zorunlu' ? 'bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800' : 'bg-gradient-to-r from-green-600 to-emerald-700 hover:from-green-700 hover:to-emerald-800'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span x-text="type === 'ihtiyari' ? 'İhtiyari Dosya Oluştur' : 'Zorunlu Dosya Başlat'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>