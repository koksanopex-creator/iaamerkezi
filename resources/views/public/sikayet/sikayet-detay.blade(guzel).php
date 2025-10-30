<x-guest-layout>
    <style>
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-in { animation: slideIn 0.4s ease-out; }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-fade-in { animation: fadeIn 0.5s ease-out; }
        
        .status-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .8; }
        }
        
        .gradient-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .info-card:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
    </style>

    {{-- Hero Başlık Bölümü --}}
    <div class="bg-white rounded-2xl shadow-2xl mb-8 overflow-hidden animate-slide-in border border-indigo-100">
        <div class="gradient-header px-6 md:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-start space-x-4">
                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white">Şikayet Detayları</h1>
                        <div class="flex items-center gap-3 mt-2 flex-wrap">
                            <span class="text-white/90 text-sm flex items-center bg-white/20 px-3 py-1 rounded-lg backdrop-blur-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                #{{ $sikayet->id }}
                            </span>
                            <span class="text-white/90 text-sm flex items-center bg-white/20 px-3 py-1 rounded-lg backdrop-blur-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $sikayet->takip_token ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center">
                    {!! $sikayet->musteri_durum_badge !!}
                </div>
            </div>
        </div>
        
        {{-- Durum Çubuğu --}}
        <div class="px-6 md:px-8 py-4 bg-gradient-to-r from-gray-50 to-indigo-50 border-b border-indigo-100">
            <div class="flex items-center justify-between max-w-4xl mx-auto">
                <div class="flex items-center space-x-2 {{ $sikayet->musteri_durum == 'Yeni' ? 'opacity-100' : 'opacity-50' }}">
                    <div class="w-8 h-8 rounded-full {{ $sikayet->musteri_durum == 'Yeni' ? 'bg-blue-600 status-pulse' : 'bg-gray-400' }} text-white flex items-center justify-center font-bold text-xs shadow-lg">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-700 hidden sm:inline">Yeni</span>
                </div>
                <div class="h-1 flex-1 mx-2 bg-gray-300 rounded-full overflow-hidden">
                    <div class="h-full {{ in_array($sikayet->musteri_durum, ['İşlemde', 'Kapatıldı']) ? 'bg-gradient-to-r from-blue-600 to-indigo-600 w-1/2' : 'w-0' }} transition-all duration-500"></div>
                </div>
                <div class="flex items-center space-x-2 {{ $sikayet->musteri_durum == 'İşlemde' ? 'opacity-100' : 'opacity-50' }}">
                    <div class="w-8 h-8 rounded-full {{ $sikayet->musteri_durum == 'İşlemde' ? 'bg-yellow-500 status-pulse' : 'bg-gray-400' }} text-white flex items-center justify-center font-bold text-xs shadow-lg">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-700 hidden sm:inline">İşlemde</span>
                </div>
                <div class="h-1 flex-1 mx-2 bg-gray-300 rounded-full overflow-hidden">
                    <div class="h-full {{ $sikayet->musteri_durum == 'Kapatıldı' ? 'bg-gradient-to-r from-indigo-600 to-green-600 w-full' : 'w-0' }} transition-all duration-500"></div>
                </div>
                <div class="flex items-center space-x-2 {{ $sikayet->musteri_durum == 'Kapatıldı' ? 'opacity-100' : 'opacity-50' }}">
                    <div class="w-8 h-8 rounded-full {{ $sikayet->musteri_durum == 'Kapatıldı' ? 'bg-green-600 status-pulse' : 'bg-gray-400' }} text-white flex items-center justify-center font-bold text-xs shadow-lg">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-700 hidden sm:inline">Tamamlandı</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Başarı/Hata Mesajları --}}
    @if (session('success'))
        <div class="mb-6 bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 text-green-800 p-5 rounded-xl shadow-lg animate-slide-in" role="alert">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if (session('error'))
         <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-800 p-5 rounded-xl shadow-lg animate-slide-in" role="alert">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="font-semibold">{{ session('error') }}</p>
            </div>
        </div>
    @endif
    @if ($errors->any())
         <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-800 px-5 py-4 rounded-xl shadow-lg animate-slide-in" role="alert">
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

    {{-- Şikayet Bilgileri Kartı --}}
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 border border-gray-200 mb-8 animate-fade-in">
        <div class="flex items-center space-x-3 mb-6 pb-4 border-b-2 border-indigo-200">
            <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold shadow-lg">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800">Şikayet Bilgileri</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="info-card bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200">
                <div class="flex items-start space-x-3">
                    <div class="p-2 bg-blue-100 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 font-medium mb-1">Müşteri Adı</p>
                        <p class="text-sm font-bold text-gray-800">{{ $sikayet->musteri_adi }}</p>
                    </div>
                </div>
            </div>

            <div class="info-card bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-xl border border-purple-200">
                <div class="flex items-start space-x-3">
                    <div class="p-2 bg-purple-100 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 font-medium mb-1">E-posta</p>
                        <p class="text-sm font-bold text-gray-800 break-all">{{ $sikayet->musteri_iletisim }}</p>
                    </div>
                </div>
            </div>

            <div class="info-card bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-xl border border-green-200">
                <div class="flex items-start space-x-3">
                    <div class="p-2 bg-green-100 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 font-medium mb-1">Konum</p>
                        <p class="text-sm font-bold text-gray-800">{{ $sikayet->konum_tipi == 'Yurt İçi' ? '🇹🇷' : '🌍' }} {{ $sikayet->konum_tipi }}</p>
                    </div>
                </div>
            </div>

            <div class="info-card bg-gradient-to-br from-orange-50 to-red-50 p-4 rounded-xl border border-orange-200">
                <div class="flex items-start space-x-3">
                    <div class="p-2 bg-orange-100 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 font-medium mb-1">Şikayet Tarihi</p>
                        <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)->format('d.m.Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="info-card bg-gradient-to-br from-yellow-50 to-amber-50 p-4 rounded-xl border border-yellow-200 md:col-span-2">
                <div class="flex items-start space-x-3">
                    <div class="p-2 bg-yellow-100 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 font-medium mb-1">Kategori</p>
                        <p class="text-sm font-bold text-gray-800">{{ $sikayet->sikayetKategori->ad ?? 'Belirtilmemiş' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-5 rounded-xl border border-indigo-200">
                <div class="flex items-start space-x-3 mb-3">
                    <div class="p-2 bg-indigo-100 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-600 font-medium mb-2">Şikayet Konusu</p>
                        <p class="text-base font-bold text-gray-800">{{ $sikayet->musteri_sikayet_konusu }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-5 rounded-xl border-2 border-gray-200">
                <div class="flex items-start space-x-3 mb-3">
                    <div class="p-2 bg-gray-200 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-600 font-medium mb-3">Detaylı Açıklama</p>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $sikayet->musteri_sikayet_detayi }}</p>
                    </div>
                </div>
            </div>

            {{-- Dosyalar --}}
            @if($sikayet->dosyalar && $sikayet->dosyalar->count() > 0)
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-5 rounded-xl border border-blue-200">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-gray-800">Eklenen Dosyalar ({{ $sikayet->dosyalar->count() }})</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($sikayet->dosyalar as $dosya)
                    <a href="{{ asset('storage/' . $dosya->dosya_yolu) }}" target="_blank" 
                       class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-blue-200 hover:border-blue-400 hover:shadow-md transition-all duration-200 group">
                        <div class="p-2 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                            @if(str_contains($dosya->mime_tipi, 'image'))
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                </svg>
                            @elseif(str_contains($dosya->mime_tipi, 'pdf'))
                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate group-hover:text-blue-600">{{ $dosya->orijinal_adi }}</p>
                            <p class="text-xs text-gray-500">{{ $dosya->mime_tipi }}</p>
                        </div>
                        <svg class="w-5 h-5 text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Durum Aksiyonları --}}
    
    {{-- 1. Düzenleme Butonu (Yeni ve Kilitli Değilse) --}}
    @if(is_null($sikayet->edit_locked_at) && $sikayet->musteri_durum == 'Yeni')
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl shadow-lg p-6 md:p-8 border-2 border-blue-200 mb-8 animate-fade-in">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-start space-x-4">
                    <div class="p-3 bg-blue-600 rounded-xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Düzenleme Yapabilirsiniz</h3>
                        <p class="text-sm text-gray-600">Şikayetiniz henüz işleme alınmadı. İsterseniz bilgilerinizi güncelleyebilirsiniz.</p>
                    </div>
                </div>
                <a href="{{ route('public.sikayet.edit', ['token' => $sikayet->takip_token]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-xl font-bold text-sm text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Düzenle
                </a>
            </div>
        </div>
    @endif

    {{-- 2. Proje Takip (Takım Atanmışsa) --}}
    @if($sikayet->atanan_cozum_takimi_id && $sikayet->musteri_durum != 'Yeni')
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl shadow-lg p-6 md:p-8 border-2 border-indigo-200 mb-8 animate-fade-in">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-start space-x-4">
                    <div class="p-3 bg-indigo-600 rounded-xl shadow-lg status-pulse">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Çözüm Ekibine İletildi</h3>
                        <p class="text-sm text-gray-600">Şikayetiniz ilgili ekibimiz tarafından değerlendiriliyor. Proje ilerlemesini takip edebilirsiniz.</p>
                    </div>
                </div>
                <a href="#" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-xl font-bold text-sm text-white opacity-50 cursor-not-allowed shadow-lg whitespace-nowrap" 
                   disabled>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Proje Takibi (Yakında)
                </a>
            </div>
        </div>
    @elseif($sikayet->musteri_durum == 'İşlemde')
        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-2xl shadow-lg p-6 md:p-8 border-2 border-yellow-300 mb-8 animate-fade-in">
            <div class="flex items-start space-x-4">
                <div class="p-3 bg-yellow-500 rounded-xl shadow-lg status-pulse">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Şikayetiniz İnceleniyor</h3>
                    <p class="text-sm text-gray-600">Ekibimiz şikayetinizi değerlendiriyor ve en kısa sürede size geri dönüş yapacak.</p>
                </div>
            </div>
        </div>
    @elseif($sikayet->musteri_durum == 'Yeni' && !is_null($sikayet->edit_locked_at))
        <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl shadow-lg p-6 md:p-8 border-2 border-gray-300 mb-8 animate-fade-in">
            <div class="flex items-start space-x-4">
                <div class="p-3 bg-gray-500 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">İşleme Alındı</h3>
                    <p class="text-sm text-gray-600">Şikayetiniz değerlendirme için sistemimize kaydedildi ve kilitlendi.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- 3. Geri Bildirim Formu (Kapatıldı Durumunda) --}}
    @if($sikayet->musteri_durum == 'Kapatıldı')
        <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 border-2 border-green-200 mb-8 animate-fade-in">
            <div class="flex items-center space-x-3 mb-6 pb-4 border-b-2 border-green-200">
                <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-bold shadow-lg">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Çözüm Değerlendirmesi</h2>
            </div>

            @if($sikayet->musteri_feedback)
                {{-- Geri bildirim verilmişse --}}
                <div class="p-6 rounded-xl border-2 {{ $sikayet->musteri_feedback == 'Onaylandı' ? 'bg-gradient-to-br from-green-50 to-emerald-50 border-green-300' : ($sikayet->musteri_feedback == 'Reddedildi' ? 'bg-gradient-to-br from-red-50 to-rose-50 border-red-300' : 'bg-gradient-to-br from-yellow-50 to-amber-50 border-yellow-300') }}">
                    <div class="flex items-start space-x-4">
                        <div class="p-3 rounded-xl {{ $sikayet->musteri_feedback == 'Onaylandı' ? 'bg-green-600' : ($sikayet->musteri_feedback == 'Reddedildi' ? 'bg-red-600' : 'bg-yellow-500') }} shadow-lg">
                            @if($sikayet->musteri_feedback == 'Onaylandı')
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($sikayet->musteri_feedback == 'Reddedildi')
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold {{ $sikayet->musteri_feedback == 'Onaylandı' ? 'text-green-800' : ($sikayet->musteri_feedback == 'Reddedildi' ? 'text-red-800' : 'text-yellow-800') }} mb-2">
                                Geri Bildiriminiz: {{ $sikayet->musteri_feedback }}
                            </h3>
                            @if($sikayet->musteri_feedback_note)
                            <div class="bg-white/60 backdrop-blur-sm p-4 rounded-lg mt-3 border {{ $sikayet->musteri_feedback == 'Onaylandı' ? 'border-green-200' : ($sikayet->musteri_feedback == 'Reddedildi' ? 'border-red-200' : 'border-yellow-200') }}">
                                <p class="text-xs font-semibold text-gray-600 mb-1">Notunuz:</p>
                                <p class="text-sm text-gray-700 italic">{{ $sikayet->musteri_feedback_note }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                {{-- Geri bildirim formu --}}
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200 mb-6">
                    <div class="flex items-start space-x-3 mb-4">
                        <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h3 class="text-base font-bold text-gray-800 mb-1">Şikayetiniz Çözümlendi!</h3>
                            <p class="text-sm text-gray-600">Lütfen çözümümüzü değerlendirin ve geri bildirimde bulunun.</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('public.sikayet.storeFeedback', ['token' => $sikayet->takip_token]) }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="feedback_note" class="flex items-center font-semibold text-sm text-gray-700 mb-3">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            Yorumunuz (Opsiyonel)
                        </label>
                        <textarea name="feedback_note" id="feedback_note" rows="4" 
                                  class="block w-full border-2 border-gray-300 rounded-xl shadow-sm focus:ring-green-500 focus:border-green-500 transition-all duration-200 pl-4 pr-4 py-3 text-gray-900 resize-y hover:border-gray-400" 
                                  placeholder="Çözüm hakkındaki düşüncelerinizi paylaşın...">{{ old('feedback_note') }}</textarea>
                        @error('feedback_note') 
                            <span class="text-red-600 text-xs mt-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-3">Çözümü Nasıl Değerlendiriyorsunuz?</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <button type="submit" name="feedback" value="Onaylandı" 
                                    class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-green-500 to-emerald-600 border-2 border-green-600 rounded-xl font-bold text-sm text-white hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-4 focus:ring-green-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200 group">
                                <svg class="w-12 h-12 mb-3 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-lg">Onayla</span>
                                <span class="text-xs opacity-90 mt-1">Memnunum</span>
                            </button>

                            <button type="submit" name="feedback" value="Revizyon İstendi" 
                                    class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-yellow-500 to-orange-500 border-2 border-yellow-600 rounded-xl font-bold text-sm text-white hover:from-yellow-600 hover:to-orange-600 focus:outline-none focus:ring-4 focus:ring-yellow-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200 group">
                                <svg class="w-12 h-12 mb-3 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-lg">Revizyon</span>
                                <span class="text-xs opacity-90 mt-1">İyileştirme</span>
                            </button>

                            <button type="submit" name="feedback" value="Reddedildi" 
                                    class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-red-500 to-rose-600 border-2 border-red-600 rounded-xl font-bold text-sm text-white hover:from-red-600 hover:to-rose-700 focus:outline-none focus:ring-4 focus:ring-red-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200 group">
                                <svg class="w-12 h-12 mb-3 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-lg">Reddet</span>
                                <span class="text-xs opacity-90 mt-1">Memnun Değilim</span>
                            </button>
                        </div>
                        @error('feedback') 
                            <span class="text-red-600 text-xs mt-2 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </form>
            @endif
        </div>
    @endif

    {{-- Alt Navigasyon --}}
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-8 border-t-2 border-gray-200">
        <a href="{{ url('/') }}" 
           class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 rounded-xl font-semibold text-sm text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm hover:shadow">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Ana Sayfaya Dön
        </a>

        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <span>Yardıma mı ihtiyacınız var? <a href="#" class="text-indigo-600 hover:text-indigo-800 font-semibold">Destek</a></span>
        </div>
    </div>

    {{-- Bilgilendirme Kartları --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
        <div class="bg-white rounded-xl p-5 shadow-lg border border-blue-100 hover:shadow-xl transition-shadow">
            <div class="flex items-start space-x-3">
                <div class="p-2 bg-blue-100 rounded-lg flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-gray-800 mb-1">Takip Numaranız</h3>
                    <p class="text-xs text-gray-600">Bu numarayı kaydederek şikayetinizi takip edebilirsiniz.</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-lg border border-green-100 hover:shadow-xl transition-shadow">
            <div class="flex items-start space-x-3">
                <div class="p-2 bg-green-100 rounded-lg flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-gray-800 mb-1">E-posta Bildirimleri</h3>
                    <p class="text-xs text-gray-600">Her değişiklikten e-posta ile haberdar olacaksınız.</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-lg border border-purple-100 hover:shadow-xl transition-shadow">
            <div class="flex items-start space-x-3">
                <div class="p-2 bg-purple-100 rounded-lg flex-shrink-0">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-gray-800 mb-1">Güvenli & Gizli</h3>
                    <p class="text-xs text-gray-600">Bilgileriniz güvenle saklanır ve korunur.</p>
                </div>
            </div>
        </div>
    </div>

</x-guest-layout>