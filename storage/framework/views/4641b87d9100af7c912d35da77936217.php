<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center w-12">#No</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Firma / Şikayet</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Durum</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Öncelik</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Kategori</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Son Tarih</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $sikayetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $isCustomerEntry = false;
                        if ($sikayet->olusturanKurulUyesi && $sikayet->olusturanKurulUyesi->is_personnel == 0) {
                            $isCustomerEntry = true;
                        } elseif ($sikayet->user_id && !$sikayet->olusturanKurulUyesi) {
                            $isCustomerEntry = true;
                        }
                        
                        // Sıra numarası hesaplama (sayfalama dikkate alınarak)
                        $rowNumber = ($sikayetler->currentPage() - 1) * $sikayetler->perPage() + $index + 1;
                    ?>
                    <tr class="hover:bg-indigo-50/30 transition-colors group <?php echo e($isCustomerEntry ? 'bg-red-50/30' : ''); ?>">
                        
                        <td class="px-4 py-4 text-center font-bold text-gray-400 text-xs">
                            <?php echo e($rowNumber); ?>

                        </td>

                        
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <!--[if BLOCK]><![endif]--><?php if($sikayet->customer && $sikayet->customer->logo_path): ?>
                                    <img class="h-10 w-10 rounded-lg object-contain border bg-white p-0.5"
                                        src="<?php echo e(asset('storage/' . $sikayet->customer->logo_path)); ?>"
                                        alt="<?php echo e($sikayet->customer->name); ?>">
                                <?php else: ?>
                                    <div class="h-10 w-10 rounded-lg flex items-center justify-center font-bold text-xs shadow-sm bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                                        <?php echo e($sikayet->customer ? strtoupper(substr($sikayet->customer->name, 0, 1)) : strtoupper(substr($sikayet->musteri_adi, 0, 1))); ?>

                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-black text-gray-900 truncate">
                                            <?php echo e($sikayet->customer ? $sikayet->customer->name : $sikayet->musteri_adi); ?>

                                        </span>
                                        <!--[if BLOCK]><![endif]--><?php if($isCustomerEntry): ?>
                                            <span class="bg-red-700 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow-sm">MÜŞTERİ</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="text-xs text-indigo-600 font-bold truncate hover:underline underline-offset-2">
                                        <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>">
                                            <?php echo e(Str::limit($sikayet->musteri_sikayet_konusu, 60)); ?>

                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>

                        
                        <td class="px-4 py-4 text-center">
                            <div class="scale-90">
                                <?php echo $sikayet->musteri_durum_badge; ?>

                            </div>
                        </td>

                        
                        <td class="px-4 py-4 text-center">
                            <?php
                                $oncelikClass = match ($sikayet->musteri_oncelik) {
                                    'Acil' => 'text-red-600 bg-red-50 border-red-100',
                                    'Yüksek' => 'text-orange-600 bg-orange-50 border-orange-100',
                                    'Normal' => 'text-blue-600 bg-blue-50 border-blue-100',
                                    'Düşük' => 'text-green-600 bg-green-50 border-green-100',
                                    default => 'text-gray-600 bg-gray-100 border-gray-200'
                                };
                            ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded border text-[10px] font-bold <?php echo e($oncelikClass); ?>">
                                <?php echo e($sikayet->musteri_oncelik); ?>

                            </span>
                        </td>

                        
                        <td class="px-4 py-4 text-center">
                            <span class="text-xs font-medium text-gray-600"><?php echo e($sikayet->sikayetKategori->ad ?? 'Genel'); ?></span>
                        </td>

                        
                        <td class="px-4 py-4 text-center text-xs">
                            <div class="font-bold <?php echo e($sikayet->musteri_cozum_son_tarihi && \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->isPast() ? 'text-red-600' : 'text-gray-700'); ?>">
                                <?php echo e($sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : '-'); ?>

                            </div>
                        </td>

                        
                        <td class="px-4 py-4 text-right space-x-1 whitespace-nowrap">
                            
                            <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|Müşteri Şikayeti Kurulu')): ?>
                                <button wire:click="$dispatch('openTriyajModal', { id: <?php echo e($sikayet->id); ?> })"
                                    class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors inline-block" title="Yönet">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            
                            <a href="<?php echo e(route('admin.sikayetler.show', $sikayet)); ?>" 
                                class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors inline-block" title="Detay">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>

                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $sikayet)): ?>
                                <a href="<?php echo e(route('admin.sikayetler.edit', $sikayet)); ?>"
                                    class="p-1.5 rounded-lg text-indigo-600 hover:bg-indigo-50 transition-colors inline-block" title="Düzenle">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            <?php endif; ?>

                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $sikayet)): ?>
                                <button wire:click="delete(<?php echo e($sikayet->id); ?>)" wire:confirm="Silmek istediğinize emin misiniz?"
                                    class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition-colors inline-block" title="Sil">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            <?php endif; ?>

                            
                            <!--[if BLOCK]><![endif]--><?php if($sikayet->iaaProjesi): ?>
                                <a href="<?php echo e(route('proje.workspace.show', $sikayet->iaaProjesi->id)); ?>" target="_blank"
                                    class="p-1.5 rounded-lg text-purple-600 hover:bg-purple-50 transition-colors inline-block" title="Projeye Git">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 italic">
                            Şikayet bulunamadı.
                        </td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>
</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/sikayetler-partials/list.blade.php ENDPATH**/ ?>