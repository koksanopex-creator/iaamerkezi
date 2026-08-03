<table>
    <thead>
        <tr>
            <th rowspan="2" style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle;"></th>
            <th colspan="5" style="font-size: 16px; font-weight: bold; text-align: center; vertical-align: middle; background-color: #E2E8F0; border: 1px solid #cbd5e1;">
                <?php echo e($iaa->baslik); ?>

            </th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; color: #64748B; border: 1px solid #cbd5e1; vertical-align: middle;">
                PROJE DETAY RAPORU - <?php echo e(date('d.m.Y H:i')); ?>

            </th>
        </tr>
        <tr><td colspan="6" style="height: 20px;"></td></tr>
    </thead>
    <tbody>
        <tr><td colspan="6"></td></tr>
        
        <tr>
            <td colspan="6" style="font-weight: bold; background-color: #1e3a8a; color: #ffffff; height: 30px; vertical-align: middle; border: 1px solid #ffffff;">   1. PROJE KÜNYESİ</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Proje No:</td>
            <td>#<?php echo e($iaa->id); ?></td>
            <td style="font-weight: bold; background-color: #f3f4f6;">Durum:</td>
            <td colspan="3"><?php echo e($iaa->durum); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Takım:</td>
            <td><?php echo e($takim->ad ?? '-'); ?></td>
            <td style="font-weight: bold; background-color: #f3f4f6;">Takım Lideri:</td>
            <td colspan="3"><?php echo e($takim->lider->name ?? '-'); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Başlangıç:</td>
            <td><?php echo e(\Carbon\Carbon::parse($assignment->start_date)->format('d.m.Y')); ?></td>
            <td style="font-weight: bold; background-color: #f3f4f6;">Hedef Bitiş:</td>
            <td colspan="3"><?php echo e(\Carbon\Carbon::parse($assignment->due_date)->format('d.m.Y')); ?></td>
        </tr>
        <?php if($iaa->durum == 'Tamamlandı' && $iaa->real_completion_date): ?>
        <tr>
            <td style="font-weight: bold; background-color: #f0fdf4; color: #166534;">Tamamlanma:</td>
            <td colspan="5" style="background-color: #f0fdf4; color: #166534; font-weight: bold;"><?php echo e(\Carbon\Carbon::parse($iaa->real_completion_date)->format('d.m.Y H:i')); ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">İlerleme:</td>
            <td colspan="5">%<?php echo e(round($progressPercentage)); ?> (<?php echo e($completedStepsCount); ?> / <?php echo e($totalStepsCount); ?> Adım Tamamlandı)</td>
        </tr>

        <tr><td colspan="6"></td></tr>
        
        <tr>
            <td colspan="6" style="font-weight: bold; background-color: #1e3a8a; color: #ffffff; height: 30px; vertical-align: middle; border: 1px solid #ffffff;">   2. PROJE EKİBİ (SQUAD)</td>
        </tr>
        <tr style="font-weight: bold; background-color: #f3f4f6;">
            <td colspan="3">Ad Soyad</td>
            <td colspan="2">Rol</td>
            <td>Durum</td>
        </tr>
        <?php $ekip = $iaa->projeEkibi; ?>
        <?php if($ekip->isNotEmpty()): ?>
            <?php $__currentLoopData = $ekip; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td colspan="3">
                        <?php echo e($user->name); ?>

                        <?php if($user->trashed()): ?>
                            (Pasif / İşten Ayrıldı)
                        <?php endif; ?>
                    </td>
                    <td colspan="2"><?php echo e($user->pivot->rol ?? 'Üye'); ?></td>
                    <td><?php echo e(ucfirst($user->pivot->durum ?? '-')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center;">Kayıtlı ekip üyesi bulunamadı.</td>
            </tr>
        <?php endif; ?>

        <tr><td colspan="6"></td></tr>
        
        <tr>
            <td colspan="6" style="font-weight: bold; background-color: #1e3a8a; color: #ffffff; height: 30px; vertical-align: middle; border: 1px solid #ffffff;">   3. ADIM DETAYLARI VE ANALİZLER</td>
        </tr>
        
        <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php 
                $update = $progressUpdates[$step->id] ?? null;
                $isCompleted = in_array($step->id, $completedStepIds);
            ?>
            <tr>
                <td style="font-weight: bold; background-color: #bfdbfe; color: #1e3a8a; border: 1px solid #ffffff;"><?php echo e($loop->iteration); ?>. Adım:</td>
                <td colspan="5" style="font-weight: bold; background-color: #bfdbfe; color: #1e3a8a; border: 1px solid #ffffff;">
                    <?php echo e($step->name); ?> 
                    <?php if($isCompleted && $update && $update->completed_at): ?>
                        <span style="font-weight: normal; font-size: 10px;"> (Tamamlanma: <?php echo e(\Carbon\Carbon::parse($update->completed_at)->format('d.m.Y H:i')); ?>)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if($isCompleted): ?>
                <?php if(isset($isCustomerView) && $isCustomerView && $update && $update->is_hidden_from_customer): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; background-color: #f8fafc; color: #475569; font-weight: bold; height: 40px; vertical-align: middle;">
                            Bu adım projenin <?php echo e($iaa->atananTakim ? $iaa->atananTakim->ad : 'ekip'); ?> tarafından <?php echo e($update->completed_at ? \Carbon\Carbon::parse($update->completed_at)->format('d.m.Y') : ($update->created_at ? $update->created_at->format('d.m.Y') : '-')); ?> tarihinde tamamlanmıştır.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                        $content = json_decode($update->content, true); 
                        $tools = $content['tools'] ?? [];
                        $fb = $tools['fishbone'] ?? null;
                    ?>
                    
                    
                    <?php $fiveWhys = $tools['five_whys'] ?? null; ?>
                    <?php if($fiveWhys && count(array_filter($fiveWhys)) > 0): ?>
                        <tr>
                            <td colspan="6" style="font-weight: bold; background-color: #f5f3ff; color: #6d28d9; border-bottom: 1px solid #ddd6fe;">• 5 Neden Analizi</td>
                        </tr>
                        <?php $__currentLoopData = $fiveWhys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!empty($value) && str_starts_with($key, 'why')): ?>
                                <tr>
                                    <td style="background-color: #f3f4f6; font-weight: bold;"><?php echo e(str_replace('why', '', $key)); ?>. Neden:</td>
                                    <td colspan="5"><?php echo e($value); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    
                    
                    <?php if($fb && (!empty($fb['problem']) || !empty($fb['insan']) || !empty($fb['makine']))): ?>
                        <tr>
                            <td colspan="6" style="font-weight: bold; background-color: #fee2e2; color: #991b1b; border-bottom: 1px solid #fecaca;">• Balık Kılçığı Analizi <?php if(!empty($fb['problem'])): ?> - Problem: <?php echo e($fb['problem']); ?> <?php endif; ?></td>
                        </tr>
                        <tr>
                            <td style="background-color: #f3f4f6; font-weight: bold;">İnsan:</td>
                            <td><?php echo e($fb['insan'] ?? '-'); ?></td>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Yöntem:</td>
                            <td><?php echo e($fb['yontem'] ?? '-'); ?></td>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Makine:</td>
                            <td><?php echo e($fb['makine'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Malzeme:</td>
                            <td><?php echo e($fb['malzeme'] ?? '-'); ?></td>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Ölçüm:</td>
                            <td><?php echo e($fb['olcum'] ?? '-'); ?></td>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Çevre:</td>
                            <td><?php echo e($fb['cevre'] ?? '-'); ?></td>
                        </tr>
                    <?php endif; ?>

                    
                    <?php if(isset($tools['pareto']) && !empty($tools['pareto']['rows'])): ?>
                        <?php 
                            $pRows = collect($tools['pareto']['rows'])->filter(fn($r) => !empty($r['problem']))->sortByDesc('frequency');
                            $pTotal = $pRows->sum('frequency');
                            $pMax = $pRows->max('frequency') ?: 1;
                            $pCum = 0;
                        ?>
                        <?php if($pRows->isNotEmpty()): ?>
                            <tr>
                                <td colspan="6" style="font-weight: bold; background-color: #e0f2fe; color: #075985; border-bottom: 1px solid #bae6fd;">• Pareto Analizi: <?php echo e($tools['pareto']['title'] ?? 'Frekans Dağılımı'); ?></td>
                            </tr>
                            <tr style="background-color: #f1f5f9; font-weight: bold;">
                                <td colspan="2">Madde</td>
                                <td>Sıklık</td>
                                <td>Toplam</td>
                                <td>Kümülatif %</td>
                                <td>Görsel</td>
                            </tr>
                            <?php $__currentLoopData = $pRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php 
                                    $pCum += (float)$r['frequency'];
                                    $pPerc = $pTotal > 0 ? ($pCum / $pTotal) * 100 : 0;
                                ?>
                                <tr>
                                    <td colspan="2"><?php echo e($r['problem']); ?></td>
                                    <td style="text-align: right;"><?php echo e($r['frequency']); ?></td>
                                    <td style="text-align: right;"><?php echo e($pCum); ?></td>
                                    <td style="text-align: right;">%<?php echo e(round($pPerc)); ?></td>
                                    <td style="color: #3b82f6;"><?php echo e(str_repeat('█', min(15, (int)$r['frequency']))); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    
                    <?php 
                        $swot = $tools['swot'] ?? null;
                        $hasSwot = $swot && (!empty($swot['strengths']) || !empty($swot['weaknesses']) || !empty($swot['opportunities']) || !empty($swot['threats']));
                    ?>
                    <?php if($hasSwot): ?>
                        <tr>
                            <td colspan="6" style="font-weight: bold; background-color: #f0fdf4; color: #166534; border-bottom: 1px solid #bbf7d0;">• SWOT Analizi</td>
                        </tr>
                        <tr>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Güçlü Yönler (S):</td>
                            <td colspan="2"><?php echo e($swot['strengths'] ?? '-'); ?></td>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Zayıf Yönler (W):</td>
                            <td colspan="2"><?php echo e($swot['weaknesses'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Fırsatlar (O):</td>
                            <td colspan="2"><?php echo e($swot['opportunities'] ?? '-'); ?></td>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Tehditler (T):</td>
                            <td colspan="2"><?php echo e($swot['threats'] ?? '-'); ?></td>
                        </tr>
                    <?php endif; ?>

                    
                    <?php $fourM = $tools['4m_report'] ?? null; ?>
                    <?php if($fourM): ?>
                        <tr>
                            <td colspan="6" style="font-weight: bold; background-color: #f0f7ff; color: #1e40af; border-bottom: 1px solid #dbeafe;">• 4M Analizi</td>
                        </tr>
                        <tr>
                            <td style="background-color: #f3f4f6; font-weight: bold;">İnsan (Man):</td>
                            <td colspan="2"><?php echo e($fourM['man'] ?? '-'); ?></td>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Makine (Machine):</td>
                            <td colspan="2"><?php echo e($fourM['machine'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Malzeme (Material):</td>
                            <td colspan="2"><?php echo e($fourM['material'] ?? '-'); ?></td>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Metot (Method):</td>
                            <td colspan="2"><?php echo e($fourM['method'] ?? '-'); ?></td>
                        </tr>
                    <?php endif; ?>

                    
                    <?php if(isset($tools['action_list']) || isset($tools['task_list'])): ?>
                        <tr>
                            <td colspan="6" style="font-weight: bold; background-color: #f8fafc; color: #475569; border-bottom: 1px solid #e2e8f0;">• Aksiyon ve Görev Listeleri</td>
                        </tr>
                        <?php if(isset($tools['action_list'])): ?>
                            <?php $__currentLoopData = ($tools['action_list']['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td style="font-weight: bold;">Aksiyon:</td>
                                    <td colspan="4"><?php echo e($item['text']); ?></td>
                                    <td><?php echo e($item['is_completed'] ? 'Tamamlandı' : 'Bekliyor'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php if(isset($tools['task_list'])): ?>
                            <?php $__currentLoopData = ($tools['task_list']['tasks'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td style="font-weight: bold;">Görev:</td>
                                    <td colspan="3"><?php echo e($task['description']); ?></td>
                                    <td style="font-weight: bold;">Sorumlu:</td>
                                    <td>
                                        <?php $assignedUser = \App\Models\User::find($task['assigned_user_id']); ?>
                                        <?php echo e($assignedUser->name ?? '-'); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    
                    <?php
                        $stepTools = \App\Models\IaaStepTool::where('iaa_id', $iaa->id)
                            ->where('iaa_workflow_step_id', $step->id)
                            ->orderBy('order')
                            ->get();
                    ?>
                    <?php $__currentLoopData = $stepTools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $tData = is_array($tool->data) ? $tool->data : json_decode($tool->data, true); ?>
                        
                        <?php if($tool->tool_type === '5why'): ?>
                            <?php if(!empty($tData['whys']) && count(array_filter($tData['whys'])) > 0): ?>
                                <tr>
                                    <td colspan="6" style="font-weight: bold; background-color: #f5f3ff; color: #6d28d9; border-bottom: 1px solid #ddd6fe;">• <?php echo e($tool->title ?? '5 Neden Analizi'); ?> <?php if(!empty($tData['problemStatement'])): ?> - Problem: <?php echo e($tData['problemStatement']); ?> <?php endif; ?></td>
                                </tr>
                                <?php $__currentLoopData = $tData['whys']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $why): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!empty($why)): ?>
                                        <tr>
                                            <td style="background-color: #f3f4f6; font-weight: bold;"><?php echo e($index + 1); ?>. Neden:</td>
                                            <td colspan="5"><?php echo e($why); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!empty($tData['rootCause'])): ?>
                                    <tr>
                                        <td style="background-color: #fee2e2; color: #991b1b; font-weight: bold;">KÖK NEDEN:</td>
                                        <td colspan="5" style="color: #991b1b;"><?php echo e($tData['rootCause']); ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php elseif($tool->tool_type === 'chart'): ?>
                            <?php if(!empty($tData['labels']) && (!empty($tData['values']) || !empty($tData['series']))): ?>
                                <tr>
                                    <td colspan="6" style="font-weight: bold; background-color: #ffe4e6; color: #be123c; border-bottom: 1px solid #fecdd3;">• <?php echo e($tool->title ?? 'Grafik Analizi'); ?> <?php if(!empty($tData['chartTitle'])): ?> - <?php echo e($tData['chartTitle']); ?> <?php endif; ?></td>
                                </tr>
                                <tr style="background-color: #f1f5f9; font-weight: bold;">
                                    <td colspan="3">Kategori</td>
                                    <?php if(!empty($tData['series'])): ?>
                                        <?php $__currentLoopData = $tData['series']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td colspan="<?php echo e(max(1, floor(3 / count($tData['series'])))); ?>"><?php echo e($s['name'] ?? 'Değer'); ?></td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <td colspan="3">Değer</td>
                                    <?php endif; ?>
                                </tr>
                                <?php $__currentLoopData = $tData['labels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td colspan="3"><?php echo e($label); ?></td>
                                        <?php if(!empty($tData['series'])): ?>
                                            <?php $__currentLoopData = $tData['series']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <td colspan="<?php echo e(max(1, floor(3 / count($tData['series'])))); ?>"><?php echo e($s['data'][$index] ?? 0); ?></td>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <td colspan="3"><?php echo e($tData['values'][$index] ?? 0); ?></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php elseif($tool->tool_type === 'pareto'): ?>
                            <?php 
                                $pRows = collect($tData['items']??[])->filter(fn($r) => !empty($r['category']) && is_numeric($r['frequency']))->sortByDesc('frequency')->values();
                                $pTotal = $pRows->sum('frequency');
                                $pCum = 0;
                            ?>
                            <?php if($pRows->isNotEmpty()): ?>
                                <tr>
                                    <td colspan="6" style="font-weight: bold; background-color: #e0f2fe; color: #075985; border-bottom: 1px solid #bae6fd;">• <?php echo e($tool->title ?? 'Pareto Analizi'); ?></td>
                                </tr>
                                <tr style="background-color: #f1f5f9; font-weight: bold;">
                                    <td colspan="2">Kategori</td>
                                    <td>Sıklık</td>
                                    <td>Toplam</td>
                                    <td>Kümülatif %</td>
                                    <td>Görsel</td>
                                </tr>
                                <?php $__currentLoopData = $pRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php 
                                        $pCum += (float)$r['frequency'];
                                        $pPerc = $pTotal > 0 ? ($pCum / $pTotal) * 100 : 0;
                                    ?>
                                    <tr>
                                        <td colspan="2"><?php echo e($r['category']); ?></td>
                                        <td style="text-align: right;"><?php echo e($r['frequency']); ?></td>
                                        <td style="text-align: right;"><?php echo e($pCum); ?></td>
                                        <td style="text-align: right;">%<?php echo e(round($pPerc)); ?></td>
                                        <td style="color: #3b82f6;"><?php echo e(str_repeat('█', min(15, (int)$r['frequency']))); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php elseif($tool->tool_type === 'fishbone'): ?>
                            <?php $cats = $tData['categories'] ?? []; ?>
                            <tr>
                                <td colspan="6" style="font-weight: bold; background-color: #fee2e2; color: #991b1b; border-bottom: 1px solid #fecaca;">• <?php echo e($tool->title ?? 'Balık Kılçığı Analizi'); ?> <?php if(!empty($tData['problem_statement'])): ?> - Problem: <?php echo e($tData['problem_statement']); ?> <?php endif; ?></td>
                            </tr>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">İnsan:</td>
                                <td><?php echo e(implode(', ', $cats['insan'] ?? []) ?: '-'); ?></td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Yöntem:</td>
                                <td><?php echo e(implode(', ', $cats['metot'] ?? []) ?: '-'); ?></td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Makine:</td>
                                <td><?php echo e(implode(', ', $cats['makine'] ?? []) ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Malzeme:</td>
                                <td><?php echo e(implode(', ', $cats['malzeme'] ?? []) ?: '-'); ?></td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Ölçüm:</td>
                                <td><?php echo e(implode(', ', $cats['olcum'] ?? []) ?: '-'); ?></td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Çevre:</td>
                                <td><?php echo e(implode(', ', $cats['cevre'] ?? []) ?: '-'); ?></td>
                            </tr>
                        <?php elseif($tool->tool_type === 'swot'): ?>
                            <tr>
                                <td colspan="6" style="font-weight: bold; background-color: #f0fdf4; color: #166534; border-bottom: 1px solid #bbf7d0;">• <?php echo e($tool->title ?? 'SWOT Analizi'); ?></td>
                            </tr>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Güçlü Yönler (S):</td>
                                <td colspan="2"><?php echo e($tData['strengths'] ?? '-'); ?></td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Zayıf Yönler (W):</td>
                                <td colspan="2"><?php echo e($tData['weaknesses'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Fırsatlar (O):</td>
                                <td colspan="2"><?php echo e($tData['opportunities'] ?? '-'); ?></td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Tehditler (T):</td>
                                <td colspan="2"><?php echo e($tData['threats'] ?? '-'); ?></td>
                            </tr>
                        <?php elseif($tool->tool_type === '4m_report'): ?>
                            <tr>
                                <td colspan="6" style="font-weight: bold; background-color: #f0f7ff; color: #1e40af; border-bottom: 1px solid #dbeafe;">• <?php echo e($tool->title ?? '4M Gelişim Raporu'); ?></td>
                            </tr>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">İnsan (Man):</td>
                                <td colspan="2"><?php echo e($tData['items']['man'] ?? '-'); ?></td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Makine (Machine):</td>
                                <td colspan="2"><?php echo e($tData['items']['machine'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Malzeme (Material):</td>
                                <td colspan="2"><?php echo e($tData['items']['material'] ?? '-'); ?></td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Metot (Method):</td>
                                <td colspan="2"><?php echo e($tData['items']['method'] ?? '-'); ?></td>
                            </tr>
                        <?php elseif($tool->tool_type === 'checklist'): ?>
                            <tr>
                                <td colspan="6" style="font-weight: bold; background-color: #f8fafc; color: #475569; border-bottom: 1px solid #e2e8f0;">• <?php echo e($tool->title ?? 'Kontrol Listesi'); ?></td>
                            </tr>
                            <?php $__currentLoopData = ($tData['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td colspan="5"><?php echo e($item['text']); ?></td>
                                    <td><?php echo e(!empty($item['checked']) ? 'Tamamlandı' : 'Bekliyor'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php elseif($tool->tool_type === 'action_list'): ?>
                            <tr>
                                <td colspan="6" style="font-weight: bold; background-color: #f8fafc; color: #475569; border-bottom: 1px solid #e2e8f0;">• <?php echo e($tool->title ?? 'Aksiyon Listesi'); ?></td>
                            </tr>
                            <?php $__currentLoopData = ($tData['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td style="font-weight: bold;">Aksiyon:</td>
                                    <td colspan="3"><?php echo e($item['action'] ?? ''); ?></td>
                                    <td style="font-weight: bold;">Sorumlu: <?php echo e($item['owner'] ?? ''); ?></td>
                                    <td><?php echo e(!empty($item['status']) && $item['status'] == 'completed' ? 'Tamamlandı' : 'Bekliyor'); ?> (<?php echo e($item['target_date'] ?? ''); ?>)</td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <?php if(isset($tools['bar_chart_data']) && is_array($tools['bar_chart_data'])): ?>
                        <?php $__currentLoopData = $tools['bar_chart_data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bIndex => $bData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!empty($bData['rows'])): ?>
                                <tr>
                                    <td colspan="6" style="font-weight: bold; background-color: #ffe4e6; color: #be123c; border-bottom: 1px solid #fecdd3;">• <?php echo e($bData['title'] ?? 'Sütun Grafiği'); ?></td>
                                </tr>
                                <tr style="background-color: #f1f5f9; font-weight: bold;">
                                    <td colspan="3"><?php echo e($bData['axis_x_label'] ?? 'Kategoriler'); ?></td>
                                    <td colspan="3"><?php echo e($bData['axis_y_label'] ?? 'Değerler'); ?></td>
                                </tr>
                                <?php $__currentLoopData = $bData['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(isset($r['label']) && $r['label'] !== ''): ?>
                                    <tr>
                                        <td colspan="3"><?php echo e($r['label']); ?></td>
                                        <td colspan="3"><?php echo e($r['value'] ?? 0); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                    <?php if(isset($tools['line_chart_data']) && is_array($tools['line_chart_data'])): ?>
                        <?php $__currentLoopData = $tools['line_chart_data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lIndex => $lData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!empty($lData['rows'])): ?>
                                <tr>
                                    <td colspan="6" style="font-weight: bold; background-color: #ffe4e6; color: #be123c; border-bottom: 1px solid #fecdd3;">• <?php echo e($lData['title'] ?? 'Çizgi Grafiği'); ?></td>
                                </tr>
                                <tr style="background-color: #f1f5f9; font-weight: bold;">
                                    <td colspan="3"><?php echo e($lData['axis_x_label'] ?? 'Kategoriler'); ?></td>
                                    <td colspan="3"><?php echo e($lData['axis_y_label'] ?? 'Değerler'); ?></td>
                                </tr>
                                <?php $__currentLoopData = $lData['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(isset($r['label']) && $r['label'] !== ''): ?>
                                    <tr>
                                        <td colspan="3"><?php echo e($r['label']); ?></td>
                                        <td colspan="3"><?php echo e($r['value'] ?? 0); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                    
                    <?php
                        $stepVisits = \App\Models\IaaZiyaretPlani::where('iaa_id', $iaa->id)
                            ->where('iaa_workflow_step_id', $step->id)
                            ->get();
                    ?>
                    <?php if($stepVisits->isNotEmpty()): ?>
                        <tr>
                            <td colspan="6" style="font-weight: bold; background-color: #fef3c7; color: #d97706; border-bottom: 1px solid #fde68a;">• Ziyaret Planı ve Sonuçları</td>
                        </tr>
                        <?php $__currentLoopData = $stepVisits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Müşteri:</td>
                                <td colspan="2"><?php echo e($iaa->musteriSikayeti->customer->name ?? $iaa->musteriSikayeti->musteri_adi ?? 'Müşteri'); ?></td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Tarih / Durum:</td>
                                <td colspan="2"><?php echo e($sv->visit_date ? \Carbon\Carbon::parse($sv->visit_date)->format('d.m.Y H:i') : '-'); ?> / <?php echo e($sv->status); ?></td>
                            </tr>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Personel:</td>
                                <td colspan="2"><?php echo e($sv->visitor_name ?? ($sv->visitor->name ?? 'Belirtilmedi')); ?></td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Tahmini Dönüş:</td>
                                <td colspan="2"><?php echo e($sv->estimated_return_date ? \Carbon\Carbon::parse($sv->estimated_return_date)->format('d.m.Y') : '-'); ?></td>
                            </tr>
                            <?php if($sv->result): ?>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Sonuç:</td>
                                <td colspan="5"><?php echo e($sv->result); ?></td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                    
                    <?php if(isset($content['form_data']) && is_array($content['form_data'])): ?>
                        <?php $__currentLoopData = $content['form_data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(isset($field['text']) && !empty($field['text'])): ?>
                                <tr style="background-color: #fdfdfd;">
                                    <td style="font-weight: bold; vertical-align: top;">Not / Açıklama:</td>
                                    <td colspan="5"><?php echo e($field['text']); ?></td>
                                </tr>
                            <?php endif; ?>
                            
                            <?php if(isset($field['before_text']) || isset($field['after_text']) || isset($field['before_images']) || isset($field['after_images'])): ?>
                                <tr>
                                    <td colspan="6" style="font-weight: bold; background-color: #e0e7ff; color: #3730a3; border-bottom: 1px solid #c7d2fe;">• Önce / Sonra Karşılaştırma</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-weight: bold; background-color: #fee2e2; color: #991b1b;">ÖNCE</td>
                                    <td colspan="3" style="font-weight: bold; background-color: #dcfce7; color: #166534;">SONRA</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="vertical-align: top;"><?php echo e($field['before_text'] ?? '-'); ?></td>
                                    <td colspan="3" style="vertical-align: top;"><?php echo e($field['after_text'] ?? '-'); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="vertical-align: top;">
                                        <?php if(isset($field['before_images']) && is_array($field['before_images'])): ?>
                                            <?php $__currentLoopData = $field['before_images']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(is_string($img)): ?>
                                                    [IMG:<?php echo e($img); ?>]<br>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td colspan="3" style="vertical-align: top;">
                                        <?php if(isset($field['after_images']) && is_array($field['after_images'])): ?>
                                            <?php $__currentLoopData = $field['after_images']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(is_string($img)): ?>
                                                    [IMG:<?php echo e($img); ?>]<br>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php if(isset($field['files']) && is_array($field['files']) && count($field['files']) > 0): ?>
                                <tr>
                                    <td style="font-weight: bold; background-color: #f8fafc; vertical-align: top;">Ekli Dosyalar / Resimler:</td>
                                    <td colspan="5">
                                        <?php $__currentLoopData = $field['files']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(is_string($file)): ?>
                                                <?php $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION)); ?>
                                                <?php if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                                    [IMG:<?php echo e($file); ?>]<br>
                                                <?php else: ?>
                                                    <a href="<?php echo e(asset('storage/' . $file)); ?>" style="color: #2563eb; text-decoration: underline;">[Dosyayı Görüntüle]</a><br>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php if(isset($field['user_ids']) || isset($field['info_user_ids'])): ?>
                                <?php
                                    $uIds = $field['info_user_ids'] ?? $field['user_ids'] ?? [];
                                    $uNames = [];
                                    if (is_array($uIds) && !empty($uIds)) {
                                        $uNames = \App\Models\User::whereIn('id', $uIds)->pluck('name')->toArray();
                                    }
                                ?>
                                <?php if(!empty($uNames)): ?>
                                    <tr style="background-color: #fdfdfd;">
                                        <td style="font-weight: bold; vertical-align: top;">Seçilen Kullanıcılar:</td>
                                        <td colspan="5"><?php echo e(implode(', ', $uNames)); ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else: ?>
                <tr><td colspan="6" style="color: #94a3b8; font-style: italic;">Adım tamamlanmamış.</td></tr>
            <?php endif; ?>
            <tr><td colspan="6" style="border: none; height: 10px;"></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <?php if(isset($iade) && $iade): ?>
        <tr><td colspan="6"></td></tr>
        <tr>
            <td colspan="6" style="font-weight: bold; background-color: #1e3a8a; color: #ffffff; height: 25px; vertical-align: middle;">  4. İADE VE HURDA BİLDİRİMİ</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Ürün Grubu:</td>
            <td><?php echo e($iade->urun_turu ?? '-'); ?></td>
            <td style="font-weight: bold; background-color: #f3f4f6;">İade Sebebi:</td>
            <td colspan="3"><?php echo e($iade->iade_sebebi ?? '-'); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">İade Miktarı:</td>
            <td><?php echo e(floatval($iade->miktar)); ?> <?php echo e($iade->birim ?? ''); ?></td>
            <td style="font-weight: bold; background-color: #f3f4f6;">Toplam Parti:</td>
            <td colspan="3"><?php echo e(floatval($iade->toplam_parti_miktari)); ?> <?php echo e($iade->birim ?? ''); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">İade Oranı:</td>
            <td>
                <?php
                    $toplam = floatval($iade->toplam_parti_miktari);
                    $miktar = floatval($iade->miktar);
                    $oran = ($toplam > 0) ? ($miktar / $toplam) * 100 : 0;
                ?>
                %<?php echo e(number_format($oran, 1)); ?>

            </td>
            <td style="font-weight: bold; background-color: #f3f4f6;">İade Tarihi:</td>
            <td colspan="3"><?php echo e($iade->iade_tarihi ? $iade->iade_tarihi->format('d.m.Y') : '-'); ?></td>
        </tr>
        <?php if($iade->aciklama): ?>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Açıklama:</td>
            <td colspan="5">
                <?php if(isset($isCustomerView) && $isCustomerView && !$iade->musteri_gorebilir_mi): ?>
                    <span style="color: #94a3b8; font-style: italic;">Bu bölüm sadece şirket içi bilgilendirme amaçlıdır.</span>
                <?php else: ?>
                    <?php echo e($iade->aciklama); ?>

                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php endif; ?>

        
        <?php if(isset($visitData) && $visitData): ?>
        <tr><td colspan="6"></td></tr>
        <tr>
            <td colspan="6" style="font-weight: bold; background-color: #1e3a8a; color: #ffffff; height: 25px; vertical-align: middle;">  <?php echo e(isset($iade) && $iade ? '5' : '4'); ?>. MÜŞTERİ ZİYARET BİLGİLERİ</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Ziyaret Tarihi:</td>
            <td><?php echo e(isset($visitData['visit_date']) ? \Carbon\Carbon::parse($visitData['visit_date'])->format('d.m.Y H:i') : '-'); ?></td>
            <td style="font-weight: bold; background-color: #f3f4f6;">Ziyaret Sebebi:</td>
            <td colspan="3"><?php echo e($visitData['visit_reason'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Lot No:</td>
            <td><?php echo e($visitData['lot_no'] ?? '-'); ?></td>
            <td style="font-weight: bold; background-color: #f3f4f6;">Barkod:</td>
            <td colspan="3"><?php echo e($visitData['barcode'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Görüşülen Kişiler:</td>
            <td colspan="5">
                <?php if(!empty($visitData['contact_persons'])): ?>
                    <?php if(is_array($visitData['contact_persons'])): ?>
                        <?php echo e(implode(', ', $visitData['contact_persons'])); ?>

                    <?php else: ?>
                        <?php echo e($visitData['contact_persons']); ?>

                    <?php endif; ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php if(!empty($visitData['findings'])): ?>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Tespitler:</td>
            <td colspan="5"><?php echo e($visitData['findings']); ?></td>
        </tr>
        <?php endif; ?>
        <?php if(!empty($visitData['result'])): ?>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Sonuç:</td>
            <td colspan="5"><?php echo e($visitData['result']); ?></td>
        </tr>
        <?php endif; ?>
        <?php if(!empty($visitData['visit_notes'])): ?>
            <?php if(!(isset($isCustomerView) && $isCustomerView)): ?>
            <tr>
                <td style="font-weight: bold; background-color: #f3f4f6;">Notlar:</td>
                <td colspan="5"><?php echo e($visitData['visit_notes']); ?></td>
            </tr>
            <?php endif; ?>
        <?php endif; ?>
        <?php endif; ?>
        
        <tr><td colspan="6"></td></tr>
        <tr>
            <td colspan="6" style="text-align: center; color: #94a3b8; font-style: italic; font-size: 10px;">
                Bu rapor sistemsel olarak <?php echo e(auth()->user()->name); ?> tarafından oluşturulmuştur.
            </td>
        </tr>
    </tbody>
</table>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/export/excel.blade.php ENDPATH**/ ?>