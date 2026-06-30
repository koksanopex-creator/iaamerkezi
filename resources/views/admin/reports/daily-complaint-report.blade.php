@push('pageTitle')
    Günlük Müşteri Şikayetleri Raporu ({{ $tarih }}) | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Günlük Müşteri Şikayetleri Raporu') }}
            <span class="text-sm font-normal text-gray-500 ml-2">({{ $tarih }})</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(isset($raporData['sikayet_genel']))
                <!-- ÖZET KARTLARI (FİLTRELEMEYE YARIYOR) -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- TOPLAM -->
                    <a href="{{ route('admin.sikayetler.index') }}"
                        class="block p-6 bg-pink-50 border border-pink-200 rounded-lg shadow-sm hover:shadow-md transition">
                        <h3 class="text-xs font-bold text-pink-700 uppercase tracking-wide">TOPLAM ŞİKAYET</h3>
                        <p class="mt-2 text-3xl font-extrabold text-pink-700">
                            {{ $raporData['sikayet_genel']['toplam_kayit'] }}</p>
                    </a>

                    <!-- YENİ (FİLTRE LİNKİ) -->
                    <!-- YENİ (FİLTRE LİNKİ) -->
                    <a href="{{ route('admin.sikayetler.index', ['durum' => 'Yeni']) }}"
                        class="block p-6 bg-white border border-red-200 rounded-lg shadow-sm hover:shadow-md transition group">
                        <h3
                            class="text-xs font-bold text-gray-500 uppercase tracking-wide group-hover:text-red-600 transition">
                            YENİ / BEKLEYEN</h3>
                        <p class="mt-2 text-3xl font-extrabold text-red-500">
                            {{ $raporData['sikayet_genel']['bekleyen_yeni'] }}</p>
                    </a>

                    <!-- İŞLEMDE -->
                    <a href="{{ route('admin.sikayetler.index', ['durum' => 'İşlemde']) }}"
                        class="block p-6 bg-white border border-amber-200 rounded-lg shadow-sm hover:shadow-md transition group">
                        <h3
                            class="text-xs font-bold text-gray-500 uppercase tracking-wide group-hover:text-amber-600 transition">
                            İŞLEMDE</h3>
                        <p class="mt-2 text-3xl font-extrabold text-amber-500">
                            {{ $raporData['sikayet_genel']['islemde_olan'] }}</p>
                    </a>

                    <!-- ÇÖZÜLEN -->
                    <!-- ÇÖZÜLEN -->
                    <a href="{{ route('admin.sikayetler.index', ['durum' => 'Kapatıldı']) }}"
                        class="block p-6 bg-white border border-emerald-200 rounded-lg shadow-sm hover:shadow-md transition group">
                        <h3
                            class="text-xs font-bold text-gray-500 uppercase tracking-wide group-hover:text-emerald-600 transition">
                            ÇÖZÜLEN / KAPALI</h3>
                        <p class="mt-2 text-3xl font-extrabold text-emerald-500">
                            {{ $raporData['sikayet_genel']['cozumlenen'] }}</p>
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- ZAMAN BAZLI TABLO -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Dönemsel Performans</h3>
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500">Dönem</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-500">Gelen</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-500">Kapanan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900">Bugün</td>
                                    <td class="px-3 py-3 text-right">{{ $raporData['sikayet_zaman']['bugun']['gelen'] }}
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        {{ $raporData['sikayet_zaman']['bugun']['kapanan'] }}
                                        @if($raporData['sikayet_zaman']['bugun']['kapanan'] > $raporData['sikayet_zaman']['bugun']['gelen'])
                                            <span
                                                class="text-emerald-600 text-xs block">(+{{ $raporData['sikayet_zaman']['bugun']['kapanan'] - $raporData['sikayet_zaman']['bugun']['gelen'] }})</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900">Bu Hafta / Geçen H.</td>
                                    <td class="px-3 py-3 text-right">{{ $raporData['sikayet_zaman']['bu_hafta']['gelen'] }}
                                        <span class="text-gray-400">/
                                            {{ $raporData['sikayet_zaman']['gecen_hafta']['gelen'] }}</span></td>
                                    <td class="px-3 py-3 text-right">
                                        {{ $raporData['sikayet_zaman']['bu_hafta']['kapanan'] }} <span
                                            class="text-gray-400">/
                                            {{ $raporData['sikayet_zaman']['gecen_hafta']['kapanan'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900">Bu Ay / Geçen Ay</td>
                                    <td class="px-3 py-3 text-right">{{ $raporData['sikayet_zaman']['bu_ay']['gelen'] }}
                                        <span class="text-gray-400">/
                                            {{ $raporData['sikayet_zaman']['gecen_ay']['gelen'] }}</span></td>
                                    <td class="px-3 py-3 text-right">{{ $raporData['sikayet_zaman']['bu_ay']['kapanan'] }}
                                        <span class="text-gray-400">/
                                            {{ $raporData['sikayet_zaman']['gecen_ay']['kapanan'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900">Bu Yıl / Geçen Yıl</td>
                                    <td class="px-3 py-3 text-right">{{ $raporData['sikayet_zaman']['bu_yil']['gelen'] }}
                                        <span class="text-gray-400">/
                                            {{ $raporData['sikayet_zaman']['gecen_yil']['gelen'] }}</span></td>
                                    <td class="px-3 py-3 text-right">{{ $raporData['sikayet_zaman']['bu_yil']['kapanan'] }}
                                        <span class="text-gray-400">/
                                            {{ $raporData['sikayet_zaman']['gecen_yil']['kapanan'] }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ÇEYREK TABLOSU -->
                    @if(isset($raporData['sikayet_ceyrekler']))
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Çeyrek Bazlı Hedefler</h3>
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500">Çeyrek</th>
                                        <th class="px-3 py-2 text-right font-medium text-gray-500">Gelen</th>
                                        <th class="px-3 py-2 text-right font-medium text-gray-500">Kapanan</th>
                                        <th class="px-3 py-2 text-right font-medium text-gray-500">Başarı</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($raporData['sikayet_ceyrekler'] as $key => $qData)
                                        <tr>
                                            <td class="px-3 py-3 font-medium text-gray-900">{{ date('Y') }} {{ $key }}</td>
                                            <td class="px-3 py-3 text-right">{{ $qData['gelen'] }}</td>
                                            <td class="px-3 py-3 text-right">{{ $qData['kapanan'] }}</td>
                                            <td class="px-3 py-3 text-right">
                                                @if($qData['gelen'] > 0)
                                                    @php $success = round(($qData['kapanan'] / $qData['gelen']) * 100); @endphp
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $success >= 80 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                        %{{ $success }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-300">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- KATEGORİ BAZLI DAĞILIM (DETAY LİNKLERİ İLE) -->
                @if(isset($raporData['sikayet_bolumler']))
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Kategori Bazlı Dağılım</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500">Kategori / Bölüm</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Toplam</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Yeni</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">İşlemde</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Biten</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($raporData['sikayet_bolumler'] as $row)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $row['kategori_adi'] }}</td>
                                            <td class="px-4 py-3 text-center font-bold">{{ $row['toplam'] }}</td>
                                            <td class="px-4 py-3 text-center">
                                                @if($row['yeni'] > 0)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ $row['yeni'] }}</span>
                                                @else <span class="text-gray-300">-</span> @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($row['islemde'] > 0)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">{{ $row['islemde'] }}</span>
                                                @else <span class="text-gray-300">-</span> @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($row['kapali'] > 0)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">{{ $row['kapali'] }}</span>
                                                @else <span class="text-gray-300">-</span> @endif
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                {{-- Kategori Adına Göre Filtreleme --}}
                                                {{-- Not: Controller'da isim ile filtreleme yoksa ID gerekebilir ama şimdilik Search
                                                ile idare edebilir veya ID ekleyebiliriz --}}
                                                <a href="{{ route('admin.sikayetler.index', ['search' => $row['kategori_adi']]) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">
                                                    İncele &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>