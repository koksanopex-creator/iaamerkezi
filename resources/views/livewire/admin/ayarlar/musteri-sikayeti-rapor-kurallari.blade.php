<div class="space-y-6">
    {{-- Üst Bar: Başlık ve Ekle Butonu --}}
    <div class="flex justify-between items-center border-b border-gray-200 pb-4">
        <div>
            <h3 class="text-lg font-medium leading-6 text-gray-900">Kurul Yöneticisi Rapor Kuralları</h3>
            <p class="mt-1 text-sm text-gray-500">Müşteri Şikayeti Kurulu Yöneticileri ve harici kişiler için ekip performans raporu kuralları.</p>
        </div>
        <button type="button" wire:click="yeniKural" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
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
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum & Başlık</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zamanlama</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapsam & Alıcılar</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bildirim</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">İşlemler</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($kurallar as $kural)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                @if($kural->aktif)
                                                    <span class="w-3 h-3 rounded-full bg-green-500 block"></span>
                                                @else
                                                    <span class="w-3 h-3 rounded-full bg-gray-400 block"></span>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $kural->ad }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">
                                            {{ $kural->siklik }}
                                        </span>
                                        <div class="text-sm text-gray-500 mt-1">
                                            {{ \Carbon\Carbon::parse($kural->saat)->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-500 mb-1">
                                            <span class="font-bold">Kapsam:</span> 
                                            @if($kural->rapor_kapsami == 'tum_kurul') Tüm Kurul 
                                            @elseif($kural->rapor_kapsami == 'yurt_ici_kurul') Yurt İçi Kurul
                                            @elseif($kural->rapor_kapsami == 'yurt_disi_kurul') Yurt Dışı Kurul
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <div class="flex items-center gap-1">
                                                <span class="font-bold">Roller:</span> {{ count($kural->alicilar['roller'] ?? []) }}
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span class="font-bold">Kişiler:</span> {{ count($kural->alicilar['users'] ?? []) }}
                                            </div>
                                            @if(!empty($kural->alicilar['emails']))
                                            <div class="flex items-center gap-1 text-amber-600">
                                                <span class="font-bold">Harici:</span> Var
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1 text-xs">
                                            @if($kural->mail_aktif_et) <span class="text-green-600"><i class="fas fa-envelope"></i> Mail Aktif</span> @endif
                                            @if($kural->zili_aktif_et) <span class="text-green-600"><i class="fas fa-bell"></i> Zil Aktif</span> @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <button type="button" wire:click="onizleme({{ $kural->id }})" class="text-emerald-600 hover:text-emerald-900" title="Önizleme">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
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

    {{-- MODAL --}}
    @if($isModalOpen)
    <div class="fixed z-[1060] inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('isModalOpen', false)"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full relative">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[85vh] overflow-y-auto">
                    
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ $aktifKuralId ? 'Kuralı Düzenle' : 'Yeni Rapor Kuralı Ekle' }}
                        </h3>
                        <button type="button" wire:click="openPreview" class="bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-1.5 rounded-md text-sm font-semibold border border-blue-200">
                            <i class="fas fa-eye"></i> Önizleme Göster
                        </button>
                    </div>
                    
                    <div class="mt-4 space-y-6">
                        
                        {{-- 1. GENEL BİLGİLER --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kural Adı</label>
                                <input wire:model="ad" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                                @error('ad') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex items-center mt-6">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input wire:model="aktif" type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Kural Aktif</span>
                                </label>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700">Gönderim Sıklığı</label>
                                    <select wire:model.live="siklik" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                                        <option value="gunluk">Günlük</option>
                                        <option value="haftalik">Haftalık</option>
                                        <option value="aylik">Aylık</option>
                                    </select>
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700">Periyot</label>
                                    <input wire:model="periyot" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                                    <p class="text-[10px] text-gray-400 mt-1">Örn: 2 seçilirse 2 {{ $siklik == 'gunluk' ? 'günde' : ($siklik == 'haftalik' ? 'haftada' : 'ayda') }} bir gönderilir.</p>
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700">Gönderim Saati</label>
                                    <input wire:model="saat" type="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                                </div>
                            </div>
                            
                            @if($siklik == 'haftalik')
                            <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Hangi Günler?</label>
                                <div class="flex flex-wrap gap-3">
                                    @php
                                        $haftaninGunleriTr = [
                                            'Pazartesi', 'Sali', 'Carsamba', 'Persembe', 'Cuma', 'Cumartesi', 'Pazar'
                                        ];
                                    @endphp
                                    @foreach($haftaninGunleriTr as $gun)
                                        <label class="inline-flex items-center space-x-2 bg-white px-3 py-1.5 rounded border border-gray-200 cursor-pointer hover:border-indigo-400">
                                            <input type="checkbox" wire:model="haftanin_gunleri" value="{{ $gun }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">{{ $gun }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @elseif($siklik == 'aylik')
                            <div class="bg-indigo-50/50 p-4 sm:p-6 rounded-2xl border border-indigo-100 shadow-inner" x-data="{ 
                                selectedDays: @entangle('ayin_gunleri'),
                                toggle(val) {
                                    if (!this.selectedDays) this.selectedDays = [];
                                    let strDays = this.selectedDays.map(String);
                                    let strVal = String(val);
                                    if (strDays.includes(strVal)) {
                                        this.selectedDays = this.selectedDays.filter(d => String(d) !== strVal);
                                    } else {
                                        this.selectedDays.push(val);
                                    }
                                },
                                isSelected(val) {
                                    if (!this.selectedDays) return false;
                                    return this.selectedDays.map(String).includes(String(val));
                                }
                            }">
                                <div class="flex items-center justify-between mb-4">
                                    <label class="block text-base font-black text-indigo-900">Ayın Hangi Günleri?</label>
                                    <button type="button" @click="selectedDays = []" class="text-xs font-bold text-indigo-500 hover:text-indigo-700 hover:underline">Seçimleri Temizle</button>
                                </div>
                                
                                <div class="grid grid-cols-7 gap-2 sm:gap-3">
                                    @for($i = 1; $i <= 31; $i++)
                                        <button type="button" 
                                                @click="toggle({{ $i }})"
                                                :class="isSelected({{ $i }}) ? 'bg-indigo-600 text-white shadow-lg ring-2 ring-indigo-300 border-transparent transform scale-[1.03]' : 'bg-white text-gray-700 border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 hover:shadow-sm'"
                                                class="h-11 w-full flex items-center justify-center rounded-xl border font-bold text-sm sm:text-base transition-all duration-200 focus:outline-none focus:ring-0">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                    <button type="button" 
                                            @click="toggle('son_gun')"
                                            :class="isSelected('son_gun') ? 'bg-indigo-600 text-white shadow-lg ring-2 ring-indigo-300 border-transparent transform scale-[1.03]' : 'bg-white text-gray-700 border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 hover:shadow-sm'"
                                            class="col-span-3 h-11 w-full flex items-center justify-center rounded-xl border font-bold text-sm transition-all duration-200 focus:outline-none focus:ring-0">
                                        Son Gün
                                    </button>
                                </div>
                                
                                <div class="mt-5 flex items-start gap-3 bg-indigo-100/50 p-4 rounded-xl border border-indigo-200/60">
                                    <svg class="w-5 h-5 text-indigo-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-xs text-indigo-900 font-medium leading-relaxed">
                                        Hiçbir gün seçmezseniz, rapor kuralı otomatik olarak <strong>ayın 1'inde</strong> işletilir. Çoklu seçim yaparak ayın farklı günlerinde çalışmasını sağlayabilirsiniz.
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <hr>

                        {{-- 2. ALICILAR VE KAPSAM --}}
                        <div x-data="{ 
                            initSelect2() {
                                $('.select2-livewire-rapor').select2({
                                    placeholder: 'Seçim yapınız...',
                                    width: '100%',
                                    allowClear: true
                                });

                                $('#secili_roller_select_rapor').on('change', function (e) {
                                    @this.set('secili_roller', $(this).val());
                                });

                                $('#secili_users_select_rapor').on('change', function (e) {
                                    @this.set('secili_users', $(this).val());
                                });

                                @this.on('users-updated', (event) => {
                                    $('#secili_users_select_rapor').val(event.ids).trigger('change');
                                });
                            }
                        }" x-init="setTimeout(() => initSelect2(), 50)">
                            
                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mb-4">
                                <h4 class="text-sm font-bold text-blue-900 mb-2"><i class="fas fa-sitemap mr-1"></i> Rapor Kapsamı (Performansı Gösterilecek Ekip)</h4>
                                <p class="text-xs text-blue-700 mb-3">Harici e-postalar ve seçilen kullanıcılar bu raporda hangi ekibin performansını görecek?</p>
                                <select wire:model.live="rapor_kapsami" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                                    <option value="tum_kurul">Tüm Müşteri Şikayeti Kurulu</option>
                                    <option value="yurt_ici_kurul">Sadece Yurt İçi Kurul Üyeleri</option>
                                    <option value="yurt_disi_kurul">Sadece Yurt Dışı Kurul Üyeleri</option>
                                </select>
                            </div>

                            <h4 class="text-sm font-bold text-gray-700 mb-2">Ekstra Alıcılar</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div wire:ignore>
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Roller</label>
                                    <select id="secili_roller_select_rapor" multiple class="select2-livewire-rapor mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                                        @foreach($roller as $rol)
                                            <option value="{{ $rol->id }}" @selected(in_array($rol->id, $secili_roller))>{{ $rol->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div wire:ignore>
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Kullanıcılar</label>
                                    <select id="secili_users_select_rapor" multiple class="select2-livewire-rapor mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
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
                                <textarea wire:model="harici_mailler" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" placeholder="ornek@firma.com, diger@firma.com"></textarea>
                            </div>
                        </div>

                        <hr>

                        {{-- 3. BİLDİRİM VE MAİL AYARLARI --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 mb-4">Bildirim / E-posta İçerik Ayarları</h4>
                            
                            <div class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <label class="inline-flex items-center cursor-pointer mb-3">
                                        <input wire:model="zili_aktif_et" type="checkbox" class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ml-3 text-sm font-bold text-gray-900"><i class="fas fa-bell text-amber-500"></i> Sistem İçi Zil Bildirimi Gönder</span>
                                    </label>
                                    <div class="ml-12">
                                        <label class="block text-xs text-gray-500">Zil Bildirimi Metni</label>
                                        <input wire:model="bildirim_metni" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" placeholder="Örn: Haftalık Ekip Performans Raporu">
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <label class="inline-flex items-center cursor-pointer mb-3">
                                        <input wire:model="mail_aktif_et" type="checkbox" class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ml-3 text-sm font-bold text-gray-900"><i class="fas fa-envelope text-blue-500"></i> E-Posta Gönder</span>
                                    </label>
                                    <div class="ml-12 space-y-3">
                                        <div>
                                            <label class="block text-xs text-gray-500">Mail Konusu</label>
                                            <input wire:model="mail_konusu" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" placeholder="Örn: Müşteri Şikayetleri Yönetici Raporu">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500">Mail Gövdesi (Taslak/Ön Yazı)</label>
                                            <textarea wire:model="mail_taslagi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" placeholder="Örn: Ekibinize ait güncel performans durumunu aşağıda görebilirsiniz."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="kaydet" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
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

    {{-- ÖNİZLEME MODALI --}}
    @if($isPreviewModalOpen)
    <div class="fixed z-[1070] inset-0 overflow-y-auto" aria-labelledby="preview-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity" aria-hidden="true" wire:click="closePreview"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-gray-100 rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full relative">
                
                <div class="bg-white px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900" id="preview-modal-title">
                        Rapor Önizlemesi
                    </h3>
                    <button type="button" wire:click="closePreview" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Kapat</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[75vh]">
                    
                    @if($previewData['zil_aktif'])
                    <div class="mb-6">
                        <h4 class="text-sm font-bold text-gray-500 mb-2 uppercase">Zil Bildirimi Önizlemesi</h4>
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex items-start gap-4">
                            <div class="p-2 bg-indigo-100 rounded-full text-indigo-600">
                                <i class="fas fa-chart-line text-xl"></i>
                            </div>
                            <div>
                                <h5 class="text-sm font-bold text-gray-900">{{ $previewData['bildirim_metni'] }}</h5>
                                <p class="text-xs text-gray-500 mt-1">Bildirime tıklandığında ekranda yandaki gibi bir performans tablosu açılır.</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($previewData['mail_aktif'])
                    <div>
                        <h4 class="text-sm font-bold text-gray-500 mb-2 uppercase">E-Posta Önizlemesi</h4>
                        
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-100 px-4 py-2 border-b border-gray-200 text-sm">
                                <strong>Konu:</strong> {{ $previewData['mail_konusu'] }}
                            </div>
                            
                            <div class="p-6">
                                <div class="text-center mb-6">
                                    <h1 class="text-2xl font-bold text-gray-800" style="color: #2c3e50;">Ekip Performans Raporu</h1>
                                </div>
                                
                                <p class="text-gray-700 mb-6" style="color: #34495e; line-height: 1.6;">
                                    {!! nl2br(e($previewData['mail_taslagi'])) !!}
                                </p>
                                
                                <div style="margin-top: 30px;">
                                    <h2 style="font-size: 18px; color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 8px; margin-bottom: 15px;">Güncel Performans Tablosu</h2>
                                    
                                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-family: sans-serif; font-size: 14px;">
                                        <thead>
                                            <tr style="background-color: #f8f9fa;">
                                                <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: left; color: #495057;">Personel</th>
                                                <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: center; color: #495057;">Toplam Şikayet</th>
                                                <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: center; color: #495057;">Son 7 Gün</th>
                                                <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: center; color: #495057;">Çözümlenen Şikayet</th>
                                                <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: center; color: #495057;">İptal / Reddedilen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($previewData['ekip_performansi'] as $uye)
                                            <tr>
                                                <td style="padding: 12px; border-bottom: 1px solid #dee2e6;">{{ $uye->name }}</td>
                                                <td style="padding: 12px; border-bottom: 1px solid #dee2e6; text-align: center;"><strong>{{ $uye->toplam }}</strong></td>
                                                <td style="padding: 12px; border-bottom: 1px solid #dee2e6; text-align: center;">{{ $uye->son_7_gun }}</td>
                                                <td style="padding: 12px; border-bottom: 1px solid #dee2e6; text-align: center; color: #2ecc71;">{{ $uye->cozumlenen }}</td>
                                                <td style="padding: 12px; border-bottom: 1px solid #dee2e6; text-align: center; color: #e74c3c;">{{ $uye->iptal_red }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" style="padding: 12px; text-align: center; color: #7f8c8d;">Görüntülenecek ekip performansı bulunamadı.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
                
                <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="closePreview" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
