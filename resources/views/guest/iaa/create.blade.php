<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İyileştirme Önerisi - {{ config('app.name', 'KÖKSAN') }}</title>
    
    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- ALPINE.JS CDN (GARANTİ ÇÖZÜM) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f3f4f6; }
        
        /* Modern Arka Plan Animasyonu */
        .bg-animated {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Glassmorphism Kart */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Input Odaklanma Efektleri */
        .modern-input {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        .modern-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            transform: translateY(-1px);
        }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col">
    
    {{-- Hareketli Arka Plan --}}
    <div class="bg-animated opacity-20"></div>

    <div class="flex-grow container mx-auto max-w-5xl px-4 py-12 relative z-10">
        
        {{-- Header Alanı --}}
        <div class="text-center mb-10 space-y-4">
            <a href="{{ url('/') }}" class="inline-block transition-transform hover:scale-105">
                <img src="{{ asset('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png') }}" class="h-16 w-auto mx-auto drop-shadow-xl" alt="Logo">
            </a>
            <div class="space-y-2">
                <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 tracking-tight">
                    Fikrin Geleceğimiz Olsun
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Daha iyi bir çalışma ortamı için gözlemlerin ve önerilerin bizim için çok değerli.
                </p>
            </div>
        </div>

        {{-- Başarı Mesajı --}}
        @if(session('success'))
            <div class="mb-8 bg-green-50 border border-green-200 rounded-2xl p-6 shadow-sm flex items-start gap-4" role="alert">
                <div class="bg-green-100 p-2 rounded-full text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-green-900 text-lg">Öneriniz Alındı!</h3>
                    <p class="text-green-700 mt-1">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Hata Mesajı --}}
        @if ($errors->any())
            <div class="mb-8 bg-red-50 border border-red-200 rounded-2xl p-6 shadow-sm flex items-start gap-4">
                <div class="bg-red-100 p-2 rounded-full text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-red-900 text-lg">Lütfen Bilgileri Kontrol Edin</h3>
                    <ul class="mt-2 list-disc list-inside text-red-700 text-sm space-y-1">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Ana Form Kartı --}}
        <div class="glass-card rounded-3xl shadow-2xl overflow-hidden">
            
            {{-- Bilgi Notu --}}
            <div class="bg-indigo-50/50 px-6 py-4 border-b border-indigo-100 flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center text-indigo-900 font-medium text-sm">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Lütfen <span class="text-red-500 font-bold text-base">*</span> ile işaretli alanları eksiksiz doldurunuz.</span>
                </div>
                <div class="text-xs text-indigo-400 font-semibold uppercase tracking-wider">MİSAFİR GİRİŞİ</div>
            </div>

            <div class="p-6 sm:p-10">
                <form 
                    action="{{ route('guest.iaa.store') }}" 
                    method="POST" 
                    enctype="multipart/form-data"
                    x-data="{
                        showKvkk: false,
                        files: [],
                        errorMessage: '',
                        
                        handleFileSelect(e) {
                            const fileList = e.target.files;
                            this.errorMessage = '';
                            const newFiles = Array.from(fileList).filter(file => file.type.startsWith('image/'));
                            
                            if (newFiles.length === 0 && fileList.length > 0) {
                                this.errorMessage = 'Lütfen sadece resim dosyası seçin.'; return;
                            }
                            if (this.files.length + newFiles.length > 5) {
                                this.errorMessage = 'En fazla 5 resim yükleyebilirsiniz.'; return;
                            }

                            newFiles.forEach(file => {
                                if (file.size > 2 * 1024 * 1024) { 
                                    this.errorMessage = `'${file.name}' çok büyük (Max 2MB).`; return;
                                }
                                let reader = new FileReader();
                                reader.onload = (e) => {
                                    this.files.push({ preview: e.target.result, fileObject: file });
                                    this.updateInput(); 
                                };
                                reader.readAsDataURL(file);
                            });
                        },
                        removeFile(index) {
                            this.files.splice(index, 1);
                            this.updateInput();
                        },
                        updateInput() {
                            const dt = new DataTransfer();
                            this.files.forEach(f => dt.items.add(f.fileObject));
                            $refs.fileInput.files = dt.files;
                        },
                        formatSize(size) {
                            return size > 1024 * 1024 ? (size / 1048576).toFixed(1) + ' MB' : (size / 1024).toFixed(1) + ' KB';
                        }
                    }"
                >
                    @csrf
                    
                    <div class="space-y-12">
                        
                        {{-- BÖLÜM 1: KİMSİNİZ? --}}
                        <section>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl shadow-sm">1</div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">İletişim Bilgileri</h2>
                                    <p class="text-sm text-gray-500">Size geri dönüş yapabilmemiz için gereklidir.</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Ad Soyad --}}
                                <div class="group">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Adınız Soyadınız <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                        <input type="text" name="guest_name" value="{{ old('guest_name') }}" required 
                                            class="modern-input block w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 text-gray-900 placeholder-gray-400 focus:bg-white" 
                                            placeholder="Örn: Ahmet Yılmaz">
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1 ml-1">Tam adınızı giriniz.</p>
                                </div>

                                {{-- E-posta --}}
                                <div class="group">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">E-posta Adresiniz</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <input type="email" name="guest_email" value="{{ old('guest_email') }}" 
                                            class="modern-input block w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 text-gray-900 placeholder-gray-400 focus:bg-white" 
                                            placeholder="Örn: ahmet@firma.com">
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1 ml-1">Durum güncellemeleri bu adrese gönderilir.</p>
                                </div>
                            </div>
                        </section>

                        <div class="border-t border-gray-100"></div>

                        {{-- BÖLÜM 2: ÖNERİ DETAYLARI --}}
                        <section>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xl shadow-sm">2</div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">Öneri Detayları</h2>
                                    <p class="text-sm text-gray-500">Sorunu ve çözüm önerinizi detaylandırın.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                {{-- Konu --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Konu / Başlık <span class="text-red-500">*</span></label>
                                    <input type="text" name="baslik" value="{{ old('baslik') }}" required 
                                        class="modern-input block w-full px-4 py-3 rounded-xl bg-gray-50 focus:bg-white" 
                                        placeholder="Örn: Depo Aydınlatma Sistemi İyileştirmesi">
                                    <p class="text-xs text-gray-400 mt-1">Önerinizi özetleyen kısa bir başlık.</p>
                                </div>
                                {{-- İlgili Alan --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">İlgili Alan / Departman <span class="text-red-500">*</span></label>
                                    <input type="text" name="ilgili_alan" value="{{ old('ilgili_alan') }}" required 
                                        class="modern-input block w-full px-4 py-3 rounded-xl bg-gray-50 focus:bg-white" 
                                        placeholder="Örn: Lojistik, Üretim Hattı B">
                                    <p class="text-xs text-gray-400 mt-1">Bu öneri hangi bölümü veya alanı ilgilendiriyor?</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                {{-- Mevcut Durum --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Mevcut Durum / Problem <span class="text-red-500">*</span></label>
                                    <textarea name="mevcut_durum" rows="4" required 
                                        class="modern-input block w-full px-4 py-3 rounded-xl bg-gray-50 focus:bg-white resize-none" 
                                        placeholder="Örn: Şu anda depo girişindeki lambalar yetersiz olduğu için akşam vardiyasında forklift operatörleri görüş zorluğu yaşıyor...">{{ old('mevcut_durum') }}</textarea>
                                    <p class="text-xs text-gray-400 mt-1">Şu anki sorunu veya eksikliği detaylıca açıklayın.</p>
                                </div>

                                {{-- Öneri --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Çözüm Öneriniz</label>
                                    <textarea name="oneri" rows="4" 
                                        class="modern-input block w-full px-4 py-3 rounded-xl bg-gray-50 focus:bg-white resize-none" 
                                        placeholder="Örn: Mevcut halojen lambaların hareket sensörlü LED projektörlerle değiştirilmesini öneriyorum. Bu sayede hem enerji tasarrufu sağlanır hem de görüş açısı iyileşir.">{{ old('oneri') }}</textarea>
                                    <p class="text-xs text-gray-400 mt-1">Bu sorunu nasıl çözebiliriz? Fikriniz nedir?</p>
                                </div>
                            </div>

                            {{-- Finansal Tahminler --}}
                            <div class="mt-8 bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Tahmini Rakamlar (Opsiyonel)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Tahmini Kazanç</label>
                                        <div class="flex shadow-sm rounded-xl overflow-hidden">
                                            <input type="number" name="oneren_kazanc_miktar" placeholder="0" class="modern-input block w-full px-4 py-3 border-r-0 rounded-l-xl focus:z-10">
                                            <select name="oneren_kazanc_birim" class="bg-gray-100 border border-l-0 border-gray-200 text-gray-700 font-bold px-4 py-3 rounded-r-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                                <option>TL</option><option>USD</option><option>EUR</option>
                                            </select>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">Yıllık tahmini getiri veya tasarruf.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Tahmini Bütçe</label>
                                        <div class="flex shadow-sm rounded-xl overflow-hidden">
                                            <input type="number" name="oneren_butce_miktar" placeholder="0" class="modern-input block w-full px-4 py-3 border-r-0 rounded-l-xl focus:z-10">
                                            <select name="oneren_butce_birim" class="bg-gray-100 border border-l-0 border-gray-200 text-gray-700 font-bold px-4 py-3 rounded-r-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                                <option>TL</option><option>USD</option><option>EUR</option>
                                            </select>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">Bu işin yapılması için gereken tahmini maliyet.</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <div class="border-t border-gray-100"></div>

                        {{-- BÖLÜM 3: DOSYA YÜKLEME --}}
                        <section>
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xl shadow-sm">3</div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900">Görsel Yükle</h2>
                                        <p class="text-sm text-gray-500">Sorunu veya çözümü anlatan resimler.</p>
                                    </div>
                                </div>
                                <div class="hidden sm:block">
                                    <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-full border border-orange-200">
                                        <span x-text="files.length"></span> / 5 Seçildi
                                    </span>
                                </div>
                            </div>

                            {{-- Hata Uyarısı --}}
                            <div x-show="errorMessage" x-cloak class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg flex items-center animate-pulse">
                                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span x-text="errorMessage" class="text-sm font-bold text-red-800"></span>
                            </div>

                            {{-- Önizleme Alanı --}}
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mb-4" x-show="files.length > 0" x-cloak x-transition>
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="relative group aspect-square rounded-2xl overflow-hidden border border-gray-200 shadow-md bg-white">
                                        <img :src="file.preview" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <button type="button" @click="removeFile(index)" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full shadow-lg transform hover:scale-110 transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                        <div class="absolute bottom-0 inset-x-0 bg-white/90 backdrop-blur-sm text-gray-800 text-[10px] font-bold text-center py-1 border-t border-gray-100" x-text="formatSize(file.fileObject.size)"></div>
                                    </div>
                                </template>
                            </div>

                            {{-- Yükleme Alanı --}}
                            <div x-show="files.length < 5">
                                <label class="relative flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer hover:bg-indigo-50/50 hover:border-indigo-400 transition-all group">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <div class="w-14 h-14 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-indigo-100 transition-all">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        </div>
                                        <p class="mb-1 text-sm text-gray-700 font-medium"><span class="text-indigo-600 font-bold">Resim Seçin</span> veya sürükleyip bırakın</p>
                                        <p class="text-xs text-gray-400">PNG, JPG, JPEG (Maks. 2MB)</p>
                                    </div>
                                    <input type="file" x-ref="fileInput" name="resimler[]" multiple accept="image/*" @change="handleFileSelect" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </label>
                            </div>
                        </section>

                        {{-- KVKK Onayı --}}
                        <section>
                            <div class="mt-8 flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="kvkk_approval" name="kvkk_approval" type="checkbox" required
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded cursor-pointer">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="kvkk_approval" class="font-medium text-gray-700 cursor-pointer select-none">
                                        <span class="text-indigo-600 hover:text-indigo-800 underline cursor-pointer"
                                            @click.prevent="showKvkk = true">Kişisel Verilerin Korunması Kanunu (KVKK) Metnini</span>
                                        okudum ve onaylıyorum.
                                    </label>
                                    @error('kvkk_approval') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </section>

                        {{-- KVKK MODAL --}}
                        <div x-show="showKvkk" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog"
                            aria-modal="true">
                            {{-- Backdrop --}}
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="showKvkk" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showKvkk = false"
                                    aria-hidden="true"></div>

                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                {{-- Modal Panel --}}
                                <div x-show="showKvkk" x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <div class="sm:flex sm:items-start">
                                            <div
                                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                                    KVKK Aydınlatma Metni
                                                </h3>
                                                
                                                @php
                                                    $kvkkPdfVal = \App\Models\Setting::where('key', 'kvkk_pdf')->value('value');
                                                    $kvkkTextVal = \App\Models\Setting::where('key', 'kvkk_text')->value('value');
                                                @endphp
                                                
                                                @if($kvkkPdfVal)
                                                    <div class="mt-4 w-full h-[60vh] rounded-lg overflow-hidden border border-gray-100">
                                                        <embed src="{{ asset('storage/' . $kvkkPdfVal) }}#toolbar=0" type="application/pdf" class="w-full h-full">
                                                    </div>
                                                @else
                                                    <div class="mt-4 max-h-96 overflow-y-auto bg-gray-50 p-4 rounded-lg border border-gray-100">
                                                        <div class="prose prose-sm text-gray-600">
                                                            {!! !empty($kvkkTextVal) ? $kvkkTextVal : 'KVKK metni henüz sistem yöneticisi tarafından eklenmemiştir.' !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                        <button type="button"
                                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                                            @click="showKvkk = false; document.getElementById('kvkk_approval').checked = true;">
                                            Okudum, Anladım
                                        </button>
                                        <button type="button"
                                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                            @click="showKvkk = false">
                                            Kapat
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-gray-100">
                            <a href="{{ url('/') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-800 flex items-center transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                Ana Sayfaya Dön
                            </a>
                            <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-4 px-10 rounded-xl shadow-lg shadow-indigo-500/30 transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Öneriyi Gönder
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        
        {{-- Footer --}}
        @include('layouts.footer')
    </div>
</body>
</html>