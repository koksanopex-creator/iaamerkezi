<x-guest-layout>
    <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-down { animation: slideDown 0.3s ease-out; }
        
        .gradient-header {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        }
        
        .step-indicator {
            background: linear-gradient(to right, #dc2626 0%, #ef4444 50%, #dc2626 100%);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        input:focus, select:focus, textarea:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
        
        .form-card {
            background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
        }
        
        .icon-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .7; }
        }
    </style>

    {{-- Hero Başlık Bölümü --}}
    <div class="bg-white rounded-2xl shadow-2xl mb-8 overflow-hidden animate-slide-down border border-red-100">
        <div class="gradient-header px-6 md:px-8 py-8">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-start space-x-4">
                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl shadow-lg icon-pulse">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white">Şikayet Bildirim Formu</h1>
                        <p class="text-red-50 text-sm mt-1.5 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Sorununuzu bildirin • Anlık takip edin • Hızlı çözüm alın
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm px-4 py-2.5 rounded-lg shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-white text-sm font-semibold">~2 dakika</span>
                </div>
            </div>
        </div>
        
        {{-- İlerleme Adımları --}}
        <div class="px-6 md:px-8 py-5 bg-gradient-to-r from-gray-50 to-red-50 border-b border-red-100">
            <div class="grid grid-cols-3 gap-4 max-w-3xl mx-auto">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-sm shadow-lg step-indicator">1</div>
                    <span class="text-xs md:text-sm font-semibold text-gray-700">Bilgileriniz</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-sm shadow-lg">2</div>
                    <span class="text-xs md:text-sm font-semibold text-gray-700">Şikayet Detayı</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-sm shadow-lg">3</div>
                    <span class="text-xs md:text-sm font-semibold text-gray-700">Kanıtlar</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Başarı/Hata Mesajları --}}
    @if (session('success'))
        <div class="mb-6 bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 text-green-800 p-5 rounded-xl shadow-lg animate-slide-down" role="alert">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if (session('error'))
         <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-800 p-5 rounded-xl shadow-lg animate-slide-down" role="alert">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="font-semibold">{{ session('error') }}</p>
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-800 px-5 py-4 rounded-xl shadow-lg animate-slide-down" role="alert">
            <div class="flex items-start">
                <svg class="w-6 h-6 mr-3 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <strong class="font-bold block mb-2">Lütfen aşağıdaki hataları düzeltin:</strong>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Ana Form Kartı --}}
    <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 border border-red-100">
        <form action="{{ route('public.sikayet.store') }}" method="POST" enctype="multipart/form-data" x-data="fileUploadComponent()">
            @csrf
            
            {{-- ADIM 1: KİŞİSEL BİLGİLER --}}
            <div class="mb-10">
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b-2 border-red-200">
                    <div class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center font-bold shadow-lg">1</div>
                    <h2 class="text-xl font-bold text-gray-800">Kişisel Bilgileriniz</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Müşteri Adı --}}
                    <div class="group">
                        <label for="musteri_adi" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                            Adınız Soyadınız <span class="ml-1 text-red-600">*</span>
                        </label>
                        <input type="text" name="musteri_adi" id="musteri_adi" value="{{ old('musteri_adi', auth()->user()?->name) }}" required class="block w-full border-2 border-gray-300 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 transition-all duration-200 pl-4 pr-4 py-3 text-gray-900 hover:border-gray-400" placeholder="Örn: Ahmet Yılmaz">
                        @error('musteri_adi') <span class="text-red-600 text-xs mt-1 flex items-center"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                    </div>

                    {{-- E-posta --}}
                    <div class="group">
                        <label for="musteri_iletisim" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                             <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                            E-posta Adresiniz <span class="ml-1 text-red-600">*</span>
                        </label>
                        <input type="email" name="musteri_iletisim" id="musteri_iletisim" value="{{ old('musteri_iletisim', auth()->user()?->email) }}" required class="block w-full border-2 border-gray-300 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 transition-all duration-200 pl-4 pr-4 py-3 text-gray-900 hover:border-gray-400" placeholder="ornek@email.com">
                        <p class="mt-2 text-xs text-gray-500 flex items-center">
                            <svg class="w-3 h-3 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                            Takip linki ve şifreniz bu adrese gönderilecektir
                        </p>
                        @error('musteri_iletisim') <span class="text-red-600 text-xs mt-1 flex items-center"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                    </div>

                    {{-- Konum Tipi --}}
                    <div class="md:col-span-2">
                        <label class="flex items-center font-semibold text-sm text-gray-700 mb-3">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Şikayetin Konumu <span class="text-red-600 ml-1">*</span>
                        </label>
                        <div class="flex items-center space-x-6">
                            <label class="inline-flex items-center cursor-pointer px-6 py-3 border-2 border-gray-300 rounded-xl hover:border-red-500 hover:bg-red-50 transition-all duration-200 group">
                                <input type="radio" name="konum_tipi" value="Yurt İçi" class="form-radio text-red-600 focus:ring-red-500 w-5 h-5" {{ old('konum_tipi', 'Yurt İçi') == 'Yurt İçi' ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-semibold text-gray-700 group-hover:text-red-700">🇹🇷 Yurt İçi</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer px-6 py-3 border-2 border-gray-300 rounded-xl hover:border-red-500 hover:bg-red-50 transition-all duration-200 group">
                                <input type="radio" name="konum_tipi" value="Yurt Dışı" class="form-radio text-red-600 focus:ring-red-500 w-5 h-5" {{ old('konum_tipi') == 'Yurt Dışı' ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-semibold text-gray-700 group-hover:text-red-700">🌍 Yurt Dışı</span>
                            </label>
                        </div>
                        @error('konum_tipi') <span class="text-red-600 text-xs mt-2 flex items-center"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- ADIM 2: ŞİKAYET DETAYI --}}
            <div class="mb-10">
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b-2 border-red-200">
                    <div class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center font-bold shadow-lg">2</div>
                    <h2 class="text-xl font-bold text-gray-800">Şikayet Detayları</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Kategori --}}
                    <div class="group">
                        <label for="sikayet_kategorisi_id" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                            Şikayet Kategorisi <span class="ml-1 text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <select name="sikayet_kategorisi_id" id="sikayet_kategorisi_id" required
                                    class="block w-full border-2 border-gray-300 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 transition-all duration-200 pl-4 pr-10 py-3 text-gray-900 appearance-none bg-white hover:border-gray-400">
                                <option value="">-- Kategori Seçiniz --</option>
                                @isset($kategoriler)
                                    @foreach($kategoriler as $kategori)
                                        <option value="{{ $kategori->id }}" {{ old('sikayet_kategorisi_id') == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->ad }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"><svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></div>
                        </div>
                        @error('sikayet_kategorisi_id') <span class="text-red-600 text-xs mt-1 flex items-center"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                    </div>

                    {{-- Tarih --}}
                    <div class="group">
                        <label for="musteri_sikayet_tarihi" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                             <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                            Olayın Tarihi <span class="ml-1 text-red-600">*</span>
                        </label>
                        <input type="date" name="musteri_sikayet_tarihi" id="musteri_sikayet_tarihi" value="{{ old('musteri_sikayet_tarihi', date('Y-m-d')) }}" required class="block w-full border-2 border-gray-300 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 transition-all duration-200 pl-4 pr-4 py-3 text-gray-900 hover:border-gray-400">
                        @error('musteri_sikayet_tarihi') <span class="text-red-600 text-xs mt-1 flex items-center"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                    </div>

                    {{-- Konu --}}
                    <div class="md:col-span-2">
                        <label for="musteri_sikayet_konusu" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                            Şikayet Konusu <span class="ml-1 text-red-600">*</span>
                        </label>
                        <input type="text" name="musteri_sikayet_konusu" id="musteri_sikayet_konusu" value="{{ old('musteri_sikayet_konusu') }}" required class="block w-full border-2 border-gray-300 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 transition-all duration-200 pl-4 pr-4 py-3 text-gray-900 hover:border-gray-400" placeholder="Örn: Geç teslimat, hasarlı ürün, yanlış faturalama...">
                        @error('musteri_sikayet_konusu') <span class="text-red-600 text-xs mt-1 flex items-center"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                    </div>

                    {{-- Detay --}}
                    <div class="md:col-span-2">
                        <label for="musteri_sikayet_detayi" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                             <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                            Detaylı Açıklama <span class="ml-1 text-red-600">*</span>
                        </label>
                        <textarea name="musteri_sikayet_detayi" id="musteri_sikayet_detayi" rows="6" required class="block w-full border-2 border-gray-300 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 transition-all duration-200 pl-4 pr-4 py-3 text-gray-900 resize-y hover:border-gray-400" placeholder="Lütfen yaşadığınız sorunu tüm detaylarıyla açıklayınız. Ne oldu? Ne zaman oldu? Nasıl etkilendiniz?">{{ old('musteri_sikayet_detayi') }}</textarea>
                        <p class="mt-2 text-xs text-gray-500 flex items-center">
                            <svg class="w-3 h-3 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                            Ne kadar detaylı açıklarsanız, çözüm sürecimiz o kadar hızlı olur
                        </p>
                        @error('musteri_sikayet_detayi') <span class="text-red-600 text-xs mt-1 flex items-center"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- ADIM 3: KANITLAR --}}
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b-2 border-red-200">
                    <div class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center font-bold shadow-lg">3</div>
                    <h2 class="text-xl font-bold text-gray-800">Kanıtlarınız (Opsiyonel)</h2>
                </div>
                
                <div>
                    <label for="dosyalar" class="flex items-center font-semibold text-sm text-gray-700 mb-3">
                         <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                        Fotoğraf, Video, Belge Ekleyin
                    </label>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-red-500 transition-all duration-200 bg-gradient-to-br from-gray-50 to-red-50">
                        <input
                            type="file"
                            name="dosyalar[]"
                            id="dosyalar"
                            multiple
                            accept="image/*,video/mp4,application/pdf,.doc,.docx,.xls,.xlsx"
                            capture="environment"
                            @change="updatePreviews($event)"
                            x-ref="fileInput"
                            class="hidden">
                        
                        <label for="dosyalar" class="cursor-pointer block">
                            <div class="flex flex-col items-center space-y-3">
                                <div class="p-4 bg-red-100 rounded-full">
                                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-base font-semibold text-gray-700">Dosya seçmek için tıklayın</p>
                                    <p class="text-sm text-gray-500 mt-1">veya sürükleyip bırakın</p>
                                </div>
                                <div class="flex flex-wrap justify-center gap-2 mt-3">
                                    <span class="px-3 py-1 bg-white rounded-full text-xs font-medium text-gray-600 border border-gray-300">📷 Fotoğraf</span>
                                    <span class="px-3 py-1 bg-white rounded-full text-xs font-medium text-gray-600 border border-gray-300">🎥 Video</span>
                                    <span class="px-3 py-1 bg-white rounded-full text-xs font-medium text-gray-600 border border-gray-300">📄 PDF</span>
                                    <span class="px-3 py-1 bg-white rounded-full text-xs font-medium text-gray-600 border border-gray-300">📝 Word</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    
                    <p class="mt-3 text-xs text-gray-500 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        Her dosya maksimum 10MB olabilir • Mobil kameradan çekim yapabilirsiniz
                    </p>
                    @error('dosyalar.*') <span class="text-red-600 text-xs mt-2 flex items-center justify-center"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror

                    {{-- Önizleme Alanı --}}
                    <div x-show="previews.length > 0" x-transition class="mt-6">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-semibold text-gray-700">Yüklenen Dosyalar (<span x-text="previews.length"></span>)</p>
                            <button @click.prevent="previews = []; files = []; $refs.fileInput.value = ''" class="text-xs text-red-600 hover:text-red-800 font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                Tümünü Sil
                            </button>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            <template x-for="(preview, index) in previews" :key="index">
                                <div class="relative group bg-white rounded-xl overflow-hidden border-2 border-gray-200 hover:border-red-500 transition-all duration-200 shadow-md hover:shadow-xl">
                                    <template x-if="preview.url">
                                        <img :src="preview.url" class="object-cover h-32 w-full" alt="Önizleme">
                                    </template>
                                    <template x-if="!preview.url">
                                         <div class="flex flex-col items-center justify-center h-32 bg-gradient-to-br from-gray-100 to-gray-200 p-3">
                                            <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0011.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            <p class="text-xs text-gray-600 font-medium text-center truncate w-full px-2" x-text="preview.name"></p>
                                        </div>
                                    </template>
                                    <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/70 to-transparent">
                                        <p class="text-xs text-white font-medium truncate" x-text="preview.name"></p>
                                    </div>
                                    <button @click.prevent="removePreview(index)" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all duration-200 hover:bg-red-600 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-red-400 shadow-lg">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Aksiyonları --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-8 border-t-2 border-gray-200">
                <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 rounded-xl font-semibold text-sm text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm hover:shadow">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    İptal Et
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-xl font-bold text-sm text-white hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                     <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Şikayeti Gönder
                </button>
            </div>
        </form>
    </div>

    {{-- Bilgilendirme Kartları --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
        <div class="bg-white rounded-xl p-5 shadow-lg border border-blue-100">
            <div class="flex items-start space-x-3">
                <div class="p-2 bg-blue-100 rounded-lg flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-gray-800 mb-1">E-posta Bildirimi</h3>
                    <p class="text-xs text-gray-600">Şikayetiniz kaydedildikten sonra takip linki ve şifreniz e-postanıza gönderilecektir.</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-lg border border-green-100">
            <div class="flex items-start space-x-3">
                <div class="p-2 bg-green-100 rounded-lg flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-gray-800 mb-1">Hızlı Yanıt</h3>
                    <p class="text-xs text-gray-600">Şikayetiniz en kısa sürede değerlendirilecek ve size geri dönüş yapılacaktır.</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-lg border border-purple-100">
            <div class="flex items-start space-x-3">
                <div class="p-2 bg-purple-100 rounded-lg flex-shrink-0">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-gray-800 mb-1">Güvenli & Gizli</h3>
                    <p class="text-xs text-gray-600">Tüm bilgileriniz güvenli bir şekilde saklanır ve gizliliğiniz korunur.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine.js Component --}}
    <script>
        function fileUploadComponent() {
            return {
                previews: [], 
                files: [],
                updatePreviews(event) {
                    let selectedFiles = Array.from(event.target.files);
                    this.files = this.files.concat(selectedFiles);
                    selectedFiles.forEach(file => {
                        let reader = new FileReader();
                        reader.onload = (e) => {
                            let preview = {
                                url: file.type.startsWith('image/') ? e.target.result : null,
                                name: file.name
                            };
                            this.previews.push(preview);
                        };
                        reader.readAsDataURL(file);
                    });
                    const dataTransfer = new DataTransfer();
                    this.files.forEach(file => dataTransfer.items.add(file));
                    this.$refs.fileInput.files = dataTransfer.files;
                },
                removePreview(index) {
                    this.previews.splice(index, 1);
                    this.files.splice(index, 1);
                    const dataTransfer = new DataTransfer();
                    this.files.forEach(file => dataTransfer.items.add(file));
                    this.$refs.fileInput.files = dataTransfer.files;
                }
            }
        }
    </script>
</x-guest-layout>