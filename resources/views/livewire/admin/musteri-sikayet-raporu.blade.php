<div class="space-y-6">
    {{-- Canlı bildirim mesajı --}}
    @if (session()->has('yeniSikayet'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg" role="alert">
            <p class="font-bold">🎉 Yeni Şikayet!</p>
            <p>{{ session('yeniSikayet') }}</p>
        </div>
    @endif

    {{-- KPI KARTLARI --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">Toplam Şikayet</h4>
            <p class="text-3xl font-black text-blue-600">{{ $kpi['toplam'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">Yeni (Beklemede)</h4>
            <p class="text-3xl font-black text-yellow-600">{{ $kpi['yeni'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">İşlemde</h4>
            <p class="text-3xl font-black text-indigo-600">{{ $kpi['islemde'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">Çözülen/Kapatılan</h4>
            <p class="text-3xl font-black text-green-600">{{ $kpi['cozuldu'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border {{ $kpi['gecikmis'] > 0 ? 'border-red-300 bg-red-50' : 'border-gray-100' }}">
            <h4 class="text-sm font-semibold {{ $kpi['gecikmis'] > 0 ? 'text-red-700' : 'text-gray-500' }} uppercase">Gecikmiş (İşlemde)</h4>
            <p class="text-3xl font-black {{ $kpi['gecikmis'] > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $kpi['gecikmis'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">Projeye Dönüşen</h4>
            <p class="text-3xl font-black text-purple-600">{{ $kpi['projeye_donusen'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Şikayet Durum Dağılımı</h3>
            <div id="sikayetDurumChart"></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 Şikayet Kategorisi</h3>
            <div id="sikayetKategoriChart"></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 Çözüm Takımı (Şikayet Sayısı)</h3>
            <div id="sikayetTakimChart"></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aylık Şikayet Kayıt Trendi (Son 12 Ay)</h3>
            <div id="sikayetTrendChart"></div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-green-700 mb-4">Çözülen / Kapatılan Şikayetler</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Önceliğe Göre Dağılım</h4>
                <div id="cozulenChart" class="w-full h-64"></div>
            </div>
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Son Çözülenler Listesi</h4>
                <div class="max-h-64 overflow-y-auto pr-2 space-y-3">
                    @forelse ($cozulenListesi as $item)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800 truncate" title="{{ $item->musteri_sikayet_konusu }}">{{ $item->musteri_sikayet_konusu }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->musteri_adi }}</p>
                                </div>
                                <div class="flex space-x-2 flex-shrink-0 ml-2">
                                    <a href="{{ route('admin.sikayetler.show', $item) }}" title="Şikayet Detayını Gör" class="p-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </a>
                                    @if($item->iaaProjesi)
                                    <a href="{{ route('proje.workspace.show', $item->iaaProjesi) }}" title="Proje Çalışma Alanını Gör" class="p-1.5 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM5 11a1 1 0 000 2h.01a1 1 0 100-2H5zM6 15a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zM4 17a1 1 0 100 2h12a1 1 0 100-2H4z"/></svg>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Çözülmüş şikayet bulunamadı.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-indigo-700 mb-4">İşlemde Olan Şikayetler</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Önceliğe Göre Dağılım</h4>
                <div id="islemdeChart" class="w-full h-64"></div>
            </div>
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Son İşleme Alınanlar Listesi</h4>
                <div class="max-h-64 overflow-y-auto pr-2 space-y-3">
                    @forelse ($islemdeListesi as $item)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800 truncate" title="{{ $item->musteri_sikayet_konusu }}">{{ $item->musteri_sikayet_konusu }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->musteri_adi }}</p>
                                </div>
                                <div class="flex space-x-2 flex-shrink-0 ml-2">
                                    <a href="{{ route('admin.sikayetler.show', $item) }}" title="Şikayet Detayını Gör" class="p-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </a>
                                    @if($item->iaaProjesi)
                                    <a href="{{ route('proje.workspace.show', $item->iaaProjesi) }}" title="Proje Çalışma Alanını Gör" class="p-1.5 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM5 11a1 1 0 000 2h.01a1 1 0 100-2H5zM6 15a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zM4 17a1 1 0 100 2h12a1 1 0 100-2H4z"/></svg>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">İşlemde olan şikayet bulunamadı.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-yellow-700 mb-4">Yeni (Beklemede) Olan Şikayetler</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Önceliğe Göre Dağılım</h4>
                <div id="yeniChart" class="w-full h-64"></div>
            </div>
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Son Gelenler Listesi</h4>
                <div class="max-h-64 overflow-y-auto pr-2 space-y-3">
                    @forelse ($yeniListesi as $item)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800 truncate" title="{{ $item->musteri_sikayet_konusu }}">{{ $item->musteri_sikayet_konusu }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->musteri_adi }}</p>
                                </div>
                                <div class="flex space-x-2 flex-shrink-0 ml-2">
                                    <a href="{{ route('admin.sikayetler.show', $item) }}" title="Şikayet Detayını Gör" class="p-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Yeni şikayet bulunamadı.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-purple-700 mb-4">Projeye Dönüşen Şikayetler</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Önceliğe Göre Dağılım</h4>
                <div id="projeyeDonusenChart" class="w-full h-64"></div>
            </div>
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Son Dönüşenler Listesi</h4>
                <div class="max-h-64 overflow-y-auto pr-2 space-y-3">
                    @forelse ($projeyeDonusenListesi as $item)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800 truncate" title="{{ $item->musteri_sikayet_konusu }}">{{ $item->musteri_sikayet_konusu }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->musteri_adi }}</p>
                                </div>
                                <div class="flex space-x-2 flex-shrink-0 ml-2">
                                    <a href="{{ route('admin.sikayetler.show', $item) }}" title="Şikayet Detayını Gör" class="p-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </a>
                                    @if($item->iaaProjesi)
                                    <a href="{{ route('proje.workspace.show', $item->iaaProjesi) }}" title="Proje Çalışma Alanını Gör" class="p-1.5 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM5 11a1 1 0 000 2h.01a1 1 0 100-2H5zM6 15a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zM4 17a1 1 0 100 2h12a1 1 0 100-2H4z"/></svg>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Projeye dönüşen şikayet bulunamadı.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aylık Çözülen Şikayet Trendi (Son 12 Ay)</h3>
        <div id="aylikCozulenChart"></div>
    </div>
</div>