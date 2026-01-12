<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <span class="text-gray-500">#{{ $case->id }}</span>
            {{ $case->dosya_no ?? 'Dosya No Bekleniyor' }}
            
            {{-- Dinamik Status Badge --}}
            @php
                $colors = [
                    'taslak' => 'bg-gray-100 text-gray-600',
                    'hukuk_incelemesinde' => 'bg-blue-100 text-blue-700',
                    'yonetim_onayinda' => 'bg-purple-100 text-purple-700',
                    'arabulucuda' => 'bg-indigo-100 text-indigo-700',
                    'imza_asamasinda' => 'bg-yellow-100 text-yellow-700',
                    'odeme_bekliyor' => 'bg-orange-100 text-orange-800 border border-orange-500 animate-pulse font-black',
                    'kapatildi' => 'bg-green-100 text-green-700',
                    'anlasma_saglanamadi' => 'bg-red-100 text-red-700',
                ];
                $statusClass = $colors[$case->status] ?? 'bg-gray-100 text-gray-600';
            @endphp
            <span class="px-3 py-1 text-sm rounded-full {{ $statusClass }} font-semibold shadow-sm">
                {{ strtoupper(str_replace('_', ' ', $case->status)) }}
            </span>
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            İlgili Çalışan: <span class="font-bold text-gray-700">{{ $case->calisan->name }}</span> 
            ({{ $case->calisan->email }})
        </p>

        {{-- Oluşturan ve Tarih --}}
        <p class="text-xs text-gray-400 mt-1 flex items-center gap-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Oluşturan: <span class="font-semibold">{{ $case->creator->name ?? 'Sistem' }}</span></span>
            <span class="text-gray-300">|</span>
            <span>{{ $case->created_at->format('d.m.Y H:i') }}</span>
        </p>
    </div>
    
    <div class="flex gap-2 items-center">
        
        {{-- 1. HUKUK İŞLEM BUTONLARI --}}
        @if($case->status == 'hukuk_incelemesinde' && (auth()->user()->can('arabuluculuk.approve_legal') || auth()->user()->hasRole('Superadmin')))
            <div x-data="{ openDecision: false }" class="relative">
                <button @click="openDecision = !openDecision" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow font-bold flex items-center transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Karar Ver / İşlem Yap
                </button>
                <div x-show="openDecision" @click.away="openDecision = false" class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-xl z-50 border border-gray-200 p-4" style="display: none;">
                    <form action="{{ route('admin.arabuluculuk.submitDecision', $case->id) }}" method="POST">
                        @csrf
                        <p class="text-xs font-bold text-gray-400 uppercase mb-2">Kararınız:</p>
                        <label class="flex items-start mb-3 cursor-pointer hover:bg-gray-50 p-1 rounded">
                            <input type="radio" name="action" value="send_to_board" class="mt-1 mr-2" checked>
                            <div>
                                <span class="font-bold text-sm text-gray-800">Yönetim Onayına Gönder</span>
                                <p class="text-xs text-gray-500">Ben onaylıyorum, son kararı yönetim versin.</p>
                            </div>
                        </label>
                        <label class="flex items-start mb-3 cursor-pointer hover:bg-gray-50 p-1 rounded">
                            <input type="radio" name="action" value="approve_direct" class="mt-1 mr-2">
                            <div>
                                <span class="font-bold text-sm text-green-700">Doğrudan Onayla</span>
                                <p class="text-xs text-gray-500">Yönetimi pas geç, süreç ilerlesin.</p>
                            </div>
                        </label>
                        <label class="flex items-start mb-3 cursor-pointer hover:bg-gray-50 p-1 rounded">
                            <input type="radio" name="action" value="request_revision" class="mt-1 mr-2">
                            <div>
                                <span class="font-bold text-sm text-red-700">Revizyon İste (Geri Gönder)</span>
                                <p class="text-xs text-gray-500">Personele iade et, düzeltme iste.</p>
                            </div>
                        </label>
                        <textarea name="note" class="w-full text-xs border-gray-300 rounded mb-3" rows="2" placeholder="Gerekçe veya notunuz (Revizyon için zorunlu)..."></textarea>
                        <button type="submit" class="w-full bg-indigo-600 text-white text-sm font-bold py-2 rounded hover:bg-indigo-700">İşlemi Uygula</button>
                    </form>
                </div>
            </div>
        @endif

        {{-- 2. YÖNETİM İŞLEM BUTONLARI --}}
        @if($case->status == 'yonetim_onayinda' && (auth()->user()->can('arabuluculuk.approve_board') || auth()->user()->hasRole('Superadmin')))
            <div x-data="{ openBoard: false }" class="relative">
                <button @click="openBoard = !openBoard" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow font-bold flex items-center transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Yönetim Kararı
                </button>
                <div x-show="openBoard" @click.away="openBoard = false" class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-xl z-50 border border-gray-200 p-4" style="display: none;">
                    <form action="{{ route('admin.arabuluculuk.submitDecision', $case->id) }}" method="POST">
                        @csrf
                        <label class="flex items-start mb-3 cursor-pointer hover:bg-gray-50 p-1 rounded">
                            <input type="radio" name="action" value="board_approve" class="mt-1 mr-2" checked>
                            <div>
                                <span class="font-bold text-sm text-green-700">Onayla</span>
                                <p class="text-xs text-gray-500">Süreci tamamla ve ödemeye geç.</p>
                            </div>
                        </label>
                        <label class="flex items-start mb-3 cursor-pointer hover:bg-gray-50 p-1 rounded">
                            <input type="radio" name="action" value="board_reject" class="mt-1 mr-2">
                            <div>
                                <span class="font-bold text-sm text-red-700">Hukuka İade Et (Revize)</span>
                                <p class="text-xs text-gray-500">Dosyayı hukuk birimine geri gönder.</p>
                            </div>
                        </label>
                        <textarea name="note" class="w-full text-xs border-gray-300 rounded mb-3" rows="2" placeholder="Karar notunuz..."></textarea>
                        <button type="submit" class="w-full bg-purple-600 text-white text-sm font-bold py-2 rounded hover:bg-purple-700">Kararı Kaydet</button>
                    </form>
                </div>
            </div>
        @endif

        {{-- 3. ARABULUCULUK SONUÇLANDIRMA BUTONLARI --}}
        @if($case->status == 'arabulucuda' && (
            auth()->user()->can('arabuluculuk.approve_legal') || 
            auth()->user()->hasRole('Superadmin') || 
            (auth()->user()->can('arabuluculuk.assign_mediator') && auth()->id() == $case->created_by)
        ))
            <div x-data="{ openResult: false }" class="relative">
                <button @click="openResult = !openResult" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow font-bold flex items-center transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Süreci Sonuçlandır
                </button>
                <div x-show="openResult" @click.away="openResult = false" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-xl z-50 border border-gray-200 p-4" style="display: none;">
                    <form action="{{ route('admin.arabuluculuk.submitDecision', $case->id) }}" method="POST">
                        @csrf
                        <p class="text-xs font-bold text-gray-400 uppercase mb-3">Arabuluculuk Sonucu:</p>
                        <label class="flex items-start mb-3 cursor-pointer hover:bg-green-50 p-2 rounded border border-transparent hover:border-green-200 transition">
                            <input type="radio" name="action" value="mediation_agreement" class="mt-1 mr-2" checked>
                            <div>
                                <span class="font-bold text-sm text-green-700">Anlaşma Sağlandı</span>
                                <p class="text-xs text-gray-500">Süreci tamamla ve ödeme planına geç.</p>
                            </div>
                        </label>
                        <label class="flex items-start mb-3 cursor-pointer hover:bg-red-50 p-2 rounded border border-transparent hover:border-red-200 transition">
                            <input type="radio" name="action" value="mediation_disagreement" class="mt-1 mr-2">
                            <div>
                                <span class="font-bold text-sm text-red-700">Anlaşma Sağlanamadı</span>
                                <p class="text-xs text-gray-500">Süreci olumsuz olarak kapat.</p>
                            </div>
                        </label>
                        <textarea name="note" class="w-full text-xs border-gray-300 rounded mb-3" rows="2" placeholder="Varsa arabulucu notları..."></textarea>
                        <button type="submit" class="w-full bg-green-600 text-white text-sm font-bold py-2 rounded hover:bg-green-700">Kaydet ve İlerlet</button>
                    </form>
                </div>
            </div>
        @endif

        <a href="{{ route('admin.arabuluculuk.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg shadow-sm font-medium transition">
            &larr; Listeye Dön
        </a>
    </div>
</div>