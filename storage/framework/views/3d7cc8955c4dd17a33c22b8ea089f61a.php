<div>
    <h4 class="text-lg font-semibold text-gray-800">Balık Kılçığı Diyagramı (Ishikawa)</h4>
    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <div class="space-y-8">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="flex flex-col items-center justify-end"><label class="text-sm font-bold text-gray-700">İnsan</label><textarea wire:model="toolsData.fishbone.insan" rows="3" class="w-full mt-2 text-sm border-gray-300 rounded-md shadow-sm"></textarea><div class="w-px h-12 bg-gray-400"></div></div>
                <div class="flex flex-col items-center justify-end"><label class="text-sm font-bold text-gray-700">Yöntem</label><textarea wire:model="toolsData.fishbone.yontem" rows="3" class="w-full mt-2 text-sm border-gray-300 rounded-md shadow-sm"></textarea><div class="w-px h-12 bg-gray-400"></div></div>
                <div class="flex flex-col items-center justify-end"><label class="text-sm font-bold text-gray-700">Makine</label><textarea wire:model="toolsData.fishbone.makine" rows="3" class="w-full mt-2 text-sm border-gray-300 rounded-md shadow-sm"></textarea><div class="w-px h-12 bg-gray-400"></div></div>
            </div>
            <div class="flex items-center">
                <div class="flex-grow h-px bg-gray-400"></div>
                <div class="flex-shrink-0 ml-4"><label class="text-sm font-bold text-red-700">Problem</label><input type="text" wire:model="toolsData.fishbone.problem" class="w-48 mt-1 text-sm border-gray-300 rounded-md shadow-sm"></div>
            </div>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="flex flex-col items-center justify-start"><div class="w-px h-12 bg-gray-400"></div><label class="text-sm font-bold text-gray-700">Malzeme</label><textarea wire:model="toolsData.fishbone.malzeme" rows="3" class="w-full mt-2 text-sm border-gray-300 rounded-md shadow-sm"></textarea></div>
                <div class="flex flex-col items-center justify-start"><div class="w-px h-12 bg-gray-400"></div><label class="text-sm font-bold text-gray-700">Ölçüm</label><textarea wire:model="toolsData.fishbone.olcum" rows="3" class="w-full mt-2 text-sm border-gray-300 rounded-md shadow-sm"></textarea></div>
                <div class="flex flex-col items-center justify-start"><div class="w-px h-12 bg-gray-400"></div><label class="text-sm font-bold text-gray-700">Çevre</label><textarea wire:model="toolsData.fishbone.cevre" rows="3" class="w-full mt-2 text-sm border-gray-300 rounded-md shadow-sm"></textarea></div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/project/widgets/_fishbone.blade.php ENDPATH**/ ?>