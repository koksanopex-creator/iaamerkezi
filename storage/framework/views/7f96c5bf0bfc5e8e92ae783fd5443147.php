<div class="mt-4 grid grid-cols-3 gap-3 max-w-5xl">
    
    
    <div class="p-3 bg-green-50 border border-green-200 rounded-xl flex items-center justify-between shadow-sm">
        <div>
            <p class="text-[10px] font-bold text-green-600 uppercase">Memnuniyet (Onay)</p>
            <p class="text-xl font-black text-green-700"><?php echo e($musteriKararIstatistikleri['onay_orani']); ?></p>
        </div>
        <div class="p-2 bg-white rounded-full text-green-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
    </div>

    
    <div class="p-3 bg-red-50 border border-red-200 rounded-xl flex items-center justify-between shadow-sm">
        <div>
            <p class="text-[10px] font-bold text-red-600 uppercase">Memnuniyetsiz (Red)</p>
            <p class="text-xl font-black text-red-700"><?php echo e($musteriKararIstatistikleri['red_orani']); ?></p>
        </div>
        <div class="p-2 bg-white rounded-full text-red-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
    </div>

    
    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-xl flex items-center justify-between shadow-sm">
        <div>
            <p class="text-[10px] font-bold text-yellow-600 uppercase">Revizyon Talebi</p>
            <p class="text-xl font-black text-yellow-700"><?php echo e($musteriKararIstatistikleri['revizyon']); ?></p>
        </div>
        <div class="p-2 bg-white rounded-full text-yellow-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
    </div>

</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/raporlar/partials/executive/feedback-summary.blade.php ENDPATH**/ ?>