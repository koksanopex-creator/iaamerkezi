{{-- resources/views/proje-calisma-alani/partials/_visits-tab.blade.php --}}
@php
    $isCustomer = auth()->check() && (auth()->user()->hasRole('Müşteri') || auth()->user()->hasRole('Müşteri Temsilcisi') || auth()->user()->hasRole('Müşteri Saha Temsilcisi') || !auth()->user()->is_personnel);
    
    $visitsQuery = \App\Models\IaaZiyaretPlani::where('iaa_id', $iaa->id)
        ->orderBy('created_at', 'desc')
        ->with('step');
        
    $visits = $visitsQuery->get();

    // Filtreleme (müşteri için gizli adımları çıkar)
    if ($isCustomer) {
        $visits = $visits->filter(function($visit) {
            if (!$visit->iaa_workflow_step_id) return true; // Adıma bağlı değilse göster

            // Adımın gizlilik durumunu progress_updates üzerinden kontrol et
            $iaaTalepId = \App\Models\IaaTalep::where('iaa_id', $visit->iaa_id)->value('id');
            $progressUpdate = null;
            if ($iaaTalepId) {
                $progressUpdate = \App\Models\IaaProgressUpdate::where('iaa_talep_id', $iaaTalepId)
                    ->where('iaa_workflow_step_id', $visit->iaa_workflow_step_id)
                    ->first();
            }
                
            if ($progressUpdate && $progressUpdate->is_hidden_from_customer) {
                return false;
            }
            return true;
        });
    }
@endphp

<div class="space-y-6">
    <div class="flex items-center justify-between mb-2">
        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
            </div>
            Proje Ziyaretleri
        </h3>
    </div>

    @if($visits->count() > 0)
        <div class="grid grid-cols-1 gap-8">
            @foreach($visits as $visit)
                <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm relative {{ $visit->status == 'Tamamlandı' ? 'bg-gray-50' : 'bg-white' }}">
                    
                    {{-- Adım Badge --}}
                    @if($visit->step)
                        <div class="absolute top-0 right-0 bg-indigo-100 text-indigo-700 text-[11px] font-bold px-4 py-1.5 rounded-bl-xl border-b border-l border-indigo-200 shadow-sm z-10 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            Adım: {{ $visit->step->name }}
                        </div>
                    @else
                        <div class="absolute top-0 right-0 bg-gray-100 text-gray-700 text-[11px] font-bold px-4 py-1.5 rounded-bl-xl border-b border-l border-gray-200 shadow-sm z-10 flex items-center gap-1.5">
                            Genel Ziyaret
                        </div>
                    @endif

                    <div class="pt-6 pb-2 px-2">
                        @livewire('project.plan-visit', [
                            'iaa' => $iaa, 
                            'embedded' => true,
                            'stepId' => $visit->iaa_workflow_step_id
                        ], key('visit-tab-'.$visit->id))
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-gray-50 rounded-xl p-8 text-center border-2 border-dashed border-gray-200 mt-4">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <h4 class="text-gray-500 font-medium text-sm">Görüntülenecek ziyaret planı bulunmuyor.</h4>
            @if($isCustomer)
                <p class="text-xs text-gray-400 mt-1">Bu projede sizinle paylaşılan aktif bir ziyaret planı yoktur.</p>
            @endif
        </div>
    @endif
</div>
