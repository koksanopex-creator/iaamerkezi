@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ isset($kural) ? __('Kuralı Düzenle') : __('Yeni Hatırlatıcı Kuralı') }}
    </h2>
@endsection

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center space-x-4 mb-8">
            <a href="{{ route('admin.sikayet-hatirlaticilar.index') }}" class="p-2 bg-white rounded-full shadow-sm hover:bg-slate-100 transition-all text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase">{{ isset($kural) ? 'Kuralı Düzenle' : 'Yeni Kural Oluştur' }}</h1>
                <p class="text-sm text-slate-500 font-medium italic">Otomatik bildirim kriterlerini belirleyin</p>
            </div>
        </div>

        <form action="{{ isset($kural) ? route('admin.sikayet-hatirlaticilar.update', $kural->id) : route('admin.sikayet-hatirlaticilar.store') }}" method="POST">
            @csrf
            @if(isset($kural)) @method('PUT') @endif

            <div class="space-y-6">
                
                {{-- Temel Bilgiler --}}
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Kural Adı</label>
                            <input type="text" name="ad" value="{{ old('ad', $kural->ad ?? '') }}" required placeholder="Örn: Bekleyen Şikayetler İçin Günlük Uyarı"
                                   class="block w-full rounded-2xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-slate-700">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Hatırlatma Sıklığı</label>
                            <select name="siklik" id="siklik_select" class="block w-full rounded-2xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-slate-700">
                                <option value="gunluk" {{ old('siklik', $kural->siklik ?? '') == 'gunluk' ? 'selected' : '' }}>Her Gün</option>
                                <option value="haftalik" {{ old('siklik', $kural->siklik ?? '') == 'haftalik' ? 'selected' : '' }}>Haftalık (Belirli Günler)</option>
                                <option value="aylik" {{ old('siklik', $kural->siklik ?? '') == 'aylik' ? 'selected' : '' }}>Her Ay (Ayın 1'i)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Gönderim Saati</label>
                            <input type="time" name="saat" value="{{ old('saat', $kural->saat ?? '09:00') }}" required
                                   class="block w-full rounded-2xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-slate-700">
                        </div>

                        <div class="md:col-span-2 {{ old('siklik', $kural->siklik ?? '') == 'haftalik' ? '' : 'hidden' }}" id="days_container">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Haftanın Günleri</label>
                            <div class="flex flex-wrap gap-2">
                                @php $days = ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar']; @endphp
                                @foreach($days as $index => $day)
                                    <label class="inline-flex items-center p-3 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer hover:bg-white hover:border-indigo-300 transition-all group">
                                        <input type="checkbox" name="haftanin_gunleri[]" value="{{ $index + 1 }}" 
                                               {{ in_array($index + 1, old('haftanin_gunleri', $kural->haftanin_gunleri ?? [])) ? 'checked' : '' }}
                                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm font-bold text-slate-600 group-hover:text-indigo-600">{{ $day }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kriterler --}}
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 p-8">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight mb-6">Filtre Kriterleri</h3>
                    
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Hangi Durumdaki Şikayetlere Uygulansın?</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-8">
                        @php 
                            $durumlar = ['Yeni', 'Atandı', 'İşlemde', 'İnceleniyor', 'Devam Ediyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Hukuk Onayı Bekliyor', 'Durduruldu']; 
                        @endphp
                        @foreach($durumlar as $durum)
                            <label class="inline-flex items-center p-2 rounded-lg hover:bg-slate-50 transition-all cursor-pointer">
                                <input type="checkbox" name="proje_durumlari[]" value="{{ $durum }}"
                                       {{ in_array($durum, old('proje_durumlari', $kural->proje_durumlari ?? [])) ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-xs font-bold text-slate-600">{{ $durum }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                        <div>
                            <h4 class="text-sm font-black text-indigo-800">Kural Aktif mi?</h4>
                            <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-widest">Pasif kurallar scheduler tarafından tetiklenmez</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="aktif" value="1" {{ old('aktif', $kural->aktif ?? true) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                </div>

                {{-- Bildirim Hedefleri --}}
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 p-8">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight mb-6">Kime Bildirim Gitsin?</h3>

                    <div class="space-y-4 mb-8">
                        @php 
                            $roller = [
                                'Müşteri Şikayeti Çözüm Lideri' => 'İlgili şikayetin çözüm lideri',
                                'Bölüm Kalite Yöneticisi' => 'İlgili bölümün kalite yöneticisi',
                                'Bölüm Lideri' => 'İlgili bölümün lideri (Müdür)',
                                'Direktör' => 'İlgili bölümün direktörü',
                                'Yonetim' => 'Genel yönetim kadrosu'
                            ]; 
                        @endphp
                        @foreach($roller as $rol => $desc)
                            <label class="flex items-center justify-between p-3 rounded-2xl border border-slate-100 hover:border-indigo-100 hover:bg-slate-50 transition-all cursor-pointer">
                                <div class="flex items-center">
                                    <input type="checkbox" name="bildirim_rolleri[]" value="{{ $rol }}"
                                           {{ in_array($rol, old('bildirim_rolleri', $kural->bildirim_rolleri ?? [])) ? 'checked' : '' }}
                                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <div class="ml-3">
                                        <p class="text-xs font-black text-slate-800 uppercase tracking-tight">{{ $rol }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium">{{ $desc }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center p-4 bg-rose-50 rounded-2xl border border-rose-100 cursor-pointer">
                            <input type="checkbox" name="sikayeti_girene_bildir" value="1" {{ old('sikayeti_girene_bildir', $kural->sikayeti_girene_bildir ?? false) ? 'checked' : '' }} class="rounded text-rose-600">
                            <span class="ml-3 text-xs font-black text-rose-800 uppercase tracking-tight">Şikayeti Giren Personel</span>
                        </label>
                        <label class="flex items-center p-4 bg-emerald-50 rounded-2xl border border-emerald-100 cursor-pointer">
                            <input type="checkbox" name="musteriye_bildir" value="1" {{ old('musteriye_bildir', $kural->musteriye_bildir ?? false) ? 'checked' : '' }} class="rounded text-emerald-600">
                            <span class="ml-3 text-xs font-black text-emerald-800 uppercase tracking-tight">Müşterinin Kendisi</span>
                        </label>
                    </div>

                    <div class="mt-8">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Ekstra Bildirilecek Kişiler</label>
                        <select name="ek_kullanici_ids[]" multiple class="block w-full rounded-2xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-slate-700 select2-searchable">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ in_array($user->id, old('ek_kullanici_ids', $kural->ek_kullanici_ids ?? [])) ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->getRoleNames()->first() ?? 'Rol Yok' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-[10px] text-slate-400 italic">Birden fazla seçim yapabilirsiniz. Bu kişilere de her durumda bildirim gider.</p>
                    </div>
                </div>

                {{-- Bildirim İçeriği --}}
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 p-8">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight mb-6">Bildirim İçeriği ve Mail Taslağı</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Mail Konusu</label>
                            <input type="text" name="mail_konusu" value="{{ old('mail_konusu', $kural->mail_konusu ?? '') }}" placeholder="Örn: {sikayet_konusu} Hakkında Hatırlatma"
                                   class="block w-full rounded-2xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-slate-700">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Mail Taslağı (Body)</label>
                            <textarea name="mail_taslagi" rows="4" placeholder="Mail içeriğini buraya yazın..."
                                      class="block w-full rounded-2xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-slate-700">{{ old('mail_taslagi', $kural->mail_taslagi ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Zil Bildirimi Metni (Kısa)</label>
                            <input type="text" name="bildirim_metni" value="{{ old('bildirim_metni', $kural->bildirim_metni ?? '') }}" placeholder="Örn: {musteri_adi} şikayeti için hatırlatma bekliyor."
                                   class="block w-full rounded-2xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-slate-700">
                        </div>

                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Kullanılabilir Değişkenler</h4>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-2 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-indigo-600">{sikayet_konusu}</span>
                                <span class="px-2 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-indigo-600">{musteri_adi}</span>
                                <span class="px-2 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-indigo-600">{firma_adi}</span>
                                <span class="px-2 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-indigo-600">{tarih}</span>
                                <span class="px-2 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-indigo-600">{sikayet_durumu}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6">
                    <button type="submit" class="inline-flex items-center px-10 py-4 bg-indigo-600 text-white text-sm font-black rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all hover:-translate-y-1 active:translate-y-0">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ isset($kural) ? 'GÜNCELLEMELERİ KAYDET' : 'KURALI OLUŞTUR VE YAYINLA' }}
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            border-color: #e2e8f0 !important;
            border-radius: 1rem !important;
            padding: 8px !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #6366f1 !important;
            ring: 2px !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-searchable').select2({
                placeholder: "Kullanıcı arayın...",
                allowClear: true,
                width: '100%'
            });
        });

        document.getElementById('siklik_select').addEventListener('change', function() {
            const container = document.getElementById('days_container');
            if (this.value === 'haftalik') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        });
    </script>
@endpush
@endsection
