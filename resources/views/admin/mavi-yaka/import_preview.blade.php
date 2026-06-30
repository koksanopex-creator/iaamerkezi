<x-app-layout>
    <x-slot name="title">Mavi Yaka İçe Aktarma Önizleme</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('İçe Aktarma Önizleme') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <h1 id="pageMainTitle" class="text-2xl font-bold text-gray-900 transition-colors">Dosya Analiz Sonucu</h1>
                <p id="pageMainSubtitle" class="text-gray-500 text-sm transition-colors">Veriler henüz kaydedilmedi, lütfen aşağıdaki detayları kontrol edip onaylayın.</p>
            </div>

            <div class="space-y-6">
                {{-- Progress Bar (TOP) --}}
                <div id="progressContainer" class="hidden bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <span id="progressLabel" class="text-sm font-bold text-gray-700">İşlem Yapılıyor...</span>
                        <span id="progressPercent" class="text-sm font-bold text-indigo-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                        <div id="progressBar" class="bg-indigo-600 h-full transition-all duration-300 w-0"></div>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <p id="progressStatus" class="text-xs text-gray-500 font-medium">Lütfen bekleyin, veriler sisteme kaydediliyor...</p>
                        <p id="etaStatus" class="text-xs font-bold text-indigo-500">Tahmini Kalan: Hesaplanıyor...</p>
                    </div>
                </div>

                {{-- Final Report UI (TOP - Hidden by default) --}}
                <div id="reportContainer" class="hidden bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
                    <div class="flex items-center gap-3 pb-4 border-b">
                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Aktarım Özeti</h3>
                    </div>
                    
                    <div class="grid grid-cols-4 gap-4 text-center">
                        <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                            <span class="block text-2xl font-bold text-green-700" id="statAdded">0</span>
                            <span class="text-xs text-green-600 font-medium uppercase tracking-wider">Yeni</span>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                            <span class="block text-2xl font-bold text-blue-700" id="statUpdated">0</span>
                            <span class="text-xs text-blue-600 font-medium uppercase tracking-wider">Güncelleme</span>
                        </div>
                        <div class="p-4 bg-yellow-50 rounded-xl border border-yellow-100">
                            <span class="block text-2xl font-bold text-yellow-700" id="statDepts">{{ count($newDepts) }}</span>
                            <span class="text-xs text-yellow-600 font-medium uppercase tracking-wider">Yeni Bölüm</span>
                        </div>
                        <div class="p-4 bg-red-50 rounded-xl border border-red-100">
                            <span class="block text-2xl font-bold text-red-700" id="statErrors">0</span>
                            <span class="text-xs text-red-600 font-medium uppercase tracking-wider">Hata/Atlanan</span>
                        </div>
                    </div>

                    <div id="errorListContainer" class="hidden">
                        <h4 class="text-sm font-bold text-red-800 mb-2">Hatalı Kayıt Detayları:</h4>
                        <div class="max-h-60 overflow-y-auto border border-red-100 rounded-xl bg-red-50">
                            <table class="min-w-full divide-y divide-red-200">
                                <thead class="bg-red-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-bold text-red-700 uppercase">Satır</th>
                                        <th class="px-4 py-2 text-left text-xs font-bold text-red-700 uppercase">Personel</th>
                                        <th class="px-4 py-2 text-left text-xs font-bold text-red-700 uppercase">Hata Nedeni</th>
                                    </tr>
                                </thead>
                                <tbody id="errorTableBody" class="divide-y divide-red-100 font-medium text-xs text-red-900">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-center pt-2">
                        <a href="{{ route('admin.mavi-yaka.index') }}" class="px-8 py-2 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition-all shadow-md">Listeye Dön</a>
                    </div>
                </div>

                {{-- Genel Özet --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-2xl font-bold text-gray-900">{{ $userCount }} Personel</span>
                            <span class="text-sm text-gray-500">İşlem Modu: <strong class="text-indigo-600">{{ $mode === 'update' ? 'Güncelle ve Ekle' : 'Sadece Yeni Kayıtlar' }}</strong></span>
                        </div>
                    </div>
                </div>

                {{-- Yeni Bölümler --}}
                @if(count($newDepts) > 0)
                <div class="bg-yellow-50 rounded-2xl border border-yellow-200 p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <svg class="w-6 h-6 text-yellow-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <div>
                            <h3 class="text-yellow-800 font-bold">Yeni Bölümler Tespit Edildi!</h3>
                            <p class="text-yellow-700 text-sm">Dosyadaki aşağıdaki bölümler sistemde kayıtlı değil. Onay verirseniz otomatik olarak oluşturulacaktır.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($newDepts as $dept)
                            <span class="px-3 py-1 bg-white border border-yellow-300 rounded-lg text-xs font-bold text-yellow-800 uppercase">{{ $dept }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Mevcut Bölümler (Eşleşme Onayı) --}}
                @if(count($existingDepts) > 0)
                <div class="bg-indigo-50 rounded-2xl border border-indigo-200 p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <svg class="w-6 h-6 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <h3 class="text-indigo-800 font-bold">Eşleşen Bölümler</h3>
                            <p class="text-indigo-700 text-sm">Sistemdeki mevcut bölümlerle eşleşenler aşağıdadır. Eşleşmelerin doğruluğunu teyit edin.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($existingDepts as $dept)
                            <span class="px-3 py-1 bg-white border border-indigo-300 rounded-lg text-xs font-medium text-indigo-800 uppercase">{{ $dept }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <div id="actionContainer" class="bg-gray-50 rounded-2xl p-4 text-center">
                    <p class="text-sm text-gray-500 mb-4 font-medium italic">"Aktarımı Tamamla" butonuna bastığınızda işlemler veritabanına uygulanacaktır.</p>
                    
                    <form id="importForm" action="{{ route('admin.mavi-yaka.import-execute') }}" method="POST" class="inline-block">
                        @csrf
                        <div class="flex gap-3 justify-center">
                            <a href="{{ route('admin.mavi-yaka.import') }}" class="px-6 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-100">Geri Dön</a>
                            <button type="submit" class="px-8 py-2 text-sm font-bold text-white bg-green-600 rounded-xl hover:bg-green-700 shadow-lg transition-all hover:-translate-y-1">Aktarımı Tamamla ve Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('importForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const progressPercent = document.getElementById('progressPercent');
            const progressStatus = document.getElementById('progressStatus');
            const reportContainer = document.getElementById('reportContainer');
            
            // UI Hazırla
            submitBtn.disabled = true;
            submitBtn.classList.add('hidden');
            progressContainer.classList.remove('hidden');
            
            let chunkIndex = 0;
            let isFinished = false;
            let totalAdded = 0;
            let totalUpdated = 0;
            let allErrors = [];
            
            // ETA Hesaplama değişkenleri
            const startTime = Date.now();
            const totalItems = {{ $userCount }};
            const totalChunks = Math.ceil(totalItems / 10);
            const etaStatus = document.getElementById('etaStatus');
            
            try {
                while (!isFinished) {
                    const response = await fetch("{{ route('admin.mavi-yaka.import-execute-chunk') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ chunk_index: chunkIndex })
                    });
                    
                    if (!response.ok) {
                        const errorHtml = await response.text();
                        console.error(errorHtml);
                        throw new Error('Sunucu taraflı bir hata oluştu. Lütfen sistem yöneticisine başvurun.');
                    }
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        totalAdded += result.added;
                        totalUpdated += result.updated;
                        if (result.errors && result.errors.length > 0) {
                            allErrors = allErrors.concat(result.errors);
                        }
                        isFinished = result.is_finished;
                        chunkIndex++;
                        
                        // Progress Güncelle
                        progressBar.style.width = result.progress + '%';
                        progressPercent.innerText = result.progress + '%';
                        progressStatus.innerText = `${totalAdded + totalUpdated + allErrors.length} satır işlendi...`;
                        
                        // ETA (Kalan Süre) Hesapla
                        const elapsedMs = Date.now() - startTime;
                        const avgMsPerChunk = elapsedMs / chunkIndex; 
                        const remainingChunks = totalChunks - chunkIndex;
                        const remainingMs = avgMsPerChunk * remainingChunks;
                        
                        if (remainingChunks > 0) {
                            let seconds = Math.round(remainingMs / 1000);
                            let etaStr = "";
                            if (seconds >= 60) {
                                etaStr = Math.floor(seconds / 60) + " dk " + (seconds % 60) + " sn";
                            } else if (seconds > 0) {
                                etaStr = seconds + " sn";
                            } else {
                                etaStr = "Tamamlanıyor...";
                            }
                            etaStatus.innerText = "Tahmini Kalan: " + etaStr;
                        } else {
                            etaStatus.innerText = "İşlem bitti, rapor hazırlanıyor...";
                        }
                        
                    } else {
                        throw new Error(result.error || 'Bir hata oluştu.');
                    }
                }
                
                // İşlem Bitti - Raporu Hazırla
                progressContainer.classList.add('hidden');
                reportContainer.classList.remove('hidden');
                document.getElementById('actionContainer').classList.add('hidden');
                
                // Başlıkları Başarı olarak değiştir
                const mainTitle = document.getElementById('pageMainTitle');
                const mainSubtitle = document.getElementById('pageMainSubtitle');
                mainTitle.innerText = "Aktarım Başarıyla Tamamlandı";
                mainTitle.classList.remove('text-gray-900');
                mainTitle.classList.add('text-green-600');
                mainSubtitle.innerText = "Tüm veriler sisteme eksiksiz bir şekilde işlendi. Aşağıdan aktarım özetini inceleyebilirsiniz.";
                
                document.getElementById('statAdded').innerText = totalAdded;
                document.getElementById('statUpdated').innerText = totalUpdated;
                document.getElementById('statErrors').innerText = allErrors.length;
                
                if (allErrors.length > 0) {
                    const errorListContainer = document.getElementById('errorListContainer');
                    const errorTableBody = document.getElementById('errorTableBody');
                    errorListContainer.classList.remove('hidden');
                    
                    allErrors.forEach(err => {
                        const row = `<tr>
                            <td class="px-4 py-2 font-bold">${err.row}</td>
                            <td class="px-4 py-2">${err.name}</td>
                            <td class="px-4 py-2 text-red-600 italic">${err.message}</td>
                        </tr>`;
                        errorTableBody.insertAdjacentHTML('beforeend', row);
                    });
                }
                
                // Session temizliği için sessizce finish rotasına git (isteğe bağlı, şimdilik UI'da kalıyoruz)
                fetch("{{ route('admin.mavi-yaka.import-finish') }}?added=" + totalAdded + "&updated=" + totalUpdated + "&newDepts={{ count($newDepts) }}");
                
            } catch (error) {
                console.error(error);
                alert('Beklenmedik bir hata oluştu: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.classList.remove('hidden');
            }
        });
    </script>
    @endpush
</x-app-layout>
