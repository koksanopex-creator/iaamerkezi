@props(['progressUpdate', 'step', 'isAssignedToSomeoneElse' => false, 'assignments' => collect(), 'canEdit' => false])

<div x-show="open" x-transition class="mt-4 border-t-2 border-gray-100 pt-4 space-y-6">
    
    {{-- Üst Bilgi Çubuğu (Tarih ve Buton) --}}
    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-200">
        
        {{-- Sol Taraf: Tamamlanma Tarihi ve Kullanıcı --}}
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <span class="font-medium">Tamamlanma:</span>
                <span class="font-bold text-gray-800">
                    {{ $progressUpdate->completed_at ? \Carbon\Carbon::parse($progressUpdate->completed_at)->format('d.m.Y H:i') : '-' }}
                </span>
                <span class="mx-1 font-medium text-gray-400">|</span>
                <span class="font-medium text-gray-600">Tamamlayan:</span>
                <span class="font-bold text-indigo-600">{{ $progressUpdate->user->name ?? 'Bilinmeyen Kullanıcı' }}</span>
            </div>
        </div>

        {{-- Sağ Taraf: Yeniden Düzenle Butonu (KİLİT KONTROLÜ İLE) --}}
        @php
            // Projenin durumunu kontrol et (Controller'da yaptığımız kilit mantığı)
            $iaaDurum = null;
            if(isset($iaa)) {
                $iaaDurum = $iaa->durum;
            } else {
                $iaaDurum = DB::table('iaa_talepleri')
                    ->join('iaas', 'iaa_talepleri.iaa_id', '=', 'iaas.id')
                    ->where('iaa_talepleri.id', $progressUpdate->iaa_talep_id)
                    ->value('iaas.durum');
            }

            $kilitliDurumlar = ['Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Tamamlandı'];
            $isLocked = in_array($iaaDurum, $kilitliDurumlar);

            // Sorumlu için sonraki adım kontrolü
            $isQualityManager = isset($iaa) ? app(\App\Services\ProjectWorkspace\ProjeCalismaAlaniService::class)->isQualityManagerWithInterventionPower(auth()->user(), $iaa) : false;
            $isLeader = isset($iaa) && $iaa->atananTakim && $iaa->atananTakim->lider_user_id == auth()->id();
            $isSuperAdmin = auth()->user()->hasRole('Superadmin');
            
            $isOrdinaryAssignee = !$isLeader && !$isSuperAdmin && !$isQualityManager;
            $canReopen = true;
            $blockReason = '';
            
            // Herkes için "Sonraki Adım Tamamlandı Mı?" kontrolü (Geri Al / Sil işlemi için gerekli)
            $subsequentCompleted = false;
            if (isset($step) && isset($step->order)) {
                $subsequentCompleted = \App\Models\IaaProgressUpdate::where('iaa_talep_id', $progressUpdate->iaa_talep_id)
                    ->whereNotNull('completed_at')
                    ->whereHas('step', function($q) use ($step) {
                        $q->where('order', '>', $step->order);
                    })
                    ->exists();
            }

            if ($isOrdinaryAssignee && $subsequentCompleted) {
                $canReopen = false;
                $blockReason = 'Bir sonraki adım tamamlandığı için bu adımı düzenleme yetkiniz kapanmıştır.';
            }

            $canUndo = !$subsequentCompleted;
        @endphp

        @if(!$isLocked && $canEdit)
            <div class="flex items-center gap-2">
                @if($canReopen)
                    @if(!$isAssignedToSomeoneElse)
                        {{-- Geri Al (Sil) Butonu --}}
                        @if($canUndo)
                            <div x-data="{ showUndoModal: false }">
                                <button type="button" @click="showUndoModal = true" class="inline-flex items-center px-3 py-1.5 bg-red-50 border border-red-200 rounded-md font-semibold text-xs text-red-600 uppercase tracking-widest shadow-sm hover:bg-red-100 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150" title="Adımı tamamen silerek bir önceki duruma geri dön">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Adımı Geri Al
                                </button>

                                {{-- UNDO MODAL --}}
                                <div x-show="showUndoModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showUndoModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                                        <!-- This element is to trick the browser into centering the modal contents. -->
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                        <div x-show="showUndoModal" @click.away="showUndoModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-[101]">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                        Adımı Tamamen Sil
                                                    </h3>
                                                    <div class="mt-2">
                                                        <p class="text-sm text-gray-500">
                                                            Bu adımı geri aldığınızda adım <strong>sıfırlanacak</strong> ve aşağıdaki veriler kalıcı olarak <strong>silinecektir</strong>:
                                                        </p>
                                                        <ul class="mt-3 text-sm text-red-600 bg-red-50 border border-red-100 p-3 rounded-md list-disc list-inside">
                                                            @php
                                                                $fileCount = 0;
                                                                $fieldCount = 0;
                                                                if($progressUpdate->content) {
                                                                    $cData = json_decode($progressUpdate->content, true);
                                                                    $fData = $cData['form_data'] ?? [];
                                                                    $fieldCount = count($fData);
                                                                    foreach($fData as $w) {
                                                                        if(isset($w['files']) && is_array($w['files'])) $fileCount += count($w['files']);
                                                                        if(isset($w['before_image_path'])) $fileCount++;
                                                                        if(isset($w['after_image_path'])) $fileCount++;
                                                                    }
                                                                }
                                                            @endphp
                                                            <li>Adıma girilen <strong>{{ $fieldCount }} adet</strong> form/widget verisi</li>
                                                            @if($fileCount > 0)
                                                                <li>Sunucuya yüklenen <strong>{{ $fileCount }} adet</strong> dosya/görsel</li>
                                                            @endif
                                                            <li>Adımın tamamlanma onayı ve atanan sorumluluklar (varsa)</li>
                                                        </ul>
                                                        <p class="mt-3 text-sm font-medium text-gray-700">
                                                            Bu işlem kesinlikle geri alınamaz. Devam etmek istiyor musunuz?
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                <form action="{{ route('proje.workspace.undoStep', $progressUpdate) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                                        Evet, Tamamen Sil
                                                    </button>
                                                </form>
                                                <button type="button" @click="showUndoModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                                                    İptal
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <button type="button" class="inline-flex items-center px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-md font-semibold text-xs text-gray-400 uppercase tracking-widest cursor-not-allowed" title="Sadece en son tamamlanan adım geri alınabilir.">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Adımı Geri Al
                            </button>
                        @endif

                        <form action="{{ route('proje.workspace.reopenStep', $progressUpdate) }}" method="POST" onsubmit="return confirm('Dikkat: Bu adımı yeniden açmak, onay sürecini sıfırlayabilir. Devam etmek istiyor musunuz?');">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-1.5 text-gray-500 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Düzenle / Aç
                            </button>
                        </form>
                    @else
                        @php 
                            $sorumluUserIds = $assignments->pluck('user_id')->toArray();
                            $sorumluNames = \App\Models\User::whereIn('id', $sorumluUserIds)->pluck('name')->implode(', ');
                        @endphp
                        <span class="inline-flex items-center px-3 py-1.5 bg-blue-50 border border-blue-100 rounded-md font-semibold text-xs text-blue-500 uppercase tracking-widest cursor-help" title="Bu adım '{{ $sorumluNames ?: 'ekibe' }}' atanmıştır. Sadece sorumlu kişiler veya lider düzenleyebilir.">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Sorumlu: {{ $sorumluNames ? Str::limit($sorumluNames, 20) : 'Atanmış' }}
                        </span>
                    @endif
            @else
                <span class="inline-flex items-center px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-md font-semibold text-xs text-amber-600 uppercase tracking-widest cursor-not-allowed" title="{{ $blockReason }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Kilitli (Sonraki Adım Dolu)
                </span>
            @endif
            </div>
        @else
            <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 border border-gray-200 rounded-md font-semibold text-xs text-gray-400 uppercase tracking-widest cursor-not-allowed" title="{{ $iaaDurum == 'Direktör Onayı Bekliyor' ? 'Proje direktör onayında. Müdahale için önce onayınızı geri çekmelisiniz.' : 'Proje onay aşamasında veya tamamlandığı için düzenleme yapılamaz.' }}">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Kilitli
            </span>
        @endif
    </div>

    {{-- JSON verisini ayrıştır --}}
    @php
        $reportData = $progressUpdate->content ? json_decode($progressUpdate->content, true) : null;
        $formData = $reportData['form_data'] ?? [];
        $toolsData = $reportData['tools'] ?? []; // five_whys, fishbone, pareto, bar_chart_data, line_chart_data içerir
    @endphp

    @if(!$reportData)
        {{-- JSON Değilse: Düz Metin İçerik Göster (Standart textarea içeriği) --}}
        <div class="p-4 bg-white border border-gray-200 rounded-xl text-gray-800 text-sm whitespace-pre-wrap shadow-sm ring-1 ring-black/5">
            <div class="flex items-center gap-2 font-bold text-gray-400 uppercase text-[10px] mb-3 tracking-widest border-b border-gray-100 pb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                ADIM RAPORU / İÇERİK
            </div>
            <div class="leading-relaxed text-gray-700">
                {{ $progressUpdate->content ?: 'Herhangi bir not girilmemiş.' }}
            </div>
        </div>
    @else
        {{-- === DİNAMİK WIDGET SONUÇ GÖSTERİMİ === --}}
        @foreach($step->widgets as $index => $widget)
            @php
                $widgetType = $widget['type'] ?? 'unknown';
                // Widget tanımından gelen config (sadece varsayılanlar için kullanılacak)
                $widgetConfigDefaults = $widget['config'] ?? []; 
                $widgetValue = $formData[$index] ?? null; // Form verisi
                 // Kaydedilmiş araç verisi (config + rows içerir)
                 $toolValue = null;
                 if ($widgetType === 'five_whys') $toolValue = $toolsData['five_whys'] ?? null;
                 elseif ($widgetType === 'fishbone') $toolValue = $toolsData['fishbone'] ?? null;
                 elseif ($widgetType === 'pareto') $toolValue = $toolsData['pareto'] ?? null;
                 elseif ($widgetType === 'bar_chart') $toolValue = $toolsData['bar_chart_data'][$index] ?? null;
                 elseif ($widgetType === 'line_chart') $toolValue = $toolsData['line_chart_data'][$index] ?? null;
                 elseif ($widgetType === 'swot') $toolValue = $toolsData['swot'] ?? null;
                 elseif ($widgetType === '4m_report') $toolValue = $toolsData['4m_report'][$index] ?? null;

                $widgetTitle = $widgetConfigDefaults['title'] ?? Str::ucfirst(str_replace(['_', 'chart', 'data'], [' ', '', ''], $widgetType));
            @endphp

            <div class="mb-6"> {{-- Her widget arasına boşluk --}}
                {{-- Info Text --}}
                @if($widgetType === 'info_text')
                    <div class="p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg">
                         {{-- Başlığı widget tanımından al --}}
                         <h5 class="text-base font-semibold text-blue-800 mb-2">{{ $widgetConfigDefaults['title'] ?? 'Bilgilendirme' }}</h5>
                        <div class="mt-1 text-sm text-blue-700 prose prose-sm max-w-none">
                           {!! nl2br(e($widgetConfigDefaults['content'] ?? '')) !!}
                        </div>
                    </div>

                {{-- Normal Form Alanları (Grafikler ve Analiz Araçları Hariç) --}}
                 @elseif(!in_array($widgetType, ['five_whys', 'fishbone', 'pareto', 'bar_chart', 'line_chart', 'swot', 'checklist', 'before_after', 'risk_matrix', '4m_report', 'task_list', 'action_list', 'prioritization_matrix', 'image_upload']))
                     <div class="text-sm max-w-none">
                         {{-- Başlığı widget tanımından al --}}
                         <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetConfigDefaults['title'] ?? Str::ucfirst(str_replace('_', ' ', $widgetType)) }}</h5>

                         @if($widgetType === 'textbox')
                         <p class="mt-1 text-gray-800 font-medium bg-gray-50 p-3 rounded-lg border border-gray-200">
                         {!! !empty($widgetValue['text']) ? nl2br(e($widgetValue['text'])) : '<span class="text-gray-400 italic">Girilmemiş</span>' !!}
                            </p>
                        @elseif($widgetType === 'user_select' || $widgetType === 'user_select_info')
                            @php 
                                $selectedUsers = collect();
                                $userIds = $widgetValue['info_user_ids'] ?? $widgetValue['user_ids'] ?? null;
                                if (is_array($userIds)) {
                                    $selectedUsers = \App\Models\User::whereIn('id', $userIds)->get();
                                } elseif (isset($widgetValue['user_id'])) {
                                    $selectedUser = \App\Models\User::find($widgetValue['user_id']);
                                    if ($selectedUser) $selectedUsers->push($selectedUser);
                                }
                            @endphp
                            <div class="mt-1 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                @if($selectedUsers->isNotEmpty())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($selectedUsers as $u)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200 shadow-sm">
                                                <svg class="mr-1.5 h-3.5 w-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                {{ $u->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-sm">Seçilmemiş</span>
                                @endif
                            </div>
                         @elseif($widgetType === 'date_picker')
                            <p class="mt-1 text-gray-800 font-medium bg-gray-50 p-3 rounded-lg border border-gray-200">
                                {!! isset($widgetValue['date']) && $widgetValue['date'] ? \Carbon\Carbon::parse($widgetValue['date'])->format('d.m.Y') : '<span class="text-gray-400 italic">Tarih Girilmemiş</span>' !!}
                            </p>
                            @elseif($widgetType === 'file_upload')
                                @if(!empty($widgetValue['files']) && is_array($widgetValue['files']))
                                    <div class="mt-1 flex flex-wrap gap-3">
                                        @foreach($widgetValue['files'] as $filePath)
                                            @php $isImage = Str::endsWith(strtolower($filePath), ['.png', '.jpg', '.jpeg', '.gif', '.bmp', '.webp']); @endphp
                                            @if($isImage)
                                                {{-- 🚨 DÜZELTME 1: Fancybox linki --}}
                                                <a href="{{ asset('storage/' . $filePath) }}" data-fancybox="gallery-{{$step->id}}-{{$index}}" data-caption="{{ basename($filePath) }}" class="block">
                                                    {{-- 🚨 DÜZELTME 2: Resim kaynağı --}}
                                                    <img src="{{ asset('storage/' . $filePath) }}" alt="{{ basename($filePath) }}" class="h-24 w-24 object-cover rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                                </a>
                                            @else
                                                {{-- 🚨 DÜZELTME 3: Dosya linki --}}
                                                <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="flex items-center gap-2 text-blue-600 hover:underline bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm">
                                                    <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.122 2.122l7.81-7.81" /></svg>
                                                    <span>{{ basename($filePath) }}</span>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <p class="mt-1 text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Dosya yüklenmemiş.</p>
                                @endif
                        @else
                             <p class="mt-1 text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Veri gösterimi desteklenmiyor: {{ $widgetType }}</p>
                        @endif
                     </div>

                {{-- === ARAÇ GÖSTERİMİ === --}}
                {{-- Five Whys --}}
                @elseif($widgetType === 'five_whys' && !empty($toolValue) && count(array_filter($toolValue)) > 0)
                     {{-- Başlığı widget tanımından al --}}
                     <div class="text-sm max-w-none"> <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetConfigDefaults['title'] ?? '5 Neden Analizi Sonuçları' }}</h5> <dl class="border rounded-lg p-4 bg-indigo-50/50"> @foreach($toolValue as $key => $value) @if(!empty($value) && str_starts_with($key, 'why')) <dt class="font-bold text-gray-600">{{ str_replace('why', '', $key) }}. Neden?</dt> <dd class="ml-4 mb-2 text-gray-800 whitespace-pre-wrap">{{ $value }}</dd> @endif @endforeach </dl> </div>
                {{-- Fishbone --}}
                 @elseif($widgetType === 'fishbone' && !empty($toolValue) && count(array_filter(array_slice($toolValue, 1))) > 0)
                      {{-- Başlığı widget tanımından al --}}
                      <div class="text-sm max-w-none mt-4"> <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetConfigDefaults['title'] ?? 'Balık Kılçığı Analizi Sonuçları' }}</h5> <div class="border rounded-lg p-4 bg-gray-50"> <p class="mb-4"><span class="font-bold text-red-700">Problem:</span> {{ $toolValue['problem'] ?? '' }}</p> <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4"> @foreach(['insan', 'yontem', 'makine', 'malzeme', 'olcum', 'cevre'] as $key) @if(!empty($toolValue[$key])) <div> <dt class="font-bold text-gray-700 capitalize">{{ $key }}</dt> <dd class="ml-4 mt-1 text-gray-600 whitespace-pre-wrap">{{ $toolValue[$key] }}</dd> </div> @endif @endforeach </dl> </div> </div>
                {{-- Pareto --}}
                @elseif($widgetType === 'pareto' && !empty($toolValue) && !empty($toolValue['rows']) && count(array_filter(array_column($toolValue['rows'], 'problem'))) > 0 )
                      @php /* Pareto hesaplama */ $pareto = $toolValue; $rows = $pareto['rows'] ?? []; $processedData = collect($rows)->filter(fn($row) => !empty($row['problem']) && isset($row['frequency']) && is_numeric($row['frequency']) && $row['frequency'] > 0)->sortByDesc('frequency')->values(); $totalFrequency = $processedData->sum('frequency'); $cumulative = 0; $tableRows = $processedData->map(function ($item) use ($totalFrequency, &$cumulative) { $cumulative += (float)$item['frequency']; $item['cumulative_sum'] = $cumulative; $item['cumulative_percentage'] = $totalFrequency > 0 ? round(($cumulative / $totalFrequency) * 100, 2) : 0; return $item; }); $chartDataForJs = [ 'labels' => $tableRows->pluck('problem')->toArray(), 'frequencies' => $tableRows->pluck('frequency')->toArray(), 'percentages' => $tableRows->pluck('cumulative_percentage')->toArray(), 'header2' => $pareto['header2'] ?? 'Sıklık', ]; $header1 = $pareto['header1'] ?? 'Problem'; $header2 = $pareto['header2'] ?? 'Sıklık'; $chartId = "paretoChart-" . $progressUpdate->id . "-" . $index; @endphp 
                      {{-- Başlığı widget tanımından al --}}
                      <div class="text-sm max-w-none mt-4"> <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetConfigDefaults['title'] ?? 'Pareto Analizi Sonuçları' }}</h5> <div class="border rounded-lg p-2 bg-white mb-4" style="height: 300px;"> <canvas id="{{ $chartId }}"></canvas> </div> <div class="overflow-x-auto border rounded-lg"> <table class="min-w-full text-sm"> <thead class="bg-gray-100"><tr><th class="p-2 text-left font-bold">#</th> <th class="p-2 text-left font-bold">{{ $header1 }}</th> <th class="p-2 text-right font-bold">{{ $header2 }}</th> <th class="p-2 text-right font-bold">Toplam {{ $header2 }}</th> <th class="p-2 text-right font-bold">Kümülatif %</th></tr></thead> <tbody class="divide-y"> @foreach($tableRows as $row) <tr> <td class="p-2">{{ $loop->iteration }}</td> <td class="p-2">{{ $row['problem'] }}</td> <td class="p-2 text-right">{{ number_format($row['frequency'], 0) }}</td> <td class="p-2 text-right">{{ number_format($row['cumulative_sum'], 0) }}</td> <td class="p-2 text-right font-bold">{{ number_format($row['cumulative_percentage'], 2) }}%</td> </tr> @endforeach </tbody> </table> </div> </div> @push('scripts') <script> document.addEventListener('DOMContentLoaded', function () { const canvas = document.getElementById('{{ $chartId }}'); const chartData = @json($chartDataForJs); if (canvas && chartData && typeof Chart !== 'undefined') { new Chart(canvas.getContext('2d'), { type: 'bar', data: { labels: chartData.labels, datasets: [ { label: chartData.header2, data: chartData.frequencies, backgroundColor: 'rgba(59, 130, 246, 0.5)', borderColor: 'rgba(59, 130, 246, 1)', yAxisID: 'y', }, { label: 'Kümülatif %', data: chartData.percentages, type: 'line', borderColor: 'rgba(239, 68, 68, 1)', tension: 0.1, yAxisID: 'y1', } ] }, options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, scales: { y: { type: 'linear', display: true, position: 'left', beginAtZero: true, title: { display: true, text: chartData.header2 } }, y1: { type: 'linear', display: true, position: 'right', min: 0, max: 100, grid: { drawOnChartArea: false }, ticks: { callback: value => value + '%' }, title: { display: true, text: 'Kümülatif %' } } } } }); } }); </script> @endpush

                {{-- === YENİ GRAFİKLERİ GÖSTER === --}}
                {{-- Sütun Grafiği --}}
                @elseif($widgetType === 'bar_chart' && !empty($toolValue) && !empty($toolValue['rows']) && count(array_filter(array_column($toolValue['rows'], 'label'))) > 0 )
                     @php /* Sütun Grafiği verisini hesapla */ $rows = $toolValue['rows'] ?? []; $processedData = collect($rows)->filter(fn($row) => isset($row['label']) && $row['label'] !== '' && isset($row['value']) && is_numeric($row['value']))->values(); 
                         // DÜZELTME: Başlıkları $toolValue'dan al, yoksa widgetConfig'den
                         $chartDataForJs = [ 
                             'labels' => $processedData->pluck('label')->toArray(), 
                             'values' => $processedData->pluck('value')->toArray(), 
                             'title' => $toolValue['title'] ?? $widgetConfigDefaults['title'] ?? 'Sütun Grafiği', 
                             'axis_x' => $toolValue['axis_x_label'] ?? $widgetConfigDefaults['axis_x_label'] ?? 'Kategoriler', 
                             'axis_y' => $toolValue['axis_y_label'] ?? $widgetConfigDefaults['axis_y_label'] ?? 'Değerler', 
                         ]; 
                         $chartId = "barChart-" . $progressUpdate->id . "-" . $index; 
                     @endphp 
                     <div class="text-sm max-w-none mt-4"> 
                         <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $chartDataForJs['title'] }}</h5> 
                         <div class="border rounded-lg p-2 bg-white mb-4" style="height: 300px;"> <canvas id="{{ $chartId }}"></canvas> </div> 
                     </div> 
                     @push('scripts') <script> document.addEventListener('DOMContentLoaded', function () { const canvas = document.getElementById('{{ $chartId }}'); const chartData = @json($chartDataForJs); if (canvas && chartData && typeof Chart !== 'undefined') { new Chart(canvas.getContext('2d'), { type: 'bar', data: { labels: chartData.labels, datasets: [{ label: chartData.axis_y, data: chartData.values, backgroundColor: 'rgba(75, 192, 192, 0.5)', borderColor: 'rgba(75, 192, 192, 1)', borderWidth: 1 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: chartData.title }, legend: { display: false } }, scales: { x: { title: { display: true, text: chartData.axis_x } }, y: { beginAtZero: true, title: { display: true, text: chartData.axis_y } } } } }); } }); </script> @endpush
                {{-- Çizgi Grafiği --}}
                @elseif($widgetType === 'line_chart' && !empty($toolValue) && !empty($toolValue['rows']) && count(array_filter(array_column($toolValue['rows'], 'label'))) > 0 )
                    @php /* Çizgi Grafiği verisini hesapla */ $rows = $toolValue['rows'] ?? []; $processedData = collect($rows)->filter(fn($row) => isset($row['label']) && $row['label'] !== '' && isset($row['value']) && is_numeric($row['value']))->values(); 
                        // DÜZELTME: Başlıkları $toolValue'dan al, yoksa widgetConfig'den
                        $chartDataForJs = [ 
                            'labels' => $processedData->pluck('label')->toArray(), 
                            'values' => $processedData->pluck('value')->toArray(), 
                            'title' => $toolValue['title'] ?? $widgetConfigDefaults['title'] ?? 'Çizgi Grafiği', 
                            'axis_x' => $toolValue['axis_x_label'] ?? $widgetConfigDefaults['axis_x_label'] ?? 'Kategoriler', 
                            'axis_y' => $toolValue['axis_y_label'] ?? $widgetConfigDefaults['axis_y_label'] ?? 'Değerler', 
                        ]; 
                        $chartId = "lineChart-" . $progressUpdate->id . "-" . $index; 
                    @endphp 
                    <div class="text-sm max-w-none mt-4"> 
                        <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $chartDataForJs['title'] }}</h5> 
                        <div class="border rounded-lg p-2 bg-white mb-4" style="height: 300px;"> <canvas id="{{ $chartId }}"></canvas> </div> 
                    </div> 
                    @push('scripts') <script> document.addEventListener('DOMContentLoaded', function () { const canvas = document.getElementById('{{ $chartId }}'); const chartData = @json($chartDataForJs); if (canvas && chartData && typeof Chart !== 'undefined') { new Chart(canvas.getContext('2d'), { type: 'line', data: { labels: chartData.labels, datasets: [{ label: chartData.axis_y, data: chartData.values, borderColor: 'rgba(75, 192, 192, 1)', tension: 0.1, fill: false }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: chartData.title }, legend: { display: false } }, scales: { x: { title: { display: true, text: chartData.axis_x } }, y: { beginAtZero: true, title: { display: true, text: chartData.axis_y } } } } }); } }); </script> @endpush


                {{-- Action List --}}
                @elseif($widgetType === 'action_list')
                    @php
                        $actionItems = $toolsData['action_list'][$index]['items'] ?? [];
                    @endphp
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetTitle }}</h5>
                        @if(!empty($actionItems))
                            <div class="space-y-2">
                                @foreach($actionItems as $item)
                                    <div class="flex items-center gap-3 p-3 rounded-lg border {{ $item['is_completed'] ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                                        @if($item['is_completed'])
                                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <div class="w-5 h-5 rounded border-2 border-gray-300 flex-shrink-0"></div>
                                        @endif
                                        <span class="{{ $item['is_completed'] ? 'line-through text-gray-500' : 'text-gray-800' }}">{{ $item['text'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Aksiyon listesi boş.</p>
                        @endif
                    </div>

                {{-- Task List --}}
                @elseif($widgetType === 'task_list')
                    @php
                        $tasks = $toolsData['task_list'][$index]['tasks'] ?? [];
                    @endphp
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetTitle }}</h5>
                        @if(!empty($tasks))
                            <div class="overflow-x-auto border rounded-xl">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Görev Tanımı</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Sorumlu</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($tasks as $task)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $task['description'] }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600">
                                                    @php $assignedUser = \App\Models\User::find($task['assigned_user_id']); @endphp
                                                    {{ $assignedUser->name ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Görev listesi boş.</p>
                        @endif
                    </div>

                {{-- Prioritization Matrix --}}
                @elseif($widgetType === 'prioritization_matrix')
                    @php
                        $matrixItems = $toolsData['prioritization_matrix'][$index]['items'] ?? [];
                    @endphp
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetTitle }}</h5>
                        @if(!empty($matrixItems))
                            <div class="overflow-x-auto border rounded-xl">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Aksiyon</th>
                                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Efor</th>
                                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Etki</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($matrixItems as $item)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item['action'] }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                        {{ $item['effort'] === 'yüksek' ? 'bg-red-100 text-red-700' : ($item['effort'] === 'orta' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                                        {{ Str::ucfirst($item['effort']) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                        {{ $item['impact'] === 'düşük' ? 'bg-red-100 text-red-700' : ($item['impact'] === 'orta' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                                        {{ Str::ucfirst($item['impact']) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Önceliklendirme matrisi boş.</p>
                        @endif
                    </div>

                {{-- Image Upload (Gallery View) --}}
                @elseif($widgetType === 'image_upload')
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetTitle }}</h5>
                        @if(!empty($widgetValue['files']) && is_array($widgetValue['files']))
                            <div class="mt-1 flex flex-wrap gap-3">
                                @foreach($widgetValue['files'] as $filePath)
                                    <a href="{{ asset('storage/' . $filePath) }}" data-fancybox="gallery-{{$step->id}}-{{$index}}" data-caption="{{ basename($filePath) }}" class="block">
                                        <img src="{{ asset('storage/' . $filePath) }}" alt="{{ basename($filePath) }}" class="h-28 w-28 object-cover rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-1 text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Resim yüklenmemiş.</p>
                        @endif
                    </div>

                {{-- SWOT Analizi --}}
                @elseif($widgetType === 'swot' && !empty($toolValue) && count(array_filter($toolValue)) > 0)
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-3">{{ $widgetTitle }}</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @if(!empty($toolValue['strengths']))
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2"><span class="w-7 h-7 rounded-lg bg-green-500 text-white flex items-center justify-center text-xs font-black">S</span><span class="text-sm font-bold text-green-700">Güçlü Yönler</span></div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $toolValue['strengths'] }}</p>
                            </div>
                            @endif
                            @if(!empty($toolValue['weaknesses']))
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2"><span class="w-7 h-7 rounded-lg bg-red-500 text-white flex items-center justify-center text-xs font-black">W</span><span class="text-sm font-bold text-red-700">Zayıf Yönler</span></div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $toolValue['weaknesses'] }}</p>
                            </div>
                            @endif
                            @if(!empty($toolValue['opportunities']))
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2"><span class="w-7 h-7 rounded-lg bg-blue-500 text-white flex items-center justify-center text-xs font-black">O</span><span class="text-sm font-bold text-blue-700">Fırsatlar</span></div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $toolValue['opportunities'] }}</p>
                            </div>
                            @endif
                            @if(!empty($toolValue['threats']))
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2"><span class="w-7 h-7 rounded-lg bg-amber-500 text-white flex items-center justify-center text-xs font-black">T</span><span class="text-sm font-bold text-amber-700">Tehditler</span></div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $toolValue['threats'] }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                {{-- Kontrol Listesi --}}
                @elseif($widgetType === 'checklist')
                    @php
                        $items = !empty($widgetConfigDefaults['items']) ? array_values(array_filter(array_map('trim', explode("\n", $widgetConfigDefaults['items'])))) : [];
                        $checkedItems = $widgetValue['checklist'] ?? [];
                        $checkedCount = count(array_filter($checkedItems));
                        $totalItems = count($items);
                    @endphp
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetConfigDefaults['title'] ?? 'Kontrol Listesi' }}</h5>
                        @if($totalItems > 0)
                            <div class="mb-2 text-xs font-medium text-gray-500">{{ $checkedCount }}/{{ $totalItems }} tamamlandı</div>
                            <div class="space-y-1">
                                @foreach($items as $itemIndex => $item)
                                    @php $isChecked = !empty($checkedItems[$itemIndex]); @endphp
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $isChecked ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                                        @if($isChecked)
                                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                        <span class="{{ $isChecked ? 'line-through text-gray-400' : 'text-gray-700' }}">{{ $item }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Kontrol listesi maddeleri tanımlanmamış.</p>
                        @endif
                    </div>

                {{-- Önce/Sonra Karşılaştırma --}}
                @elseif($widgetType === 'before_after')
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-3">{{ $widgetConfigDefaults['title'] ?? 'Önce/Sonra Karşılaştırma' }}</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full">ÖNCE</span>
                                    </div>
                                    @if(!empty($widgetValue['before_image_path']))
                                        <a href="{{ asset('storage/' . $widgetValue['before_image_path']) }}" data-fancybox="gallery-{{$step->id}}-{{$index}}" data-caption="ÖNCESİ">
                                            <img src="{{ asset('storage/' . $widgetValue['before_image_path']) }}" alt="Önce" class="w-full h-48 object-cover rounded-lg border border-red-300 shadow-sm">
                                        </a>
                                    @endif
                                    @if(!empty($widgetValue['before_text']))
                                        <p class="text-gray-700 whitespace-pre-wrap">{{ $widgetValue['before_text'] }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 bg-green-500 text-white text-xs font-bold rounded-full">SONRA</span>
                                    </div>
                                    @if(!empty($widgetValue['after_image_path']))
                                        <a href="{{ asset('storage/' . $widgetValue['after_image_path']) }}" data-fancybox="gallery-{{$step->id}}-{{$index}}" data-caption="SONRASI">
                                            <img src="{{ asset('storage/' . $widgetValue['after_image_path']) }}" alt="Sonra" class="w-full h-48 object-cover rounded-lg border border-green-300 shadow-sm">
                                        </a>
                                    @endif
                                    @if(!empty($widgetValue['after_text']))
                                        <p class="text-gray-700 whitespace-pre-wrap">{{ $widgetValue['after_text'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                {{-- Risk Matrisi --}}
                @elseif($widgetType === 'risk_matrix')
                    @php
                        $matrixSize = intval($widgetConfigDefaults['size'] ?? 5);
                        if (!in_array($matrixSize, [3, 5])) $matrixSize = 5;
                        $selRow = intval($widgetValue['risk_row'] ?? 0);
                        $selCol = intval($widgetValue['risk_col'] ?? 0);
                        $riskScore = $selRow * $selCol;
                        $maxScore = $matrixSize * $matrixSize;
                    @endphp
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-3">{{ $widgetConfigDefaults['title'] ?? 'Risk Matrisi' }}</h5>
                        @if($selRow > 0 && $selCol > 0)
                            <div class="flex items-center gap-3 mb-3">
                                <span class="text-sm font-bold text-gray-700">Seçilen Risk:</span>
                                @php
                                    $pct = $riskScore / $maxScore;
                                    if ($pct >= 0.6) $badgeColor = 'bg-red-400 text-white';
                                    elseif ($pct >= 0.35) $badgeColor = 'bg-amber-300 text-amber-900';
                                    elseif ($pct >= 0.15) $badgeColor = 'bg-yellow-200 text-yellow-800';
                                    else $badgeColor = 'bg-green-200 text-green-800';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-sm font-bold {{ $badgeColor }}">Olasılık: {{ $selRow }} × Etki: {{ $selCol }} = {{ $riskScore }}</span>
                            </div>
                            @if(!empty($widgetValue['risk_notes']))
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <span class="font-bold text-gray-600">Notlar:</span>
                                    <p class="text-gray-700 mt-1 whitespace-pre-wrap">{{ $widgetValue['risk_notes'] }}</p>
                                </div>
                            @endif
                        @else
                            <p class="text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Risk değerlendirmesi yapılmamış.</p>
                        @endif
                    </div>

                {{-- 4M Raporu Gözterimi --}}
                @elseif($widgetType === '4m_report' && !empty($toolValue) && count(array_filter($toolValue)) > 0)
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-3">{{ $widgetTitle }}</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                    <span class="text-xs font-bold text-indigo-700 uppercase">İnsan (Man)</span>
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $toolValue['man'] ?? '' }}</p>
                            </div>
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                    <span class="text-xs font-bold text-indigo-700 uppercase">Makine (Machine)</span>
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $toolValue['machine'] ?? '' }}</p>
                            </div>
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                    <span class="text-xs font-bold text-indigo-700 uppercase">Malzeme (Material)</span>
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $toolValue['material'] ?? '' }}</p>
                            </div>
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                    <span class="text-xs font-bold text-indigo-700 uppercase">Metot (Method)</span>
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $toolValue['method'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                {{-- Boş Araç/Grafik Gösterimi --}}
                 @elseif(in_array($widgetType, ['five_whys', 'fishbone', 'pareto', 'bar_chart', 'line_chart', 'swot']))
                      <div class="text-sm max-w-none">
                         {{-- Başlığı widget tanımından al --}}
                         <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetConfigDefaults['title'] ?? Str::ucfirst(str_replace(['_', 'chart', 'data'], [' ', '', ''], $widgetType)) }}</h5>
                         <p class="mt-1 text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Bu araç için veri girilmemiş.</p>
                     </div>
                @endif
                {{-- === ARAÇ GÖSTERİMİ BİTİŞİ === --}}
            </div> {{-- Widget boşluk div'i --}}
        @endforeach
        {{-- === DİNAMİK GÖSTERİM BİTİŞİ === --}}
    @endif {{-- End if !$reportData --}}

    {{-- EĞER BU ADIMA AİT BİR ZİYARET VARSA GÖSTER --}}
    @php
        $iaaId = isset($iaa) ? (is_object($iaa) ? $iaa->id : $iaa['id']) : ($progressUpdate->iaa_talep_id ?? null);
        $stepId = isset($step) ? (is_object($step) ? $step->id : $step['id']) : ($progressUpdate->iaa_workflow_step_id ?? null);
        $completedVisit = null;
        if ($iaaId && $stepId) {
            $completedVisit = \App\Models\IaaZiyaretPlani::where('iaa_id', $iaaId)
                ->where('iaa_workflow_step_id', $stepId)
                ->first();
        }
    @endphp
    @if($completedVisit)
        <div class="mt-8 border-t-2 border-dashed border-gray-300 pt-6">
            <h5 class="text-lg font-bold text-gray-800 mb-4">Ziyaret Planı ve Sonuçları</h5>
            <livewire:project.plan-visit :iaa="$iaa" :embedded="true" :stepId="$stepId" :wire:key="'plan-visit-completed-'.$stepId" />
        </div>
    @endif
</div>

{{-- Fancybox & Chart.js Scriptleri (Sadece bir kere yüklenir) --}}
@pushOnce('scripts')
    {{-- Fancybox --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
         function initFancybox() { Fancybox.bind("[data-fancybox]", { /* Custom options */ }); }
         document.addEventListener('DOMContentLoaded', initFancybox);
         // Livewire v3 uses 'navigate' event
         document.addEventListener('livewire:navigated', () => { if (typeof Fancybox !== 'undefined') { Fancybox.destroy(); } initFancybox(); });
    </script>
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script> {{-- Update Chart.js version --}}
@endpushOnce

