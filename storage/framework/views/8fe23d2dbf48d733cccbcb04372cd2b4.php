<?php
    // visit_planned bayrağına göre Livewire bileşeninden veya Takvim API'den bağımsız olarak 
    // özet görünümü için bir yapı kuruyoruz. Normalde PlanVisit bileşeni özet gösteriyor
    // ancak onaylı/bekleyen durumlarda daha derli toplu bir kart gerekebilir.
?>

<?php if($iaa->visit_planned): ?>
    <div id="ziyaret-bilgileri-alani" class="mt-8 mb-8 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
        
        <div class="bg-gradient-to-r from-indigo-50 via-white to-white px-6 py-5 border-b border-indigo-100 flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    Müşteri Ziyaret Bilgileri
                </h3>
                <p class="text-xs text-indigo-600 mt-1 pl-11">Bu proje kapsamında Takvim üzerinde bir ziyaret planlanmıştır.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200 shadow-sm">
                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></span>
                    PLANLANDI
                </span>
            </div>
        </div>

        <div class="p-2 bg-white">
            
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('project.plan-visit', ['iaa' => $iaa, 'embedded' => true]);

$__html = app('livewire')->mount($__name, $__params, 'visit-form-card-'.$iaa->id, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /var/www/kys_koksan/iaa/resources/views/proje-calisma-alani/partials/_visit-details-card.blade.php ENDPATH**/ ?>