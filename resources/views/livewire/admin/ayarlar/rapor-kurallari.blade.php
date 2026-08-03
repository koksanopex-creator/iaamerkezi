<div class="space-y-6">
    {{-- Üst Bar: Başlık ve Ekle Butonu --}}
    <div class="flex justify-between items-center border-b border-gray-200 pb-4">
        <div>
            <h3 class="text-lg font-medium leading-6 text-gray-900">Tanımlı Rapor Kuralları</h3>
            <p class="mt-1 text-sm text-gray-500">Otomatik gönderilecek raporların listesi ve ayarları.</p>
        </div>
        {{-- ÖNEMLİ: type="button" ekledik ki ana formu tetiklemesin --}}
        <button type="button" wire:click="yeniKural" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Yeni Kural Ekle
        </button>
    </div>

    {{-- Başarı Mesajı --}}
    @if (session()->has('success'))
        <div class="rounded-md bg-green-50 p-4 border-l-4 border-green-400">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- TABLO LİSTESİ --}}
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlık</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periyot / Saat</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alıcılar</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İçerik</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">İşlemler</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $grupluKurallar = $kurallar->groupBy(function($kural) {
                                    $icerik = $kural->icerik_ayarlari ?? [];
                                    if (!empty($icerik['sikayet_ozet']) || !empty($icerik['sikayet_detay'])) {
                                        return 'Müşteri Şikayetleri Raporları';
                                    } elseif (!empty($icerik['iaa_ozet']) || !empty($icerik['iaa_havuz'])) {
                                        return 'İyileştirmeye Açık Alan (İAA) Raporları';
                                    } elseif (!empty($icerik['disiplin_ozet'])) {
                                        return 'Disiplin Süreci Raporları';
                                    } elseif (!empty($icerik['arabuluculuk_ozet'])) {
                                        return 'Arabuluculuk Raporları';
                                    } else {
                                        return 'Genel / Karma Raporlar';
                                    }
                                });
                            @endphp

                            @forelse($grupluKurallar as $kategoriAdi => $kategoriKurallari)
                                <tr class="bg-gray-100">
                                    <td colspan="5" class="px-6 py-2 text-xs font-bold text-gray-700 uppercase tracking-wider bg-gray-50 border-y border-gray-200">
                                        {{ $kategoriAdi }}
                                    </td>
                                </tr>
                                @foreach($kategoriKurallari as $kural)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $kural->baslik }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">
                                                {{ $kural->periyot }}
                                            </span>
                                            <div class="text-sm text-gray-500 mt-1">
                                                {{ \Carbon\Carbon::parse($kural->gonderim_saati)->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs text-gray-500">
                                                @php
                                                    $rolIsimleri = collect($kural->alicilar['roller'] ?? [])->map(fn($id) => $rolesMap[$id] ?? 'Bilinmeyen')->implode(', ');
                                                    $kisiIsimleri = collect($kural->alicilar['users'] ?? [])->map(fn($id) => $usersMap[$id] ?? 'Bilinmeyen')->implode(', ');
                                                @endphp
                                                <div class="flex items-center gap-1 cursor-help" title="{{ $rolIsimleri ?: 'Rol seçilmemiş' }}">
                                                    <span class="font-bold border-b border-dashed border-gray-400">Roller:</span> {{ count($kural->alicilar['roller'] ?? []) }}
                                                </div>
                                                <div class="flex items-center gap-1 cursor-help mt-1" title="{{ $kisiIsimleri ?: 'Kişi seçilmemiş' }}">
                                                    <span class="font-bold border-b border-dashed border-gray-400">Kişiler:</span> {{ count($kural->alicilar['users'] ?? []) }}
                                                </div>
                                                @if(!empty($kural->alicilar['emails']))
                                                <div class="flex items-center gap-1 text-amber-600 mt-1 cursor-help" title="{{ is_array($kural->alicilar['emails']) ? implode(', ', $kural->alicilar['emails']) : $kural->alicilar['emails'] }}">
                                                    <span class="font-bold border-b border-dashed border-gray-400">Harici:</span> Var
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($kural->icerik_ayarlari as $key => $val)
                                                    @if($val) 
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ str_replace('_', ' ', ucfirst($key)) }}
                                                        </span> 
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <button type="button" wire:click="manuelGonder({{ $kural->id }})" class="text-amber-600 hover:text-amber-900" title="Şimdi Gönder">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                            </button>
                                            <button type="button" wire:click="duzenle({{ $kural->id }})" class="text-indigo-600 hover:text-indigo-900" title="Düzenle">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button type="button" wire:click="sil({{ $kural->id }})" onclick="confirm('Bu kuralı silmek istediğinize emin misiniz?') || event.stopImmediatePropagation()" class="text-red-600 hover:text-red-900" title="Sil">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Henüz tanımlı bir rapor kuralı bulunmamaktadır.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL (Tailwind CSS Overlay) --}}
     @if($isModalOpen)
    <div class="fixed z-[1060] inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            {{-- Arka plan karartma --}}
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('isModalOpen', false)"></div>

            {{-- Modal İçeriği --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full relative">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[75vh] overflow-y-auto">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        {{ $aktifKuralId ? 'Kuralı Düzenle' : 'Yeni Rapor Kuralı Ekle' }}
                    </h3>
                    
                    <div class="mt-4 space-y-4">
                    {{-- 1. GENEL BİLGİLER --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Rapor Başlığı</label>
                                <input wire:model="baslik" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2 border">
                                @error('baslik') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex gap-2">
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700">Periyot</label>
                                    {{-- DİKKAT: wire:model.live OLARAK DEĞİŞTİRDİM --}}
                                    <select wire:model.live="periyot" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2 border">
                                        <option value="gunluk">Günlük</option>
                                        <option value="haftalik">Haftalık</option>
                                        <option value="aylik">Aylık</option>
                                    </select>
                                </div>
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700">Saat</label>
                                    <input wire:model="gonderim_saati" type="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2 border">
                                </div>
                            </div>

                            {{-- BURADAN AŞAĞISI YENİ EKLENEN KISIM --}}
                            
                            {{-- Haftalık Seçilirse Göster --}}
                            @if($periyot == 'haftalik')
                            <div class="md:col-span-2 bg-purple-50 p-3 rounded-lg border border-purple-100">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Hangi Günler Gönderilsin?</label>
                                <div class="flex flex-wrap gap-3">
                                    @php
                                        $haftaninGunleri = [
                                            'Monday' => 'Pazartesi', 'Tuesday' => 'Salı', 'Wednesday' => 'Çarşamba',
                                            'Thursday' => 'Perşembe', 'Friday' => 'Cuma', 'Saturday' => 'Cumartesi', 'Sunday' => 'Pazar'
                                        ];
                                    @endphp
                                    @foreach($haftaninGunleri as $eng => $tr)
                                        <label class="inline-flex items-center space-x-2 bg-white px-3 py-1.5 rounded border border-gray-200 cursor-pointer hover:border-purple-400">
                                            <input type="checkbox" wire:model="gunler" value="{{ $eng }}" class="rounded text-purple-600 focus:ring-purple-500">
                                            <span class="text-sm text-gray-700">{{ $tr }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Birden fazla gün seçebilirsiniz.</p>
                            </div>
                            @endif

                            {{-- Aylık Seçilirse Göster --}}
                            @if($periyot == 'aylik')
                            <div class="md:col-span-2 bg-blue-50 p-3 rounded-lg border border-blue-100">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Ayın Hangi Günü?</label>
                                <select wire:model="gunler" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                                    @for($i=1; $i<=31; $i++)
                                        <option value="{{ $i }}">{{ $i }}. Gün</option>
                                    @endfor
                                </select>
                            </div>
                            @endif

                            {{-- YENİ EKLENEN KISIM BİTTİ --}}

                        </div>

                        <hr>

                        {{-- 2. ALICI SEÇİMİ --}}
                        <div x-data="{ 
                            initSelect2() {
                                $('.select2-livewire').select2({
                                    placeholder: 'Seçim yapınız...',
                                    width: '100%',
                                    allowClear: true
                                });

                                $('#secili_roller_select').on('change', function (e) {
                                    @this.set('secili_roller', $(this).val());
                                });

                                $('#secili_users_select').on('change', function (e) {
                                    @this.set('secili_users', $(this).val());
                                });

                                // BACKEND'den (Rol seçimiyle) gelen kullanıcı güncellemelerini yakala
                                @this.on('users-updated', (event) => {
                                    $('#secili_users_select').val(event.ids).trigger('change');
                                });
                            }
                        }" x-init="setTimeout(() => initSelect2(), 50)">
                            <h4 class="text-sm font-bold text-gray-700 mb-2">Alıcılar</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div wire:ignore>
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Roller</label>
                                    <select id="secili_roller_select" multiple class="select2-livewire mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2 border">
                                        @foreach($roller as $rol)
                                            <option value="{{ $rol->id }}" @selected(in_array($rol->id, $secili_roller))>{{ $rol->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div wire:ignore>
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Kullanıcılar</label>
                                    <select id="secili_users_select" multiple class="select2-livewire mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2 border">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" @selected(in_array($user->id, $secili_users))>
                                                {{ $user->name }} ({{ $user->bolum->ad ?? 'Dış Personel' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="block text-xs font-medium text-gray-500 uppercase">Harici E-postalar</label>
                                <textarea wire:model="harici_mailler" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2 border" placeholder="ornek@firma.com, diger@firma.com"></textarea>
                            </div>
                        </div>

                        <hr>

                        {{-- 3. İÇERİK SEÇİMİ --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 mb-2">Rapor İçeriği</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <div>
                                    <span class="block text-xs font-bold text-purple-700 mb-1">Şikayet Sistemi</span>
                                    <div class="space-y-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model.live="icerik.sikayet_ozet" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-600">Genel Özet</span>
                                        </label>
                                        <br>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model.live="icerik.sikayet_detay" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-600">Bölüm Dağılımı</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <span class="block text-xs font-bold text-purple-700 mb-1">İAA Projeleri</span>
                                    <div class="space-y-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model.live="icerik.iaa_ozet" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-600">Genel Durum</span>
                                        </label>
                                        <br>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model.live="icerik.iaa_havuz" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-600">Havuz Bekleyen</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <span class="block text-xs font-bold text-purple-700 mb-1">Diğer</span>
                                    <div class="space-y-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model.live="icerik.disiplin_ozet" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-600">Disiplin Özet</span>
                                        </label>
                                        <br>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model.live="icerik.arabuluculuk_ozet" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-600">Arabuluculuk</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- DİSİPLİN ÖZEL AYARLARI (Sadece Disiplin Özet seçiliyse göster) --}}
                        @if(!empty($icerik['disiplin_ozet']) && $icerik['disiplin_ozet'] !== 'false' && $icerik['disiplin_ozet'] !== false)
                        <hr class="my-4">
                        <div x-data="{ 
                            initDisiplinSelect2() {
                                $('.select2-disiplin').select2({
                                    placeholder: 'Tüm Suç Kategorileri (Boş bırakırsanız hepsi gider)',
                                    width: '100%',
                                    allowClear: true
                                });
                                $('#disiplin_suc_select').on('change', function (e) {
                                    @this.set('disiplin_suc_kategorileri', $(this).val());
                                });
                            }
                        }" x-init="setTimeout(() => initDisiplinSelect2(), 50)">
                            <h4 class="text-sm font-bold text-indigo-700 mb-2">Disiplin Raporu Özel Ayarları</h4>
                            <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100 space-y-4">
                                
                                {{-- 1. Veri Kapsamı --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Veri Kapsamı (Kime Ne Gidecek?)</label>
                                    <div class="space-y-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" wire:model.live="disiplin_kapsam" value="tum_veriler" class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-700"><strong>Tüm Veriler:</strong> Seçilen tüm alıcılara şirketin genel disiplin raporu gider. (Örn: Yönetim)</span>
                                        </label>
                                        <br>
                                        <label class="inline-flex items-center">
                                            <input type="radio" wire:model.live="disiplin_kapsam" value="kendi_bolumu" class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-700"><strong>Sadece Kendi Bölümü:</strong> Alıcılara sadece <u class="font-bold">kendi bölümlerinde</u> gerçekleşen olayların raporu gider. (Örn: Bölüm Liderleri)</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- 2. Suç Kategorisi Filtresi --}}
                                <div wire:ignore>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Suç / İhlal Kategorisi Filtresi</label>
                                    <select id="disiplin_suc_select" multiple class="select2-disiplin mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                                        @foreach($disiplinKategorileri as $kat)
                                            <option value="{{ $kat->id }}" @selected(in_array($kat->id, $disiplin_suc_kategorileri))>{{ $kat->ad }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Örn: Sadece İSG ihlallerini göndermek isterseniz İSG'yi seçin. Boş bırakırsanız tüm olaylar gider.</p>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    {{-- ÖNEMLİ: type="button" --}}
                    <button type="button" wire:click="kaydet" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Kaydet
                    </button>
                    <button type="button" wire:click="$set('isModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        İptal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>