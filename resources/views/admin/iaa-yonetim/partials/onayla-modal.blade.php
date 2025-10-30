<x-modal name="onayla-modal-{{ $iaa->id }}" focusable>
    {{-- SİZİN ÇALIŞAN VERİ AKTARIM YÖNTEMİNİZ KORUNDU --}}
    <div x-data="approvalForm({
        risk: '{{ $iaa->risk }}',
        kazanc_miktar: '{{ $iaa->kazanc_miktar }}',
        kazanc_birim: '{{ $iaa->kazanc_birim }}',
        butce_miktar: '{{ $iaa->butce_miktar }}',
        butce_birim: '{{ $iaa->butce_birim }}',
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
                    <select name="risk" x-model="risk" id="risk_{{ $iaa->id }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">                        <option value="1">1 (Çok Düşük)</option>
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
                    <x-input-label for="butce_miktar_{{ $iaa->id }}" value="Tahmini Bütçe" />
                    <x-text-input type="number" step="0.01" name="butce_miktar" x-model="butce_miktar" id="butce_miktar_{{ $iaa->id }}" class="mt-1 block w-full" />
                </div>
                 <div>
                    <x-input-label for="butce_birim_{{ $iaa->id }}" value="Birim" />
                    <select name="butce_birim" x-model="butce_birim" id="butce_birim_{{ $iaa->id }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        @foreach($paraBirimleri as $birim)<option value="{{ $birim }}">{{ $birim }}</option>@endforeach
                    </select>
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
                <select name="risk" id="risk_edit_{{ $iaa->id }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    {{-- old() fonksiyonu, validation hatası durumunda kullanıcının girdiği değeri korur --}}
                    <option value="1" @selected(old('risk', $iaa->risk) == 1)>1 (Çok Düşük)</option>
                    <option value="2" @selected(old('risk', $iaa->risk) == 2)>2 (Düşük)</option>
                    <option value="3" @selected(old('risk', $iaa->risk) == 3)>3 (Orta)</option>
                    <option value="4" @selected(old('risk', $iaa->risk) == 4)>4 (Yüksek)</option>
                    <option value="5" @selected(old('risk', $iaa->risk) == 5)>5 (Çok Yüksek)</option>
                </select>
            </div>
            <div></div>

            {{-- Kazanç --}}
            <div>
                <x-input-label for="kazanc_miktar_edit_{{ $iaa->id }}" value="Tahmini Yıllık Kazanç" />
                <x-text-input type="number" step="0.01" name="kazanc_miktar" value="{{ old('kazanc_miktar', $iaa->kazanc_miktar) }}" id="kazanc_miktar_edit_{{ $iaa->id }}" class="mt-1 block w-full" />
            </div>
            <div>
                <x-input-label for="kazanc_birim_edit_{{ $iaa->id }}" value="Birim" />
                <select name="kazanc_birim" id="kazanc_birim_edit_{{ $iaa->id }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @foreach($paraBirimleri as $birim)<option value="{{ $birim }}" @selected(old('kazanc_birim', $iaa->kazanc_birim) == $birim)>{{ $birim }}</option>@endforeach
                </select>
            </div>

            {{-- Bütçe --}}
            <div>
                <x-input-label for="butce_miktar_edit_{{ $iaa->id }}" value="Tahmini Bütçe" />
                <x-text-input type="number" step="0.01" name="butce_miktar" value="{{ old('butce_miktar', $iaa->butce_miktar) }}" id="butce_miktar_edit_{{ $iaa->id }}" class="mt-1 block w-full" />
            </div>
            <div>
                <x-input-label for="butce_birim_edit_{{ $iaa->id }}" value="Birim" />
                <select name="butce_birim" id="butce_birim_edit_{{ $iaa->id }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                     @foreach($paraBirimleri as $birim)<option value="{{ $birim }}" @selected(old('butce_birim', $iaa->butce_birim) == $birim)>{{ $birim }}</option>@endforeach
                </select>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">İptal</x-secondary-button>
            <x-primary-button class="ml-3">Puanı Güncelle</x-primary-button>
        </div>
    </form>
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
                
                // Orijinal verileri sakla
                originalData: iaaData,

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