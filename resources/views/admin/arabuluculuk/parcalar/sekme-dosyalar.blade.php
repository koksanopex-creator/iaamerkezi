{{-- YÜKLEME FORMU VE UYARILAR --}}
@if($case->status == 'kapatildi')
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700"><strong>Dosya Kapatıldı:</strong> Bu dosya kapatıldığı için artık yeni belge
                    yüklenemez.</p>
            </div>
        </div>
    </div>
@elseif(auth()->id() == $case->created_by && $case->status == 'odeme_bekliyor' && !auth()->user()->can('arabuluculuk.approve_legal'))
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">Dosya şu an ödeme planı aşamasında olduğu için dosya yükleme yetkiniz
                    geçici olarak kısıtlanmıştır.</p>
                <p class="text-xs text-blue-600 mt-1">Bir hata varsa "Finans & Ödeme" sekmesinden süreci geri
                    çekebilirsiniz.</p>
            </div>
        </div>
    </div>
@else
    <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
        <h3 class="font-bold mb-3">Yeni Belge Yükle</h3>
        <form action="{{ route('admin.arabuluculuk.uploadFile', $case->id) }}" method="POST" enctype="multipart/form-data"
            class="flex gap-4 items-end">
            @csrf
            <div class="flex-1">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Belge Türü</label>
                <select name="doc_type" class="w-full border-gray-300 rounded text-sm">
                    @if($case->status == 'taslak' && (auth()->id() == $case->created_by || auth()->user()->hasRole('Superadmin') || auth()->user()->can('arabuluculuk.approve_legal')))
                        <option value="taslak_anlasma">Taslak Anlaşma Belgesi</option>
                        <option value="anlasma_saglanamadi_tutanagi">Anlaşma Sağlanamadı Tutanağı</option>
                    @endif
                    @if($case->status != 'taslak')
                        {{-- Genel Dosya Yükleme Yetkisi veya Hukuk Onay Yetkisi Olanlar --}}
                        @if(auth()->user()->can('arabuluculuk.approve_legal') || auth()->user()->can('arabuluculuk.assign_mediator') || auth()->user()->hasRole('Superadmin'))
                            <option value="imzali_belge">İmzalı Belge (PDF/UDF)</option>
                            <option value="islak_imza_teslim">Islak İmza Teslim Tutanağı</option>
                            <option value="arabuluculuk_son_tutanak">Arabuluculuk Son Tutanağı</option>
                        @endif
                        {{-- Finans, Superadmin veya Genel Dosya Yükleme Yetkisi (Dekont için) --}}
                        @if(auth()->user()->can('arabuluculuk.finance_pay') || auth()->user()->hasRole('Superadmin'))
                            @if($case->payments->count() > 0)
                                <option value="dekont">Ödeme Dekontu</option>
                            @endif
                        @endif
                    @endif
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dosyalar (Çoklu Seçilebilir)</label>
                <input type="file" name="files[]" multiple
                    class="w-full border border-gray-300 rounded p-1 text-sm bg-white" required>
            </div>
            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded font-bold hover:bg-blue-700 h-10">Yükle</button>
        </form>
    </div>
@endif

{{-- DOSYA LİSTESİ --}}
@if($case->files->count() > 0)
    <div class="grid grid-cols-1 gap-3">
        @foreach($case->files as $file)
            <div
                class="flex justify-between items-center p-3 border rounded hover:bg-gray-50 {{ $file->locked ? 'bg-red-50 border-red-200' : '' }}">
                <div class="flex items-center gap-3">
                    @if(Str::endsWith($file->dosya_yolu, '.pdf'))
                        <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                            <path fill-rule="evenodd"
                                d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                clip-rule="evenodd" />
                        </svg>
                    @elseif(Str::endsWith($file->dosya_yolu, ['.doc', '.docx']))
                        <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                clip-rule="evenodd" />
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                clip-rule="evenodd" />
                        </svg>
                    @endif
                    <div>
                        <p class="font-bold text-sm text-gray-800">
                            {{ $file->orijinal_adi }}
                            <span class="text-xs text-gray-500 bg-gray-200 px-1 rounded ml-1">{{ $file->doc_type }}</span>
                        </p>
                        <p class="text-xs text-gray-500">{{ $file->created_at->format('d.m.Y H:i') }} -
                            {{ $file->uploader->name ?? 'Sistem' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($file->locked)
                        <span class="text-xs font-bold text-red-600 flex items-center bg-red-100 px-2 py-1 rounded"
                            title="Yasal belge olduğu için kilitli">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            KİLİTLİ
                        </span>
                    @endif
                    <a href="{{ asset('storage/' . $file->dosya_yolu) }}" target="_blank"
                        class="text-sm font-bold text-blue-600 hover:underline">İndir</a>
                    @if(auth()->user()->hasRole('Superadmin') || (auth()->id() == $case->created_by && $case->status == 'taslak') || (auth()->id() == $file->uploaded_by && $case->status != 'kapatildi'))
                        <form action="{{ route('admin.arabuluculuk.deleteFile', ['file' => $file->id]) }}" method="POST"
                            onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" title="Sil">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-10 text-gray-500">Henüz dosya yüklenmemiş.</div>
@endif