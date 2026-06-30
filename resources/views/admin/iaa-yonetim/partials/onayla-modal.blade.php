<x-modal name="onayla-modal-{{ $iaa->id }}" focusable>
    {{-- SİZİN ÇALIŞAN VERİ AKTARIM YÖNTEMİNİZ KORUNDU --}}
    <div x-data="approvalForm({
        risk: '{{ $iaa->risk }}',
        kazanc_miktar: '{{ $iaa->kazanc_miktar }}',
        kazanc_birim: '{{ $iaa->kazanc_birim }}',
        butce_miktar: '{{ $iaa->butce_miktar }}',
        butce_birim: '{{ $iaa->butce_birim }}',
        yil_baz: '{{ $iaa->yil_baz }}',
        oneren_kazanc_miktar: '{{ $iaa->oneren_kazanc_miktar }}',
        oneren_kazanc_birim: '{{ $iaa->oneren_kazanc_birim }}',
        oneren_butce_miktar: '{{ $iaa->oneren_butce_miktar }}',
        oneren_butce_birim: '{{ $iaa->oneren_butce_birim }}'
    })">
        <form method="post" action="{{ route('admin.iaa-yonetim.onayla', $iaa) }}" class="p-6">
            @csrf
            @method('patch')

            {{-- ======================== YENİ GÖRSEL TASARIM ======================== --}}
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Öneriyi Onayla ve Puanla</h2>
                    <p class="mt-1 text-sm text-gray-600">"{{ Str::limit($iaa->baslik, 30) }}" önerisini puanlayın.</p>
                </div>

                {{-- Öneri Sahibi Hesaplarını Doldur Butonu (sadece veri varsa görünür) --}}
                <template x-if="hasProposerValues">
                    <button type="button" @click="useProposerValues()" 
                            title="Öneriyi gönderen kullanıcının girdiği tahmini finansal değerleri forma otomatik olarak uygular."
                            class="flex-shrink-0 ml-4 inline-flex items-center space-x-2 bg-blue-100 text-blue-800 font-semibold text-xs py-1 px-3 rounded-full hover:bg-blue-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{-- İkon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                        </svg>
                        <span>Öneren Verilerini Kullan</span>
                    </button>
                </template>
            </div>
            {{-- ======================== YENİ GÖRSEL TASARIM SONU ======================== --}}

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Risk --}}
                <div>
                    <x-input-label for="risk_{{ $iaa->id }}" value="Risk (1-5 Arası)" />
                    <select name="risk" x-model="risk" id="risk_{{ $iaa->id }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="1">1 (Çok Düşük)</option>
                        <option value="2">2 (Düşük)</option>
                        <option value="3">3 (Orta)</option>
                        <option value="4">4 (Yüksek)</option>
                        <option value="5">5 (Çok Yüksek)</option>
                    </select>
                </div>
                <div></div>

                {{-- Kazanç --}}
                <div>
                    <x-input-label for="kazanc_miktar_{{ $iaa->id }}" value="Tahmini Yıllık Kazanç" />
                    <x-text-input type="number" step="0.01" name="kazanc_miktar" x-model="kazanc_miktar" id="kazanc_miktar_{{ $iaa->id }}" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="kazanc_birim_{{ $iaa->id }}" value="Birim" />
                    <select name="kazanc_birim" x-model="kazanc_birim" id="kazanc_birim_{{ $iaa->id }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                         @foreach($paraBirimleri as $birim)<option value="{{ $birim }}">{{ $birim }}</option>@endforeach
                    </select>
                </div>

                {{-- Bütçe --}}
                <div>
                    <x-input-label for="butce_miktar_{{ $iaa->id }}" value="Tahmini Bütçe (Tek Seferlik)" />
                    <x-text-input type="number" step="0.01" name="butce_miktar" x-model="butce_miktar" id="butce_miktar_{{ $iaa->id }}" class="mt-1 block w-full" />
                </div>
                 <div>
                    <x-input-label for="butce_birim_{{ $iaa->id }}" value="Birim" />
                    <select name="butce_birim" x-model="butce_birim" id="butce_birim_{{ $iaa->id }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        @foreach($paraBirimleri as $birim)<option value="{{ $birim }}">{{ $birim }}</option>@endforeach
                    </select>
                </div>

                {{-- PUANLAMA SÜRESİ --}}
                <div class="col-span-2">
                    <x-input-label for="yil_baz_input_{{ $iaa->id }}" value="Puanlama Süresi (Yıl)" />
                    <div class="mt-1 flex items-center gap-3">
                        {{-- Hızlı Seçim Butonları --}}
                        <div class="grid grid-cols-4 gap-2 flex-grow">
                            @foreach([1 => '1 Yıl', 3 => '3 Yıl', 5 => '5 Yıl', 10 => '10 Yıl'] as $yil => $etiket)
                            <button type="button" 
                                    @click="yil_baz = {{ $yil }}" 
                                    class="text-center px-2 py-2 rounded-lg border-2 text-xs font-semibold transition-all focus:outline-none"
                                    :class="yil_baz == {{ $yil }} 
                                        ? 'bg-indigo-600 border-indigo-600 text-white shadow-md' 
                                        : 'bg-white border-gray-200 text-gray-600 hover:border-indigo-300'">
                                {{ $etiket }}
                            </button>
                            @endforeach
                        </div>
                        {{-- Manuel Seçim --}}
                        <div class="w-24">
                            <input type="number" min="1" max="50" x-model.number="yil_baz" id="yil_baz_input_{{ $iaa->id }}"
                                   class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs py-2 text-center" 
                                   placeholder="Diğer" />
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Seçtiğiniz süre boyunca elde edilecek toplam kazanç esas alınarak puan hesaplanır.</p>
                    <input type="hidden" name="yil_baz" :value="yil_baz">
                </div>
            </div>

            {{-- DİNAMİK PUAN & ROI ANALİZİ --}}
            <div class="mt-5 rounded-xl overflow-hidden border border-indigo-200">
                {{-- Başlık + Puan --}}
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span class="text-sm font-semibold text-white">
                            Hesaplanan Öneri Puanı
                            <template x-if="parseFloat(kazanc_miktar) > 0 && parseFloat(butce_miktar) > 0">
                                <span class="text-indigo-200 text-xs font-normal" x-text="'(' + yil_baz + ' yıl baz alındı)'" ></span>
                            </template>
                        </span>
                    </div>
                    <div class="text-right">
                        <template x-if="dynamicPuan !== null">
                            <span class="text-2xl font-extrabold text-white" x-text="dynamicPuan"></span>
                        </template>
                        <template x-if="dynamicPuan === null">
                            <span class="text-sm text-indigo-200 font-medium">Tüm alanları doldurun</span>
                        </template>
                    </div>
                </div>

                {{-- ROI Tablosu (kazanç ve bütçe doluysa göster) --}}
                <template x-if="parseFloat(kazanc_miktar) > 0 && parseFloat(butce_miktar) > 0">
                    <div>
                        {{-- Yıllık Projeksiyon Tablosu --}}
                        <div class="bg-white px-4 pt-3 pb-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Kümülatif Kazanç Projeksiyonu</p>
                            <div class="grid grid-cols-3 gap-2">
                                {{-- 1 Yıl --}}
                                <div class="rounded-lg p-2 text-center transition-all"
                                     :class="[
                                        roi1 >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200',
                                        yil_baz == 1 ? 'ring-2 ring-indigo-500 ring-offset-1' : ''
                                     ]">
                                    <p class="text-[10px] font-semibold uppercase" :class="roi1 >= 0 ? 'text-green-600' : 'text-red-600'">1 Yıl</p>
                                    <p class="text-sm font-extrabold" :class="roi1 >= 0 ? 'text-green-800' : 'text-red-700'"
                                       x-text="(roi1 >= 0 ? '+' : '') + roi1.toLocaleString('tr-TR') + ' ' + kazanc_birim"></p>
                                    <p class="text-[9px] mt-0.5" :class="roi1 >= 0 ? 'text-green-500' : 'text-red-400'"
                                       x-text="roi1 >= 0 ? 'Kârda ✓' : 'Zararda'"></p>
                                    <p class="text-[8px] text-gray-400 mt-1 border-t border-dashed border-gray-200 pt-0.5">(1 × Yıllık Kazanç - Bütçe)</p>
                                </div>
                                {{-- 5 Yıl --}}
                                <div class="rounded-lg p-2 text-center transition-all"
                                     :class="[
                                        roi5 >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200',
                                        yil_baz == 5 ? 'ring-2 ring-indigo-500 ring-offset-1' : ''
                                     ]">
                                    <p class="text-[10px] font-semibold uppercase" :class="roi5 >= 0 ? 'text-green-600' : 'text-red-600'">5 Yıl</p>
                                    <p class="text-sm font-extrabold" :class="roi5 >= 0 ? 'text-green-800' : 'text-red-700'"
                                       x-text="(roi5 >= 0 ? '+' : '') + roi5.toLocaleString('tr-TR') + ' ' + kazanc_birim"></p>
                                    <p class="text-[9px] mt-0.5" :class="roi5 >= 0 ? 'text-green-500' : 'text-red-400'"
                                       x-text="roi5 >= 0 ? 'Kârda ✓' : 'Zararda'"></p>
                                    <p class="text-[8px] text-gray-400 mt-1 border-t border-dashed border-gray-200 pt-0.5">(5 × Yıllık Kazanç - Bütçe)</p>
                                </div>
                                {{-- 10 Yıl --}}
                                <div class="rounded-lg p-2 text-center transition-all"
                                     :class="[
                                        roi10 >= 0 ? 'bg-emerald-50 border border-emerald-200' : 'bg-red-50 border border-red-200',
                                        yil_baz == 10 ? 'ring-2 ring-indigo-500 ring-offset-1' : ''
                                     ]">
                                    <p class="text-[10px] font-semibold uppercase" :class="roi10 >= 0 ? 'text-emerald-600' : 'text-red-600'">10 Yıl</p>
                                    <p class="text-sm font-extrabold" :class="roi10 >= 0 ? 'text-emerald-800' : 'text-red-700'"
                                       x-text="(roi10 >= 0 ? '+' : '') + roi10.toLocaleString('tr-TR') + ' ' + kazanc_birim"></p>
                                    <p class="text-[9px] mt-0.5" :class="roi10 >= 0 ? 'text-emerald-500' : 'text-red-400'"
                                       x-text="roi10 >= 0 ? 'Kârda ✓' : 'Zararda'"></p>
                                    <p class="text-[8px] text-gray-400 mt-1 border-t border-dashed border-gray-200 pt-0.5">(10 × Yıllık Kazanç - Bütçe)</p>
                                </div>
                            </div>
                        </div>

                        {{-- Amortisman Bilgisi --}}
                        <div class="bg-amber-50 border-t border-amber-100 px-4 py-2 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs text-amber-700">
                                <span class="font-semibold">Tahmini amortisman:</span>
                                <span x-text="amortisman"></span>
                            </p>
                        </div>
                    </div>
                </template>

                {{-- Formül Açıklaması --}}
                <div class="bg-indigo-50 px-4 py-2 border-t border-indigo-100">
                    <p class="text-xs text-indigo-600">
                        Puan formülü: <span class="font-mono font-semibold">round((Risk × Yıllık Kazanç × N) ÷ Bütçe)</span>
                        — <span x-text="'N = ' + yil_baz + ' yıl (seçtiğiniz ufuk)'"></span>.
                        Bütçe tek seferlik yatırım, kazanç yıllıktır.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">İptal</x-secondary-button>
                <button type="submit" class="ms-3 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">Onayla ve Puanla</button>
            </div>
        </form>
    </div>
</x-modal>

{{-- ============================================================== --}}
{{-- === YENİ "PUANI DÜZENLE" MODAL'INI BURAYA YAPIŞTIRIN === --}}
{{-- ============================================================== --}}

<x-modal name="puan-duzenle-modal-{{ $iaa->id }}" focusable>
    <div x-data="approvalForm({
        risk: '{{ old('risk', $iaa->risk) }}',
        kazanc_miktar: '{{ old('kazanc_miktar', $iaa->kazanc_miktar) }}',
        kazanc_birim: '{{ old('kazanc_birim', $iaa->kazanc_birim) }}',
        butce_miktar: '{{ old('butce_miktar', $iaa->butce_miktar) }}',
        butce_birim: '{{ old('butce_birim', $iaa->butce_birim) }}',
        yil_baz: '{{ old('yil_baz', $iaa->yil_baz) }}',
        oneren_kazanc_miktar: '{{ $iaa->oneren_kazanc_miktar }}',
        oneren_kazanc_birim: '{{ $iaa->oneren_kazanc_birim }}',
        oneren_butce_miktar: '{{ $iaa->oneren_butce_miktar }}',
        oneren_butce_birim: '{{ $iaa->oneren_butce_birim }}'
    })">
        <form method="post" action="{{ route('admin.iaa-yonetim.updateScore', $iaa) }}" class="p-6">
            @csrf
            @method('PATCH')

            <h2 class="text-lg font-medium text-gray-900">
                "{{ Str::limit($iaa->baslik, 30) }}" Puanını Düzenle
            </h2>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Risk --}}
                <div>
                    <x-input-label for="risk_edit_{{ $iaa->id }}" value="Risk (1-5 Arası)" />
                    <select name="risk" x-model="risk" id="risk_edit_{{ $iaa->id }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="1">1 (Çok Düşük)</option>
                        <option value="2">2 (Düşük)</option>
                        <option value="3">3 (Orta)</option>
                        <option value="4">4 (Yüksek)</option>
                        <option value="5">5 (Çok Yüksek)</option>
                    </select>
                </div>
                <div></div>

                {{-- Kazanç --}}
                <div>
                    <x-input-label for="kazanc_miktar_edit_{{ $iaa->id }}" value="Tahmini Yıllık Kazanç" />
                    <x-text-input type="number" step="0.01" name="kazanc_miktar" x-model="kazanc_miktar" id="kazanc_miktar_edit_{{ $iaa->id }}" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="kazanc_birim_edit_{{ $iaa->id }}" value="Birim" />
                    <select name="kazanc_birim" x-model="kazanc_birim" id="kazanc_birim_edit_{{ $iaa->id }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        @foreach($paraBirimleri as $birim)<option value="{{ $birim }}">{{ $birim }}</option>@endforeach
                    </select>
                </div>

                {{-- Bütçe --}}
                <div>
                    <x-input-label for="butce_miktar_edit_{{ $iaa->id }}" value="Tahmini Bütçe (Tek Seferlik)" />
                    <x-text-input type="number" step="0.01" name="butce_miktar" x-model="butce_miktar" id="butce_miktar_edit_{{ $iaa->id }}" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="butce_birim_edit_{{ $iaa->id }}" value="Birim" />
                    <select name="butce_birim" x-model="butce_birim" id="butce_birim_edit_{{ $iaa->id }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                         @foreach($paraBirimleri as $birim)<option value="{{ $birim }}">{{ $birim }}</option>@endforeach
                    </select>
                </div>

                {{-- PUANLAMA SÜRESİ --}}
                <div class="col-span-2">
                    <x-input-label for="yil_baz_edit_input_{{ $iaa->id }}" value="Puanlama Süresi (Yıl)" />
                    <div class="mt-1 flex items-center gap-3">
                        {{-- Hızlı Seçim Butonları --}}
                        <div class="grid grid-cols-4 gap-2 flex-grow">
                            @foreach([1 => '1 Yıl', 3 => '3 Yıl', 5 => '5 Yıl', 10 => '10 Yıl'] as $yil => $etiket)
                            <button type="button" 
                                    @click="yil_baz = {{ $yil }}" 
                                    class="text-center px-2 py-2 rounded-lg border-2 text-xs font-semibold transition-all focus:outline-none"
                                    :class="yil_baz == {{ $yil }} 
                                        ? 'bg-indigo-600 border-indigo-600 text-white shadow-md' 
                                        : 'bg-white border-gray-200 text-gray-600 hover:border-indigo-300'">
                                {{ $etiket }}
                            </button>
                            @endforeach
                        </div>
                        {{-- Manuel Seçim --}}
                        <div class="w-24">
                            <input type="number" min="1" max="50" x-model.number="yil_baz" id="yil_baz_edit_input_{{ $iaa->id }}"
                                   class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs py-2 text-center" 
                                   placeholder="Diğer" />
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Seçtiğiniz süre boyunca elde edilecek toplam kazanç esas alınarak puan hesaplanır.</p>
                    <input type="hidden" name="yil_baz" :value="yil_baz">
                </div>
            </div>

            {{-- DİNAMİK PUAN & ROI ANALİZİ --}}
            <div class="mt-5 rounded-xl overflow-hidden border border-indigo-200">
                {{-- Başlık + Puan --}}
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span class="text-sm font-semibold text-white">
                            Hesaplanan Öneri Puanı
                            <template x-if="parseFloat(kazanc_miktar) > 0 && parseFloat(butce_miktar) > 0">
                                <span class="text-indigo-200 text-xs font-normal" x-text="'(' + yil_baz + ' yıl baz alındı)'" ></span>
                            </template>
                        </span>
                    </div>
                    <div class="text-right">
                        <template x-if="dynamicPuan !== null">
                            <span class="text-2xl font-extrabold text-white" x-text="dynamicPuan"></span>
                        </template>
                        <template x-if="dynamicPuan === null">
                            <span class="text-sm text-indigo-200 font-medium">Tüm alanları doldurun</span>
                        </template>
                    </div>
                </div>

                {{-- ROI Tablosu (kazanç ve bütçe doluysa göster) --}}
                <template x-if="parseFloat(kazanc_miktar) > 0 && parseFloat(butce_miktar) > 0">
                    <div>
                        {{-- Yıllık Projeksiyon Tablosu --}}
                        <div class="bg-white px-4 pt-3 pb-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Kümülatif Kazanç Projeksiyonu</p>
                            <div class="grid grid-cols-3 gap-2">
                                {{-- 1 Yıl --}}
                                <div class="rounded-lg p-2 text-center transition-all"
                                     :class="[
                                        roi1 >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200',
                                        yil_baz == 1 ? 'ring-2 ring-indigo-500 ring-offset-1' : ''
                                     ]">
                                    <p class="text-[10px] font-semibold uppercase" :class="roi1 >= 0 ? 'text-green-600' : 'text-red-600'">1 Yıl</p>
                                    <p class="text-sm font-extrabold" :class="roi1 >= 0 ? 'text-green-800' : 'text-red-700'"
                                       x-text="(roi1 >= 0 ? '+' : '') + roi1.toLocaleString('tr-TR') + ' ' + kazanc_birim"></p>
                                    <p class="text-[9px] mt-0.5" :class="roi1 >= 0 ? 'text-green-500' : 'text-red-400'"
                                       x-text="roi1 >= 0 ? 'Kârda ✓' : 'Zararda'"></p>
                                    <p class="text-[8px] text-gray-400 mt-1 border-t border-dashed border-gray-200 pt-0.5">(1 × Yıllık Kazanç - Bütçe)</p>
                                </div>
                                {{-- 5 Yıl --}}
                                <div class="rounded-lg p-2 text-center transition-all"
                                     :class="[
                                        roi5 >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200',
                                        yil_baz == 5 ? 'ring-2 ring-indigo-500 ring-offset-1' : ''
                                     ]">
                                    <p class="text-[10px] font-semibold uppercase" :class="roi5 >= 0 ? 'text-green-600' : 'text-red-600'">5 Yıl</p>
                                    <p class="text-sm font-extrabold" :class="roi5 >= 0 ? 'text-green-800' : 'text-red-700'"
                                       x-text="(roi5 >= 0 ? '+' : '') + roi5.toLocaleString('tr-TR') + ' ' + kazanc_birim"></p>
                                    <p class="text-[9px] mt-0.5" :class="roi5 >= 0 ? 'text-green-500' : 'text-red-400'"
                                       x-text="roi5 >= 0 ? 'Kârda ✓' : 'Zararda'"></p>
                                    <p class="text-[8px] text-gray-400 mt-1 border-t border-dashed border-gray-200 pt-0.5">(5 × Yıllık Kazanç - Bütçe)</p>
                                </div>
                                {{-- 10 Yıl --}}
                                <div class="rounded-lg p-2 text-center transition-all"
                                     :class="[
                                        roi10 >= 0 ? 'bg-emerald-50 border border-emerald-200' : 'bg-red-50 border border-red-200',
                                        yil_baz == 10 ? 'ring-2 ring-indigo-500 ring-offset-1' : ''
                                     ]">
                                    <p class="text-[10px] font-semibold uppercase" :class="roi10 >= 0 ? 'text-emerald-600' : 'text-red-600'">10 Yıl</p>
                                    <p class="text-sm font-extrabold" :class="roi10 >= 0 ? 'text-emerald-800' : 'text-red-700'"
                                       x-text="(roi10 >= 0 ? '+' : '') + roi10.toLocaleString('tr-TR') + ' ' + kazanc_birim"></p>
                                    <p class="text-[9px] mt-0.5" :class="roi10 >= 0 ? 'text-emerald-500' : 'text-red-400'"
                                       x-text="roi10 >= 0 ? 'Kârda ✓' : 'Zararda'"></p>
                                    <p class="text-[8px] text-gray-400 mt-1 border-t border-dashed border-gray-200 pt-0.5">(10 × Yıllık Kazanç - Bütçe)</p>
                                </div>
                            </div>
                        </div>

                        {{-- Amortisman Bilgisi --}}
                        <div class="bg-amber-50 border-t border-amber-100 px-4 py-2 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs text-amber-700">
                                <span class="font-semibold">Tahmini amortisman:</span>
                                <span x-text="amortisman"></span>
                            </p>
                        </div>
                    </div>
                </template>

                {{-- Formül Açıklaması --}}
                <div class="bg-indigo-50 px-4 py-2 border-t border-indigo-100">
                    <p class="text-xs text-indigo-600">
                        Puan formülü: <span class="font-mono font-semibold">round((Risk × Yıllık Kazanç × N) ÷ Bütçe)</span>
                        — <span x-text="'N = ' + yil_baz + ' yıl (seçtiğiniz ufuk)'"></span>.
                        Bütçe tek seferlik yatırım, kazanç yıllıktır.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">İptal</x-secondary-button>
                <x-primary-button class="ml-3">Puanı Güncelle</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

{{-- SİZİN ÇALIŞAN SCRIPTİNİZ KORUNDU --}}
@once
    @push('scripts')
    <script>
        function approvalForm(iaaData) {
            return {
                risk: iaaData.risk || 3,
                kazanc_miktar: iaaData.kazanc_miktar || '',
                kazanc_birim: iaaData.kazanc_birim || '{{ $paraBirimleri[0] ?? 'TL' }}',
                butce_miktar: iaaData.butce_miktar || '',
                butce_birim: iaaData.butce_birim || '{{ $paraBirimleri[0] ?? 'TL' }}',
                yil_baz: parseInt(iaaData.yil_baz) || 5,
                
                // Orijinal verileri sakla
                originalData: iaaData,

                // Dinamik puan hesapla: round((risk × yıllık_kazanç × yil_baz) / bütçe)
                get dynamicPuan() {
                    const r = parseFloat(this.risk) || 0;
                    const k = parseFloat(this.kazanc_miktar) || 0;
                    const b = parseFloat(this.butce_miktar) || 0;
                    const y = parseFloat(this.yil_baz) || 5;
                    if (r > 0 && k > 0 && b > 0) {
                        return Math.round((r * k * y) / b);
                    }
                    return null;
                },

                // ROI Hesaplamaları
                // Bütçe tek seferlik yatırım, kazanç yıllık gelir
                // Net Getiri (n yıl) = (n × yıllık_kazanç) - bütçe
                get roi1() {
                    const k = parseFloat(this.kazanc_miktar) || 0;
                    const b = parseFloat(this.butce_miktar) || 0;
                    return Math.round((1 * k) - b);
                },
                get roi5() {
                    const k = parseFloat(this.kazanc_miktar) || 0;
                    const b = parseFloat(this.butce_miktar) || 0;
                    return Math.round((5 * k) - b);
                },
                get roi10() {
                    const k = parseFloat(this.kazanc_miktar) || 0;
                    const b = parseFloat(this.butce_miktar) || 0;
                    return Math.round((10 * k) - b);
                },

                // Amortisman: Kaç yılda kendini amorti eder?
                // Formül: ceil(bütçe / yıllık_kazanç)
                get amortisman() {
                    const k = parseFloat(this.kazanc_miktar) || 0;
                    const b = parseFloat(this.butce_miktar) || 0;
                    if (k <= 0) return 'Hesaplanamadı (kazanç girilmedi)';
                    if (b <= 0) return 'Hesaplanamadı (bütçe girilmedi)';
                    const yil = Math.ceil(b / k);
                    if (yil === 1) return 'İlk yılda kendini amorti eder ✓';
                    if (yil <= 3) return `Yaklaşık ${yil} yılda kendini amorti eder`;
                    if (yil <= 7) return `Yaklaşık ${yil} yılda kendini amorti eder (Uzun vadeli proje)`;
                    return `${yil} yıl+ (Çok uzun vadeli yatırım)`;
                },

                get hasProposerValues() {
                    const kazanc = parseFloat(this.originalData.oneren_kazanc_miktar) || 0;
                    const butce = parseFloat(this.originalData.oneren_butce_miktar) || 0;
                    return kazanc > 0 || butce > 0;
                },
                useProposerValues() {
                    this.kazanc_miktar = this.originalData.oneren_kazanc_miktar;
                    this.kazanc_birim = this.originalData.oneren_kazanc_birim;
                    this.butce_miktar = this.originalData.oneren_butce_miktar;
                    this.butce_birim = this.originalData.oneren_butce_birim;
                }
            }
        }
    </script>
    @endpush
@endonce