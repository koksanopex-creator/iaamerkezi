<div id="defense_section" class="mt-6">
    {{-- HATA MESAJLARI --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
            <p class="font-bold">İşlem Başarısız:</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- BAŞARI MESAJI --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- HATA MESAJI (Session) --}}
    @if(session('error'))
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
            <p class="font-bold">Hata:</p>
            <p class="text-sm">{{ session('error') }}</p>
        </div>
    @endif

    {{-- DURUM A: Savunma Bekleniyor VEYA Yönetici Değerlendirmesi --}}
    @if($case->durum == 'Savunma Bekleniyor' || $case->durum == 'Yönetici Değerlendirmesi')
        @php
            $isOwner = $case->user_id == Auth::id();
            $isReporter = $case->reporter_id == Auth::id();
            $isAdmin = Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi']);
            $isLeader = Auth::user()->hasRole('Bölüm Lideri') && Auth::user()->bolum_id == $case->user->bolum_id;
            $canEdit = $isOwner || $isReporter || $isAdmin || $isLeader;
            $hasDefense = (bool)$case->savunma_tarihi;
        @endphp

        <div x-data="{ isEditing: {{ $hasDefense ? 'false' : 'true' }} }">
            @if(Auth::user()->hasRole(['Yonetim', 'Yönetim']) && !$canEdit)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 shadow-sm text-center">
                    <div class="inline-block p-3 bg-white rounded-full text-yellow-600 shadow-sm mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-yellow-800">Personel Savunması {{ $case->durum == 'Savunma Bekleniyor' ? 'Bekleniyor' : 'Değerlendiriliyor' }}</h3>
                    <p class="text-yellow-700 mt-2 italic">{{ $case->durum == 'Savunma Bekleniyor' ? 'Bu dosya için henüz savunma girilmemiştir.' : 'Personel savunmasını girdi, hukuk onayı bekleniyor.' }}</p>
                </div>
            @elseif($canEdit)
                
                {{-- 1. İZLEME MODU (Savunma Varsa) --}}
                <template x-if="!isEditing">
                    <div class="bg-white border-t-4 border-blue-500 rounded-lg shadow-sm p-6 mb-6">
                        <div class="flex justify-between items-center border-b pb-4 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 shadow-sm transition-all group-hover:scale-110">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Personel Savunması Sisteme Kayıtlı</h3>
                                    <p class="text-xs text-gray-500 font-medium italic">Son Güncelleme: {{ $case->savunma_tarihi ? $case->savunma_tarihi->format('d.m.Y H:i') : '-' }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                @if($case->durum == 'Yönetici Değerlendirmesi' || $case->durum == 'Savunma Bekleniyor')
                                    <button @click="isEditing = true" class="bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg border border-blue-200 text-xs font-bold hover:bg-blue-100 flex items-center gap-1.5 shadow-sm transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Savunmayı Düzenle
                                    </button>
                                @endif
                                <span class="bg-blue-600 text-white text-[10px] uppercase font-black px-3 py-1.5 rounded-lg shadow-sm">Süreçte</span>
                            </div>
                        </div>
                        
                        @php
                            $savunmaMetni = $case->savunma_aciklamasi;
                            $notKismi = null;
                            if (preg_match('/\((Not: Bu savunma .*? tarafından personel adına sisteme girilmiştir\.)\)/s', $savunmaMetni, $matches)) {
                                $notKismi = $matches[1];
                                $savunmaMetni = trim(str_replace($matches[0], '', $savunmaMetni));
                                $notKismi = preg_replace('/(\d{2}\.\d{2}\.\d{4} \d{2}:\d{2})/', '<span class="font-black text-indigo-600">$1</span>', $notKismi);
                                $notKismi = preg_replace('/(Bölüm Yöneticisi (.*?)) (tarafından)/', '<span class="font-bold text-slate-800">$1</span> $3', $notKismi);
                            }
                        @endphp

                        <div class="bg-indigo-50/30 p-5 rounded-2xl border border-indigo-100 mb-4 shadow-sm">
                            <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap font-medium">"{{ $savunmaMetni }}"</div>
                            @if($notKismi)
                                <div class="mt-4 pt-4 border-t border-indigo-100/50">
                                    <div class="flex items-start gap-3 bg-white/50 p-3 rounded-xl border border-indigo-50">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div class="text-[11px] text-slate-500 leading-relaxed italic">{!! $notKismi !!}</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if(!empty($case->savunma_dosyalari))
                            <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 border-b pb-1">Savunma Ekleri:</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($case->savunma_dosyalari as $dosya)
                                    @php
                                        $url = asset('storage/' . $dosya);
                                        $ext = strtolower(pathinfo($dosya, PATHINFO_EXTENSION));
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    @endphp
                                    <a href="{{ $url }}" target="_blank" class="group relative border rounded-lg overflow-hidden bg-white hover:shadow-md transition block">
                                        <div class="h-24 w-full flex items-center justify-center bg-gray-50 overflow-hidden relative">
                                            @if($isImage)
                                                <img src="{{ $url }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                            @else
                                                <div class="text-center">
                                                    <svg class="w-8 h-8 text-gray-400 group-hover:text-indigo-500 transition mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-2 border-t bg-white truncate text-[10px] text-gray-500">{{ basename($dosya) }}</div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </template>

                {{-- 2. DÜZENLEME MODU (FORM) --}}
                <template x-if="isEditing">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 shadow-sm mb-6">
                        <div class="flex justify-between items-center mb-4 border-b border-yellow-200 pb-2">
                            <h3 class="text-lg font-bold text-yellow-800 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                @php
                                    $isLeader = Auth::user()->hasRole('Bölüm Lideri');
                                    $isAdminRole = Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi']);
                                    $isKurulRole = Auth::user()->hasRole(['Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']);
                                    $personnelName = $case->user->name ?? 'Personel';

                                    // İlk giriş başlık/açıklama (savunma_tarihi yokken)
                                    if (!$isOwner) {
                                        $ilkGirisBaslik = $personnelName . ' Adına Savunma Girişi';
                                        $ilkGirisDesc = 'Lütfen <strong>' . $personnelName . '</strong> adına savunmayı detaylı bir şekilde yazınız. Varsa kanıt dosyalarını ekleyiniz. Bu işlem kayıt altına alınacaktır.';
                                    } else {
                                        $ilkGirisBaslik = 'Personel Savunması Girişi';
                                        $ilkGirisDesc = 'Lütfen savunmanızı detaylı bir şekilde yazınız. Varsa kanıt dosyalarınızı ekleyiniz.';
                                    }

                                    // Düzenleme başlık/açıklama (savunma_tarihi varken)
                                    $title = 'Savunmayı Düzenle';
                                    $desc = 'Savunmanızda düzeltme yapabilirsiniz. Karar verilene kadar düzenleme yetkiniz mevcuttur.';

                                    if (!$isOwner && ($isLeader || $isAdminRole || $isKurulRole)) {
                                        $title = $personnelName . ' Adına Savunmayı Düzenle';
                                        $desc = $personnelName . ' adına kaydedilen savunmayı düzeltebilirsiniz. Bu işlem kayıt altına alınacaktır.';
                                    }
                                @endphp
                                {{ $case->savunma_tarihi ? $title : $ilkGirisBaslik }}
                            </h3>
                            @if($hasDefense)
                                <button @click="isEditing = false" class="text-gray-500 hover:text-gray-700 text-xs font-bold uppercase underline">Vazgeç</button>
                            @endif
                        </div>
                        @if(!$isOwner)
                            <div class="mb-3 px-3 py-2 bg-amber-100 border border-amber-300 rounded-lg flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs font-bold text-amber-800">Bu savunmayı <span class="font-black underline">{{ $personnelName }}</span> adına siz yazıyorsunuz.</p>
                            </div>
                        @endif
                        <p class="text-sm text-yellow-700 mb-4">
                            {!! $case->savunma_tarihi ? $desc : $ilkGirisDesc !!}
                        </p>

                        <form action="{{ route('disiplin.defense.store', $case->id) }}" method="POST" enctype="multipart/form-data"
                            x-data="{ 
                                upfiles: [],
                                handleFileSelect(event) {
                                    const fileList = event.target.files;
                                    const dt = new DataTransfer();
                                    this.upfiles.forEach(file => dt.items.add(file));
                                    for (let i = 0; i < fileList.length; i++) { dt.items.add(fileList[i]); this.upfiles.push(fileList[i]); }
                                    this.$refs.fileInput.files = dt.files;
                                },
                                removeFile(index) {
                                    this.upfiles.splice(index, 1);
                                    const dt = new DataTransfer();
                                    this.upfiles.forEach(file => dt.items.add(file));
                                    this.$refs.fileInput.files = dt.files;
                                },
                                formatSize(size) {
                                    if(size > 1024*1024) return (size/(1024*1024)).toFixed(2) + ' MB';
                                    return (size/1024).toFixed(2) + ' KB';
                                }
                            }">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Savunma Metni <span class="text-red-500">*</span></label>
                                <textarea name="savunma_aciklamasi" rows="6" class="w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Olayla ilgili açıklamanızı buraya yazınız..." required>{{ old('savunma_aciklamasi', $case->savunma_aciklamasi) }}</textarea>
                            </div>
                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Savunma Ekleri {{ $case->savunma_dosyalari ? '(Mevcutlara eklenir)' : '' }}</label>
                                <div class="border-2 border-dashed border-yellow-400 bg-white rounded-lg p-6 hover:bg-yellow-50 transition relative">
                                    <input x-ref="fileInput" type="file" name="savunma_dosyalari[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" @change="handleFileSelect">
                                    <div class="text-center" x-show="upfiles.length === 0">
                                        <svg class="mx-auto h-10 w-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <p class="mt-1 text-sm text-gray-600"><span class="font-medium text-yellow-600 hover:text-yellow-500">Dosya seçin</span> veya sürükleyip bırakın</p>
                                    </div>
                                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mt-4" x-show="upfiles.length > 0">
                                        <template x-for="(file, index) in upfiles" :key="index">
                                            <div class="relative group border border-yellow-200 bg-yellow-50 p-2 rounded flex items-center gap-3 z-20">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[10px] font-medium text-gray-900 truncate" x-text="file.name"></p>
                                                    <p class="text-[9px] text-gray-500" x-text="formatSize(file.size)"></p>
                                                </div>
                                                <button type="button" @click.prevent="removeFile(index)" class="text-red-500 hover:text-red-700 font-bold p-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3">
                                @if($hasDefense)
                                    <button type="button" @click="isEditing = false" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg font-bold hover:bg-gray-200 shadow transition">Vazgeç</button>
                                @endif
                                <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-yellow-700 shadow transition flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @if($case->savunma_tarihi)
                                        Değişiklikleri Kaydet
                                    @elseif(!$isOwner)
                                        {{ $personnelName }} Adına Savunmayı Kaydet ve Gönder
                                    @else
                                        Savunmayı Kaydet ve Gönder
                                    @endif
                                </button>
                            </div>
                        </form>
                    </div>
                </template>


            @endif
        </div>

    {{-- DURUM B: Karar Verildi veya Kurulda (Sadece GÖSTERİM) --}}
    @elseif(in_array($case->durum, ['Karar Verildi', 'Kurulda', 'İtiraz Edildi', 'Yönetici Değerlendirmesi']))
        <div class="bg-white border-t-4 border-blue-500 rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start border-b pb-4 mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Personel Savunması</h3>
                    @if($case->savunma_tarihi)
                        <p class="text-xs text-gray-500">Tarih: {{ $case->savunma_tarihi->format('d.m.Y H:i') }}</p>
                    @endif
                </div>
                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">Kesinleşti / Kapatıldı</span>
            </div>
            
            @php
                $savunmaMetni = $case->savunma_aciklamasi;
                $notKismi = null;
                // Regex ile (Not: ...) kısmını bul ve ayır
                if (preg_match('/\((Not: Bu savunma .*? tarafından personel adına sisteme girilmiştir\.)\)/s', $savunmaMetni, $matches)) {
                    $notKismi = $matches[1];
                    $savunmaMetni = trim(str_replace($matches[0], '', $savunmaMetni));
                    
                    // Dinamik alanları (tarih ve isim) kalınlaştır ve renklendir
                    // Tarih formatı: 14.05.2026 13:48
                    $notKismi = preg_replace('/(\d{2}\.\d{2}\.\d{4} \d{2}:\d{2})/', '<span class="font-black text-indigo-600">$1</span>', $notKismi);
                    // Yönetici ismi (Bölüm Yöneticisi X)
                    $notKismi = preg_replace('/(Bölüm Yöneticisi (.*?)) (tarafından)/', '<span class="font-bold text-slate-800">$1</span> $3', $notKismi);
                }
            @endphp

            <div class="bg-indigo-50/30 p-5 rounded-2xl border border-indigo-100 mb-4 shadow-sm">
                @if($case->savunma_aciklamasi)
                    <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap font-medium">"{{ $savunmaMetni }}"</div>
                @else
                    <div class="text-sm text-slate-400 italic font-medium text-center py-4">Bu dosya için personel savunması girilmemiştir.</div>
                @endif
                
                @if($notKismi)
                    <div class="mt-4 pt-4 border-t border-indigo-100/50">
                        <div class="flex items-start gap-3 bg-white/50 p-3 rounded-xl border border-indigo-50">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="text-[11px] text-slate-500 leading-relaxed italic">
                                {!! $notKismi !!}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if(!empty($case->savunma_dosyalari))
                <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 border-b pb-1">Savunma Ekleri:</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($case->savunma_dosyalari as $dosya)
                        @php
                            $url = asset('storage/' . $dosya);
                            $ext = strtolower(pathinfo($dosya, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp
                        <a href="{{ $url }}" target="_blank" class="group relative border rounded-lg overflow-hidden bg-white hover:shadow-md transition block">
                            <div class="h-24 w-full flex items-center justify-center bg-gray-50 overflow-hidden">
                                @if($isImage)
                                    <img src="{{ $url }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-8 h-8 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @endif
                            </div>
                            <div class="p-2 border-t bg-white italic text-[10px] text-gray-400 truncate">{{ basename($dosya) }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>