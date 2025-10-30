<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İyileştirme Önerisi Yap - {{ config('app.name', 'Laravel') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%); min-height: 100vh; }
        .floating-animation { animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
        .input-group input:focus, .input-group textarea:focus, .input-group select:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2); }
        .gradient-button { background: linear-gradient(145deg, #374151, #4b5563); transition: all 0.3s ease; }
        .gradient-button:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(75, 85, 99, 0.4); background: linear-gradient(145deg, #4b5563, #6b7280); }
    </style>
</head>
<body class="antialiased">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="floating-animation absolute top-10 left-5 w-64 h-64 bg-blue-100/50 rounded-full opacity-50 blur-2xl"></div>
        <div class="floating-animation absolute bottom-10 right-5 w-80 h-80 bg-indigo-100/50 rounded-full opacity-50 blur-2xl" style="animation-delay: -2s;"></div>
    </div>
    
    <div class="relative container mx-auto max-w-4xl py-12 px-4 z-10">
        {{-- Başlık ve Mesajlar --}}
        <div class="text-center mb-10 floating-animation">
            <a href="{{ url('/') }}"><img src="{{ asset('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png') }}" class="h-16 sm:h-20 w-auto mx-auto mb-6 drop-shadow-lg" alt="Köksan Logo"></a>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-800 tracking-tight mb-3 drop-shadow-md">İyileştirme Önerisi Paylaş</h1>
            <p class="text-lg text-gray-700 font-light">Giriş yapmadan hızlıca önerinizi bize iletin.</p>
        </div>
        @if(session('success'))<div class="mb-8 bg-green-100 border-l-4 border-green-500 text-green-800 p-5 rounded-r-lg shadow-lg" role="alert"><p class="font-bold text-lg">🎉 Harika!</p><p class="mt-1">{{ session('success') }}</p></div>@endif
        @if ($errors->any())<div class="mb-8 bg-red-100 border-l-4 border-red-500 text-red-800 p-5 rounded-r-lg shadow-lg" role="alert"><p class="font-bold text-lg">⚠️ Lütfen aşağıdaki hataları düzeltin:</p><ul class="mt-3 list-disc list-inside space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        <div class="bg-white/90 backdrop-blur-xl rounded-3xl p-6 sm:p-10 shadow-2xl border border-gray-200/80">
            <form x-data="fileUploader()" action="{{ route('guest.iaa.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-8 pb-6 border-b border-gray-200/50"><p class="text-gray-700 leading-relaxed flex items-center"><svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>Lütfen aşağıdaki formu eksiksiz doldurun. <span class="text-red-500 ml-1">*</span> ile işaretli alanlar zorunludur.</p></div>
                <div class="space-y-10">
                    
                    {{-- Bölüm 1: İletişim Bilgileri --}}
                    <div class="bg-white/50 p-6 rounded-2xl border border-gray-200/80">
                        <div class="flex items-center mb-5"><div class="bg-gray-800 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-lg mr-4">1</div><h3 class="text-2xl font-bold text-gray-900">İletişim Bilgileriniz</h3></div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="input-group"><label for="guest_name" class="block text-sm font-semibold text-gray-800 mb-2">Adınız Soyadınız <span class="text-red-500">*</span></label><input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name') }}" required class="block w-full px-4 py-3 rounded-xl shadow-sm transition-all duration-300"></div>
                            <div class="input-group"><label for="guest_email" class="block text-sm font-semibold text-gray-800 mb-2">E-posta Adresiniz</label><input type="email" name="guest_email" id="guest_email" value="{{ old('guest_email') }}" class="block w-full px-4 py-3 rounded-xl shadow-sm transition-all duration-300"></div>
                        </div>
                    </div>
                    
                    {{-- Bölüm 2: Öneri Detayları --}}
                    <div class="bg-white/50 p-6 rounded-2xl border border-gray-200/80">
                         <div class="flex items-center mb-5"><div class="bg-gray-800 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-lg mr-4">2</div><h3 class="text-2xl font-bold text-gray-900">Öneri Detayları</h3></div>
                         <div class="space-y-6">
                             <div class="input-group"><label for="baslik" class="block text-sm font-semibold text-gray-800 mb-2">Konu / Başlık <span class="text-red-500">*</span></label><input type="text" name="baslik" id="baslik" value="{{ old('baslik') }}" required class="block w-full px-4 py-3 rounded-xl shadow-sm transition-all duration-300"></div>
                             <div class="input-group"><label for="ilgili_alan" class="block text-sm font-semibold text-gray-800 mb-2">İlgili Alan / Departman <span class="text-red-500">*</span></label><input type="text" name="ilgili_alan" id="ilgili_alan" value="{{ old('ilgili_alan') }}" required placeholder="Örn: Üretim, Lojistik..." class="block w-full px-4 py-3 rounded-xl shadow-sm transition-all duration-300"></div>
                             <div class="input-group"><label for="mevcut_durum" class="block text-sm font-semibold text-gray-800 mb-2">Mevcut Durum / Problem Tanımı <span class="text-red-500">*</span></label><textarea name="mevcut_durum" id="mevcut_durum" rows="5" required class="block w-full px-4 py-3 rounded-xl shadow-sm resize-none transition-all duration-300">{{ old('mevcut_durum') }}</textarea></div>
                             <div class="input-group"><label for="oneri" class="block text-sm font-semibold text-gray-800 mb-2">İyileştirme Öneriniz</label><textarea name="oneri" id="oneri" rows="5" class="block w-full px-4 py-3 rounded-xl shadow-sm resize-none transition-all duration-300">{{ old('oneri') }}</textarea></div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6 border-t border-gray-200/80">
                                <div class="input-group sm:col-span-2"><label for="oneren_kazanc_miktar" class="block text-sm font-semibold text-gray-800 mb-2">Tahmini Kazanç</label><input type="number" name="oneren_kazanc_miktar" id="oneren_kazanc_miktar" value="{{ old('oneren_kazanc_miktar') }}" placeholder="Örn: 5000" class="block w-full px-4 py-3 rounded-xl shadow-sm transition-all duration-300"></div>
                                <div class="input-group"><label for="oneren_kazanc_birim" class="block text-sm font-semibold text-gray-800 mb-2">Para Birimi</label><select name="oneren_kazanc_birim" id="oneren_kazanc_birim" class="block w-full px-4 py-3 rounded-xl shadow-sm transition-all duration-300 bg-white"><option value="TL" @selected(old('oneren_kazanc_birim') == 'TL')>TL</option><option value="USD" @selected(old('oneren_kazanc_birim') == 'USD')>USD</option><option value="EUR" @selected(old('oneren_kazanc_birim') == 'EUR')>EUR</option></select></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6">
                                <div class="input-group sm:col-span-2"><label for="oneren_butce_miktar" class="block text-sm font-semibold text-gray-800 mb-2">Tahmini Bütçe</label><input type="number" name="oneren_butce_miktar" id="oneren_butce_miktar" value="{{ old('oneren_butce_miktar') }}" placeholder="Örn: 1000" class="block w-full px-4 py-3 rounded-xl shadow-sm transition-all duration-300"></div>
                                <div class="input-group"><label for="oneren_butce_birim" class="block text-sm font-semibold text-gray-800 mb-2">Para Birimi</label><select name="oneren_butce_birim" id="oneren_butce_birim" class="block w-full px-4 py-3 rounded-xl shadow-sm transition-all duration-300 bg-white"><option value="TL" @selected(old('oneren_butce_birim') == 'TL')>TL</option><option value="USD" @selected(old('oneren_butce_birim') == 'USD')>USD</option><option value="EUR" @selected(old('oneren_butce_birim') == 'EUR')>EUR</option></select></div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Bölüm 3: Ek Dosyalar --}}
                    <div class="bg-white/50 p-6 rounded-2xl border border-gray-200/80">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center"><div class="bg-gray-800 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-lg mr-4">3</div><h3 class="text-2xl font-bold text-gray-900">Ek Dosyalar <span class="ml-2 text-sm text-gray-500 font-medium">(İsteğe Bağlı)</span></h3></div>
                            <p class="text-sm font-medium text-gray-700">Seçilen Resimler: <span x-text="files.length" class="font-bold text-indigo-600">0</span> / 5</p>
                        </div>
                        <template x-if="errorMessage"><p x-text="errorMessage" class="my-2 text-sm text-red-600 font-semibold"></p></template>
                        <template x-if="files.length > 0">
                            <div class="grid grid-cols-3 sm:grid-cols-5 gap-4 mb-4">
                                <template x-for="(fileWrapper, index) in files" :key="index">
                                    <div class="relative group aspect-square">
                                        <img :src="fileWrapper.preview" class="w-full h-full object-cover rounded-lg shadow-md">
                                        <button @click.prevent="removeFile(index)" class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center font-bold shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">&times;</button>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <div x-show="files.length < 5">
                            <label class="relative block w-full border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-indigo-500 transition-colors cursor-pointer">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 48 48"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"></path></svg>
                                <p class="text-lg font-medium text-gray-700">Dosya Seçin</p>
                                <p class="text-sm text-gray-500 mt-1">En fazla 5 adet, her biri max 2MB</p>
                                <input type="file" x-ref="fileInput" name="resimler[]" multiple accept="image/*" @change="handleFileSelect" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>
                    </div>

                    {{-- Gönder Butonu --}}
                    <div class="flex justify-end pt-6 border-t border-gray-200/50">
                        <button type="submit" class="gradient-button w-full sm:w-auto inline-flex items-center justify-center text-white font-bold py-4 px-10 rounded-2xl shadow-xl"><svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>Öneriyi Gönder</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('fileUploader', () => ({
                files: [], // { preview: 'data:url', fileObject: File }
                errorMessage: '',
                handleFileSelect(event) {
                    this.addFiles(event.target.files);
                },
                addFiles(fileList) {
                    this.errorMessage = '';
                    const newFiles = Array.from(fileList).filter(file => file.type.startsWith('image/'));
                    if (this.files.length + newFiles.length > 5) {
                        this.errorMessage = 'Toplamda en fazla 5 resim yükleyebilirsiniz.';
                        return;
                    }
                    newFiles.forEach(file => {
                        if (file.size > 2 * 1024 * 1024) { // 2MB
                            this.errorMessage = `'${file.name}' dosyası 2MB'den büyük olduğu için eklenemedi.`;
                            return;
                        }
                        let reader = new FileReader();
                        reader.onload = (e) => {
                            this.files.push({ preview: e.target.result, fileObject: file });
                        };
                        reader.readAsDataURL(file);
                    });
                },
                removeFile(index) {
                    this.files.splice(index, 1);
                }
            }));
        });
    </script>
</body>
</html>