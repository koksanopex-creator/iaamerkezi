<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .logo { max-height: 50px; margin-bottom: 8px; }
        .title { font-size: 16px; font-weight: bold; color: #1e40af; text-transform: uppercase; margin-bottom: 5px; }
        .section { margin-bottom: 15px; }
        .section-title { font-size: 12px; font-weight: bold; background: #f1f5f9; padding: 6px; border-left: 4px solid #1e40af; margin-bottom: 8px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; table-layout: fixed; }
        th, td { border: 1px solid #cbd5e1; padding: 5px; text-align: left; word-wrap: break-word; word-break: break-all; }
        th { background: #f8fafc; font-weight: bold; color: #475569; }
        .footer { position: fixed; bottom: -60px; left: 0; right: 0; height: 30px; text-align: center; font-size: 8px; color: #94a3b8; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        @page { margin: 60px 30px; }
        
        /* Widget İçerik Stilleri */
        .widget-content { padding: 5px; }
        .label { font-weight: bold; color: #64748b; margin-right: 5px; }

        /* Pareto Tablo Grafiği */
        .pareto-bar { height: 12px; background: #3b82f6; display: block; border-radius: 2px; }
        .pareto-point { color: #ef4444; font-weight: bold; font-size: 9px; }
        
        /* Balık Kılçığı Stili */
        .fishbone-table { border: 2px solid #94a3b8; background: #f8fafc; margin-bottom: 15px; }
        .fishbone-header { background: #fee2e2; color: #b91c1c; font-weight: bold; text-align: center; font-size: 11px; padding: 5px; border-bottom: 2px solid #94a3b8; }
        .fishbone-cat-title { font-weight: bold; color: #475569; font-size: 9px; border-bottom: 1px solid #cbd5e1; padding-bottom: 2px; margin-bottom: 3px; background: #e2e8f0; padding-left: 3px; }
        .fishbone-cell { background: #fff; vertical-align: top; padding: 5px; height: 40px; }

        /* SWOT Analizi Stilleri */
        .swot-table { margin-bottom: 15px; border: 1px solid #cbd5e1; }
        .swot-cell { vertical-align: top; padding: 8px; width: 50%; border: 1px solid #cbd5e1; }
        .swot-title { font-weight: bold; font-size: 10px; margin-bottom: 4px; display: block; }
        .swot-s { border-left: 4px solid #10b981; background: #f0fdf4; color: #065f46; } /* Strengths */
        .swot-w { border-left: 4px solid #ef4444; background: #fef2f2; color: #991b1b; } /* Weaknesses */
        .swot-o { border-left: 4px solid #3b82f6; background: #eff6ff; color: #1e40af; } /* Opportunities */
        .swot-t { border-left: 4px solid #f59e0b; background: #fffbeb; color: #92400e; } /* Threats */
        .swot-icon { font-weight: black; margin-right: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <?php if(!empty($logo)): ?>
            <img src="<?php echo e(public_path('storage/' . $logo)); ?>" class="logo">
        <?php endif; ?>
        <div class="title"><?php echo e($iaa->baslik); ?></div>
        <p style="margin: 0;">Proje Detay Raporu | Tarih: <?php echo e(date('d.m.Y H:i')); ?></p>
    </div>

    <div class="section">
        <div class="section-title">1. Proje Künyesi</div>
        <table>
            <tr>
                <th width="15%">Proje No</th>
                <td width="35%">#<?php echo e($iaa->id); ?></td>
                <th width="15%">Durum</th>
                <td width="35%"><?php echo e($iaa->durum); ?></td>
            </tr>
            <tr>
                <th>Takım</th>
                <td><?php echo e($takim->ad ?? '-'); ?></td>
                <th>Takım Lideri</th>
                <td><?php echo e($takim->lider->name ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Başlangıç</th>
                <td><?php echo e(\Carbon\Carbon::parse($assignment->start_date)->format('d.m.Y')); ?></td>
                <th>Hedef Bitiş</th>
                <td><?php echo e(\Carbon\Carbon::parse($assignment->due_date)->format('d.m.Y')); ?></td>
            </tr>
            <?php if($iaa->durum == 'Tamamlandı' && $iaa->real_completion_date): ?>
            <tr>
                <th>Tamamlanma Tarihi</th>
                <td colspan="3" style="color: #10b981; font-weight: bold;"><?php echo e(\Carbon\Carbon::parse($iaa->real_completion_date)->format('d.m.Y H:i')); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <th>İlerleme</th>
                <td colspan="3">
                    <strong>%<?php echo e(round($progressPercentage)); ?></strong> 
                    (<?php echo e($completedStepsCount); ?> / <?php echo e($totalStepsCount); ?> Adım Tamamlandı)
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">2. Proje Ekibi (Squad)</div>
        <table>
            <thead>
                <tr>
                    <th width="40%">Ad Soyad</th>
                    <th width="30%">Rol</th>
                    <th width="30%">Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php $ekip = $iaa->projeEkibi; ?>
                <?php if($ekip->isNotEmpty()): ?>
                    <?php $__currentLoopData = $ekip; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <?php echo e($user->name); ?>

                                <?php if($user->trashed()): ?>
                                    <span style="color: #ef4444; font-size: 8px; font-style: italic;">(Pasif / İşten Ayrıldı)</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($user->pivot->rol ?? 'Üye'); ?></td>
                            <td><?php echo e(ucfirst($user->pivot->durum ?? '-')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Ekipten atanmış üye bulunmamaktadır.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">3. Adım Detayları ve Analizler</div>
        <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php 
                $update = $progressUpdates[$step->id] ?? null;
                $isCompleted = in_array($step->id, $completedStepIds);
            ?>
            <div style="margin-bottom: 20px; border: 1px solid #e2e8f0; padding: 10px;">
                <div style="font-weight: bold; border-bottom: 2px solid #1e40af; padding-bottom: 5px; margin-bottom: 10px; color: #1e40af; font-size: 12px;">
                    <?php echo e($loop->iteration); ?>. <?php echo e($step->name); ?> 
                    <span style="float: right; font-size: 9px; font-weight: normal; color: #64748b;">
                        [<?php echo e($isCompleted ? 'TAMAMLANDI' : 'BEKLİYOR'); ?>]
                    </span>
                </div>
                
                <?php if($isCompleted): ?>
                    <?php if(isset($isCustomerView) && $isCustomerView && $update && $update->is_hidden_from_customer): ?>
                        
                        <div style="padding: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; margin-top: 10px;">
                            <p style="margin: 0; color: #475569; font-size: 10px; font-weight: bold;">
                                Bu adım projenin <?php echo e($iaa->atananTakim ? $iaa->atananTakim->ad : 'ekip'); ?> tarafından <?php echo e($update->completed_at ? \Carbon\Carbon::parse($update->completed_at)->format('d.m.Y') : ($update->created_at ? $update->created_at->format('d.m.Y') : '-')); ?> tarihinde tamamlanmıştır.
                            </p>
                        </div>
                    <?php else: ?>
                        <div style="font-size: 9px; color: #64748b; margin-bottom: 10px;">
                            Sorumlu: <?php echo e($update->user->name ?? '-'); ?> | Tarih: <?php echo e(\Carbon\Carbon::parse($update->completed_at)->format('d.m.Y H:i')); ?>

                        </div>
                    
                    <div class="widget-content">
                        <?php 
                            $content = json_decode($update->content, true); 
                            $tools = $content['tools'] ?? [];
                        ?>

                        
                        <?php $fiveWhys = $tools['five_whys'] ?? null; ?>
                        <?php if($fiveWhys && count(array_filter($fiveWhys)) > 0): ?>
                            <div style="margin-top: 10px; margin-bottom: 15px;">
                                <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #4338ca;">5 Neden Analizi</div>
                                <div style="background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 4px; padding: 8px;">
                                    <?php $__currentLoopData = $fiveWhys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(!empty($value) && str_starts_with($key, 'why')): ?>
                                            <div style="margin-bottom: 5px;">
                                                <span style="font-weight: bold; color: #6d28d9; font-size: 9px;"><?php echo e(str_replace('why', '', $key)); ?>. Neden?</span>
                                                <div style="font-size: 9px; padding-left: 10px; border-left: 2px solid #ddd6fe; margin-top: 2px;"><?php echo e($value); ?></div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        
                        <?php 
                            $fb = $tools['fishbone'] ?? null;
                            $hasFb = $fb && (!empty($fb['problem']) || !empty($fb['insan']) || !empty($fb['yontem']) || !empty($fb['makine']));
                        ?>
                        <?php if($hasFb): ?>
                            <table class="fishbone-table">
                                <tr>
                                    <td colspan="3" class="fishbone-header">
                                        BALIK KILÇIĞI ANALİZİ <?php if(!empty($fb['problem'])): ?> - PROBLEM: <?php echo e($fb['problem']); ?> <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fishbone-cell">
                                        <div class="fishbone-cat-title">İNSAN</div>
                                        <div style="font-size: 8px;"><?php echo nl2br(e($fb['insan'] ?? '-')); ?></div>
                                    </td>
                                    <td class="fishbone-cell">
                                        <div class="fishbone-cat-title">YÖNTEM</div>
                                        <div style="font-size: 8px;"><?php echo nl2br(e($fb['yontem'] ?? '-')); ?></div>
                                    </td>
                                    <td class="fishbone-cell">
                                        <div class="fishbone-cat-title">MAKİNE</div>
                                        <div style="font-size: 8px;"><?php echo nl2br(e($fb['makine'] ?? '-')); ?></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fishbone-cell">
                                        <div class="fishbone-cat-title">MALZEME</div>
                                        <div style="font-size: 8px;"><?php echo nl2br(e($fb['malzeme'] ?? '-')); ?></div>
                                    </td>
                                    <td class="fishbone-cell">
                                        <div class="fishbone-cat-title">ÖLÇÜM</div>
                                        <div style="font-size: 8px;"><?php echo nl2br(e($fb['olcum'] ?? '-')); ?></div>
                                    </td>
                                    <td class="fishbone-cell">
                                        <div class="fishbone-cat-title">ÇEVRE</div>
                                        <div style="font-size: 8px;"><?php echo nl2br(e($fb['cevre'] ?? '-')); ?></div>
                                    </td>
                                </tr>
                            </table>
                        <?php endif; ?>

                        
                        <?php if(isset($tools['pareto']) && !empty($tools['pareto']['rows'])): ?>
                            <?php 
                                $pRows = collect($tools['pareto']['rows'])->filter(fn($r) => !empty($r['problem']) && isset($r['frequency']) && is_numeric($r['frequency']))->sortByDesc('frequency')->values();
                                $pTotal = $pRows->sum('frequency');
                                $pMax = $pRows->max('frequency') ?: 1;
                                $pCum = 0;
                            ?>
                            <?php if($pRows->isNotEmpty()): ?>
                                <div style="margin-bottom: 10px;">
                                    <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #1e40af;"><?php echo e($tools['pareto']['title'] ?? 'Pareto Analizi: Frekans Dağılımı'); ?></div>
                                    <table style="border: none;">
                                        <thead style="border-bottom: 1px solid #94a3b8;">
                                            <tr>
                                                <th width="30%" style="border:none; background:none;">Madde</th>
                                                <th width="45%" style="border:none; background:none;">Sıklık / Grafik</th>
                                                <th width="10%" style="border:none; background:none;">Adet</th>
                                                <th width="15%" style="border:none; background:none;">Kümülatif %</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $pRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php 
                                                    $pCum += (float)$row['frequency'];
                                                    $pPerc = $pTotal > 0 ? ($pCum / $pTotal) * 100 : 0;
                                                    $pBarW = ($row['frequency'] / $pMax) * 100;
                                                ?>
                                                <tr>
                                                    <td style="border:none; padding: 4px 0; font-size: 9px;"><?php echo e($row['problem']); ?></td>
                                                    <td style="border:none; padding: 4px 5px;">
                                                        <div style="background: #e2e8f0; width: 100%; height: 10px; border-radius: 2px;">
                                                            <div style="background: #3b82f6; width: <?php echo e($pBarW); ?>%; height: 100%; border-radius: 2px;"></div>
                                                        </div>
                                                    </td>
                                                    <td style="border:none; text-align: right; font-weight: bold; font-size: 9px;"><?php echo e($row['frequency']); ?></td>
                                                    <td style="border:none; text-align: right; color: #ef4444; font-weight: bold; font-size: 9px;"><?php echo e(round($pPerc)); ?>%</td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                    <div style="font-size: 8px; color: #64748b; margin-top: 5px;">
                                        <span style="display:inline-block; width:8px; height:8px; background:#3b82f6; margin-right:3px;"></span> Sıklık 
                                        <span style="display:inline-block; width:8px; height:8px; background:#ef4444; margin-left:10px; margin-right:3px;"></span> Kümülatif Hedef (%)
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        
                        <?php 
                            $swot = $tools['swot'] ?? null;
                            $hasSwot = $swot && (!empty($swot['strengths']) || !empty($swot['weaknesses']) || !empty($swot['opportunities']) || !empty($swot['threats']));
                        ?>
                        <?php if($hasSwot): ?>
                            <div style="margin-top: 10px; margin-bottom: 15px;">
                                <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #1e40af;">SWOT Analizi</div>
                                <table class="swot-table">
                                    <tr>
                                        <td class="swot-cell swot-s">
                                            <span class="swot-title"><span class="swot-icon">S</span> Güçlü Yönler</span>
                                            <div style="font-size: 8px;"><?php echo nl2br(e($swot['strengths'] ?? '-')); ?></div>
                                        </td>
                                        <td class="swot-cell swot-w">
                                            <span class="swot-title"><span class="swot-icon">W</span> Zayıf Yönler</span>
                                            <div style="font-size: 8px;"><?php echo nl2br(e($swot['weaknesses'] ?? '-')); ?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="swot-cell swot-o">
                                            <span class="swot-title"><span class="swot-icon">O</span> Fırsatlar</span>
                                            <div style="font-size: 8px;"><?php echo nl2br(e($swot['opportunities'] ?? '-')); ?></div>
                                        </td>
                                        <td class="swot-cell swot-t">
                                            <span class="swot-title"><span class="swot-icon">T</span> Tehditler</span>
                                            <div style="font-size: 8px;"><?php echo nl2br(e($swot['threats'] ?? '-')); ?></div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        <?php endif; ?>

                        
                        <?php $fourM = $tools['4m_report'] ?? null; ?>
                        
                        
                        
                        <?php if($fourM): ?>
                             <div style="margin-top: 10px; margin-bottom: 15px;">
                                <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #1e40af;">4M Analizi</div>
                                <table style="background: #f0f7ff;">
                                    <tr>
                                        <td style="padding: 5px; border: 1px solid #bfdbfe;"><strong>İnsan (Man):</strong><br><?php echo e($fourM['man'] ?? '-'); ?></td>
                                        <td style="padding: 5px; border: 1px solid #bfdbfe;"><strong>Makine (Machine):</strong><br><?php echo e($fourM['machine'] ?? '-'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px; border: 1px solid #bfdbfe;"><strong>Malzeme (Material):</strong><br><?php echo e($fourM['material'] ?? '-'); ?></td>
                                        <td style="padding: 5px; border: 1px solid #bfdbfe;"><strong>Metot (Method):</strong><br><?php echo e($fourM['method'] ?? '-'); ?></td>
                                    </tr>
                                </table>
                             </div>
                        <?php endif; ?>

                        
                        <?php if(isset($tools['action_list']) || isset($tools['task_list'])): ?>
                            <div style="margin-top: 10px;">
                                <?php if(isset($tools['action_list'])): ?>
                                    <div style="font-weight: bold; font-size: 10px; color: #1e40af; margin-bottom: 5px;">Aksiyon Listesi</div>
                                    <ul style="font-size: 9px; margin-bottom: 10px;">
                                        <?php $__currentLoopData = ($tools['action_list']['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>[<?php echo e($item['is_completed'] ? 'X' : ' '); ?>] <?php echo e($item['text']); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php endif; ?>

                                <?php if(isset($tools['task_list'])): ?>
                                    <div style="font-weight: bold; font-size: 10px; color: #1e40af; margin-bottom: 5px;">Görev Atamaları</div>
                                    <table style="font-size: 8px;">
                                        <thead>
                                            <tr style="background: #f8fafc;">
                                                <th>Görev</th>
                                                <th width="20%">Sorumlu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = ($tools['task_list']['tasks'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($task['description']); ?></td>
                                                    <td>
                                                        <?php $assignedUser = \App\Models\User::find($task['assigned_user_id']); ?>
                                                        <?php echo e($assignedUser->name ?? '-'); ?>

                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($tools['bar_chart_data']) && is_array($tools['bar_chart_data'])): ?>
                            <?php $__currentLoopData = $tools['bar_chart_data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bIndex => $bData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!empty($bData['rows'])): ?>
                                <div style="margin-top: 10px; margin-bottom: 15px;">
                                    <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #1e40af;"><?php echo e($bData['title'] ?? 'Sütun Grafiği'); ?></div>
                                    <table style="border: 1px solid #cbd5e1; width: 100%;">
                                        <thead>
                                            <tr style="background: #f1f5f9;">
                                                <th style="font-size: 9px;"><?php echo e($bData['axis_x_label'] ?? 'Kategoriler'); ?></th>
                                                <th style="font-size: 9px;"><?php echo e($bData['axis_y_label'] ?? 'Değerler'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $bData['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(isset($r['label']) && $r['label'] !== ''): ?>
                                                <tr>
                                                    <td style="font-size: 9px;"><?php echo e($r['label']); ?></td>
                                                    <td style="font-size: 9px;"><?php echo e($r['value'] ?? 0); ?></td>
                                                </tr>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>

                        <?php if(isset($tools['line_chart_data']) && is_array($tools['line_chart_data'])): ?>
                            <?php $__currentLoopData = $tools['line_chart_data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lIndex => $lData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!empty($lData['rows'])): ?>
                                <div style="margin-top: 10px; margin-bottom: 15px;">
                                    <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #1e40af;"><?php echo e($lData['title'] ?? 'Çizgi Grafiği'); ?></div>
                                    <table style="border: 1px solid #cbd5e1; width: 100%;">
                                        <thead>
                                            <tr style="background: #f1f5f9;">
                                                <th style="font-size: 9px;"><?php echo e($lData['axis_x_label'] ?? 'Kategoriler'); ?></th>
                                                <th style="font-size: 9px;"><?php echo e($lData['axis_y_label'] ?? 'Değerler'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $lData['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(isset($r['label']) && $r['label'] !== ''): ?>
                                                <tr>
                                                    <td style="font-size: 9px;"><?php echo e($r['label']); ?></td>
                                                    <td style="font-size: 9px;"><?php echo e($r['value'] ?? 0); ?></td>
                                                </tr>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>

                        
                        <?php
                            $stepVisits = \App\Models\IaaZiyaretPlani::where('iaa_id', $iaa->id)
                                ->where('iaa_workflow_step_id', $step->id)
                                ->get();
                        ?>
                        <?php if($stepVisits->isNotEmpty()): ?>
                            <div style="margin-top: 10px; margin-bottom: 15px;">
                                <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #1e40af;">Ziyaret Planı ve Sonuçları</div>
                                <?php $__currentLoopData = $stepVisits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div style="margin-bottom: 8px; padding: 8px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 4px; font-size: 9px;">
                                        <strong>Müşteri:</strong> <?php echo e($iaa->musteriSikayeti->customer->name ?? $iaa->musteriSikayeti->musteri_adi ?? 'Müşteri'); ?> |
                                        <strong>Tarih:</strong> <?php echo e($sv->visit_date ? \Carbon\Carbon::parse($sv->visit_date)->format('d.m.Y H:i') : '-'); ?> |
                                        <strong>Durum:</strong> <?php echo e($sv->status); ?><br>
                                        <strong>Personel:</strong> <?php echo e($sv->visitor_name ?? ($sv->visitor->name ?? 'Belirtilmedi')); ?> |
                                        <strong>Tahmini Dönüş:</strong> <?php echo e($sv->estimated_return_date ? \Carbon\Carbon::parse($sv->estimated_return_date)->format('d.m.Y') : '-'); ?>

                                        <?php if($sv->result): ?>
                                            <br><strong>Sonuç:</strong> <?php echo e($sv->result); ?>

                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>

                        
                        <div style="margin-top: 10px;">
                            <?php if(isset($content['form_data']) && is_array($content['form_data'])): ?>
                                <?php $__currentLoopData = $content['form_data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(isset($field['text']) && !empty($field['text'])): ?>
                                        <div style="margin-bottom: 8px; padding: 8px; background: #f8fafc; border-radius: 4px; border-left: 3px solid #1e40af; font-size: 9px; word-wrap: break-word; word-break: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                            <?php echo e($field['text']); ?>

                                        </div>
                                    <?php endif; ?>

                                    <?php $stdFiles = $field['files'] ?? $field['images'] ?? []; ?>
                                    <?php if(is_array($stdFiles) && !empty($stdFiles)): ?>
                                        <div style="margin-bottom: 8px;">
                                            <?php $__currentLoopData = $stdFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(is_string($file) && file_exists(public_path('storage/' . $file))): ?>
                                                    <?php $fileSize = filesize(public_path('storage/' . $file)); ?>
                                                    <?php if($fileSize < 10485760): ?> 
                                                        <img src="<?php echo e(public_path('storage/' . $file)); ?>" style="width: 140px; height: 140px; object-fit: cover; margin-bottom: 5px; border: 1px solid #cbd5e1; border-radius: 4px; display: inline-block; margin-right: 5px;">
                                                    <?php else: ?>
                                                        <div style="font-size: 8px; color: #64748b; padding: 4px; border: 1px solid #cbd5e1; border-radius: 4px; margin-bottom: 5px; background-color: #f8fafc; display: inline-block;">
                                                            [Büyük Görsel: <?php echo e(round($fileSize/1024/1024, 2)); ?> MB]
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if(isset($field['before_text']) || isset($field['after_text'])): ?>
                                        <div style="margin-bottom: 10px; font-size: 9px;">
                                            <div style="font-weight: bold; color: #1e40af; margin-bottom: 4px;">Önce/Sonra Karşılaştırma</div>
                                            <table style="width: 100%; border: none;">
                                                <tr>
                                                    <td style="width: 50%; vertical-align: top; padding: 5px; border: 1px solid #fca5a5; background: #fef2f2;">
                                                        <strong style="color: #ef4444;">ÖNCE</strong><br>
                                                        <?php 
                                                            $bImgs = $field['before_images'] ?? [];
                                                            if (empty($bImgs) && !empty($field['before_image_path'])) $bImgs = [$field['before_image_path']];
                                                        ?>
                                                        <?php if(!empty($bImgs)): ?>
                                                            <div style="margin-top: 5px; margin-bottom: 5px;">
                                                                <?php $__currentLoopData = $bImgs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php if(is_string($file) && file_exists(public_path('storage/' . $file))): ?>
                                                                        <?php $fileSize = filesize(public_path('storage/' . $file)); ?>
                                                                        <?php if($fileSize < 10485760): ?> 
                                                                            <img src="<?php echo e(public_path('storage/' . $file)); ?>" style="width: 140px; height: 140px; object-fit: cover; margin-bottom: 5px; border: 1px solid #fca5a5; border-radius: 4px; display: inline-block; margin-right: 5px;">
                                                                        <?php else: ?>
                                                                            <div style="font-size: 8px; color: #64748b; padding: 4px; border: 1px solid #fca5a5; border-radius: 4px; margin-bottom: 5px; background-color: #fff1f1;">
                                                                                [Büyük Görsel: <?php echo e(round($fileSize/1024/1024, 2)); ?> MB]
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    <?php endif; ?>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div style="word-wrap: break-word; word-break: break-word; white-space: pre-wrap; margin-top: 5px;"><?php echo e($field['before_text'] ?? '-'); ?></div>
                                                    </td>
                                                    <td style="width: 50%; vertical-align: top; padding: 5px; border: 1px solid #86efac; background: #f0fdf4;">
                                                        <strong style="color: #10b981;">SONRA</strong><br>
                                                        <?php 
                                                            $aImgs = $field['after_images'] ?? [];
                                                            if (empty($aImgs) && !empty($field['after_image_path'])) $aImgs = [$field['after_image_path']];
                                                        ?>
                                                        <?php if(!empty($aImgs)): ?>
                                                            <div style="margin-top: 5px; margin-bottom: 5px;">
                                                                <?php $__currentLoopData = $aImgs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php if(is_string($img) && file_exists(public_path('storage/' . $img))): ?>
                                                                        <?php $imgSize = filesize(public_path('storage/' . $img)); ?>
                                                                        <?php if($imgSize < 10485760): ?> 
                                                                            <img src="<?php echo e(public_path('storage/' . $img)); ?>" style="width: 140px; height: 140px; object-fit: cover; margin-bottom: 5px; border: 1px solid #86efac; border-radius: 4px; display: inline-block; margin-right: 5px;">
                                                                        <?php else: ?>
                                                                            <div style="font-size: 8px; color: #64748b; padding: 4px; border: 1px solid #86efac; border-radius: 4px; margin-bottom: 5px; background-color: #f0fdf4;">
                                                                                [Büyük Görsel: <?php echo e(round($imgSize/1024/1024, 2)); ?> MB]
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    <?php endif; ?>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div style="word-wrap: break-word; word-break: break-word; white-space: pre-wrap; margin-top: 5px;"><?php echo e($field['after_text'] ?? '-'); ?></div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
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
                                            <div style="margin-bottom: 8px; padding: 8px; background: #f1f5f9; border-radius: 4px; font-size: 9px;">
                                                <strong>Seçilen Kullanıcılar:</strong> <?php echo e(implode(', ', $uNames)); ?>

                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                
                
                <?php
                    $stepTools = \App\Models\IaaStepTool::where('iaa_id', $iaa->id)
                        ->where('iaa_workflow_step_id', $step->id)
                        ->orderBy('order')
                        ->get();
                ?>
                <?php $__currentLoopData = $stepTools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $tData = is_array($tool->data) ? $tool->data : json_decode($tool->data, true); ?>
                    
                    
                    <?php if($tool->tool_type === 'pareto' && isset($tData['items']) && count($tData['items']) > 0): ?>
                        <?php 
                            $pRows = collect($tData['items'])->filter(fn($r) => !empty($r['category']) && is_numeric($r['frequency']))->sortByDesc('frequency')->values();
                            $pTotal = $pRows->sum('frequency');
                            $pMax = $pRows->max('frequency') ?: 1;
                            $pCum = 0;
                        ?>
                        <?php if($pRows->isNotEmpty()): ?>
                            <div style="margin-top: 10px; margin-bottom: 15px;">
                                <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #1e40af;"><?php echo e($tool->title ?? 'Pareto Analizi'); ?></div>
                                <table style="border: none;">
                                    <thead style="border-bottom: 1px solid #94a3b8;">
                                        <tr>
                                            <th width="30%" style="border:none; background:none;">Kategori</th>
                                            <th width="45%" style="border:none; background:none;">Sıklık / Grafik</th>
                                            <th width="10%" style="border:none; background:none;">Adet</th>
                                            <th width="15%" style="border:none; background:none;">Kümülatif %</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $pRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php 
                                                $pCum += (float)$row['frequency'];
                                                $pPerc = $pTotal > 0 ? ($pCum / $pTotal) * 100 : 0;
                                                $pBarW = ($row['frequency'] / $pMax) * 100;
                                            ?>
                                            <tr>
                                                <td style="border:none; padding: 4px 0; font-size: 9px;"><?php echo e($row['category']); ?></td>
                                                <td style="border:none; padding: 4px 5px;">
                                                    <div style="background: #e2e8f0; width: 100%; height: 10px; border-radius: 2px;">
                                                        <div style="background: #3b82f6; width: <?php echo e($pBarW); ?>%; height: 100%; border-radius: 2px;"></div>
                                                    </div>
                                                </td>
                                                <td style="border:none; text-align: right; font-weight: bold; font-size: 9px;"><?php echo e($row['frequency']); ?></td>
                                                <td style="border:none; text-align: right; color: #ef4444; font-weight: bold; font-size: 9px;"><?php echo e(round($pPerc)); ?>%</td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    
                    
                    <?php elseif($tool->tool_type === 'fishbone'): ?>
                        <?php $cats = $tData['categories'] ?? []; ?>
                        <table class="fishbone-table">
                            <tr>
                                <td colspan="3" class="fishbone-header">
                                    <?php echo e($tool->title ?? 'BALIK KILÇIĞI ANALİZİ'); ?> 
                                    <?php if(!empty($tData['problem_statement'])): ?> - PROBLEM: <?php echo e($tData['problem_statement']); ?> <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="fishbone-cell">
                                    <div class="fishbone-cat-title">İNSAN</div>
                                    <ul style="font-size: 8px; margin:0; padding-left:10px;">
                                        <?php $__currentLoopData = ($cats['insan']??[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($item['text'] ?? $item); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </td>
                                <td class="fishbone-cell">
                                    <div class="fishbone-cat-title">YÖNTEM</div>
                                    <ul style="font-size: 8px; margin:0; padding-left:10px;">
                                        <?php $__currentLoopData = ($cats['metot']??[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($item['text'] ?? $item); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </td>
                                <td class="fishbone-cell">
                                    <div class="fishbone-cat-title">MAKİNE</div>
                                    <ul style="font-size: 8px; margin:0; padding-left:10px;">
                                        <?php $__currentLoopData = ($cats['makine']??[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($item['text'] ?? $item); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td class="fishbone-cell">
                                    <div class="fishbone-cat-title">MALZEME</div>
                                    <ul style="font-size: 8px; margin:0; padding-left:10px;">
                                        <?php $__currentLoopData = ($cats['malzeme']??[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($item['text'] ?? $item); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </td>
                                <td class="fishbone-cell">
                                    <div class="fishbone-cat-title">ÖLÇÜM</div>
                                    <ul style="font-size: 8px; margin:0; padding-left:10px;">
                                        <?php $__currentLoopData = ($cats['olcum']??[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($item['text'] ?? $item); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </td>
                                <td class="fishbone-cell">
                                    <div class="fishbone-cat-title">ÇEVRE</div>
                                    <ul style="font-size: 8px; margin:0; padding-left:10px;">
                                        <?php $__currentLoopData = ($cats['cevre']??[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($item['text'] ?? $item); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    
                    
                    <?php elseif($tool->tool_type === '4m_report'): ?>
                        <div style="margin-top: 10px; margin-bottom: 15px;">
                            <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #1e40af;"><?php echo e($tool->title ?? '4M Gelişim Raporu'); ?></div>
                            <table style="background: #f0f7ff;">
                                <tr>
                                    <td style="padding: 5px; border: 1px solid #bfdbfe;"><strong>İnsan (Man):</strong><br><?php echo nl2br(e($tData['items']['man'] ?? '-')); ?></td>
                                    <td style="padding: 5px; border: 1px solid #bfdbfe;"><strong>Makine (Machine):</strong><br><?php echo nl2br(e($tData['items']['machine'] ?? '-')); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px; border: 1px solid #bfdbfe;"><strong>Malzeme (Material):</strong><br><?php echo nl2br(e($tData['items']['material'] ?? '-')); ?></td>
                                    <td style="padding: 5px; border: 1px solid #bfdbfe;"><strong>Metot (Method):</strong><br><?php echo nl2br(e($tData['items']['method'] ?? '-')); ?></td>
                                </tr>
                            </table>
                        </div>

                    
                    <?php elseif($tool->tool_type === 'checklist'): ?>
                        <div style="margin-top: 10px; margin-bottom: 15px;">
                            <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #1e40af;"><?php echo e($tool->title ?? 'Kontrol Listesi'); ?></div>
                            <ul style="font-size: 9px; margin:0; padding-left:10px;">
                                <?php $__currentLoopData = ($tData['items']??[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>[<?php echo e(!empty($item['checked']) ? 'X' : ' '); ?>] <?php echo e($item['text']); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    
                    
                    <?php elseif($tool->tool_type === 'action_list'): ?>
                        <div style="margin-top: 10px; margin-bottom: 15px;">
                            <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #1e40af;"><?php echo e($tool->title ?? 'Aksiyon Listesi'); ?></div>
                            <table style="font-size: 8px;">
                                <thead>
                                    <tr style="background: #f8fafc;">
                                        <th>Durum</th>
                                        <th>Aksiyon</th>
                                        <th>Sorumlu</th>
                                        <th>Tarih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = ($tData['items']??[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>[<?php echo e(!empty($item['status']) && $item['status'] == 'completed' ? 'X' : ' '); ?>]</td>
                                            <td><?php echo e($item['action'] ?? ''); ?></td>
                                            <td><?php echo e($item['owner'] ?? ''); ?></td>
                                            <td><?php echo e($item['target_date'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    
                    
                    <?php elseif($tool->tool_type === 'swot'): ?>
                        <div style="margin-top: 10px; margin-bottom: 15px;">
                            <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #1e40af;"><?php echo e($tool->title ?? 'SWOT Analizi'); ?></div>
                            <table class="swot-table">
                                <tr>
                                    <td class="swot-cell swot-s">
                                        <span class="swot-title"><span class="swot-icon">S</span> Güçlü Yönler</span>
                                        <div style="font-size: 8px;"><?php echo nl2br(e($tData['strengths'] ?? '-')); ?></div>
                                    </td>
                                    <td class="swot-cell swot-w">
                                        <span class="swot-title"><span class="swot-icon">W</span> Zayıf Yönler</span>
                                        <div style="font-size: 8px;"><?php echo nl2br(e($tData['weaknesses'] ?? '-')); ?></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="swot-cell swot-o">
                                        <span class="swot-title"><span class="swot-icon">O</span> Fırsatlar</span>
                                        <div style="font-size: 8px;"><?php echo nl2br(e($tData['opportunities'] ?? '-')); ?></div>
                                    </td>
                                    <td class="swot-cell swot-t">
                                        <span class="swot-title"><span class="swot-icon">T</span> Tehditler</span>
                                        <div style="font-size: 8px;"><?php echo nl2br(e($tData['threats'] ?? '-')); ?></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                    
                    <?php elseif($tool->tool_type === '5why'): ?>
                        <?php if(!empty($tData['whys']) && count(array_filter($tData['whys'])) > 0): ?>
                            <div style="margin-top: 10px; margin-bottom: 15px;">
                                <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #4338ca;"><?php echo e($tool->title ?? '5 Neden Analizi'); ?></div>
                                <?php if(!empty($tData['problemStatement'])): ?>
                                    <div style="font-weight: bold; font-size: 9px; margin-bottom: 5px;">PROBLEM: <?php echo e($tData['problemStatement']); ?></div>
                                <?php endif; ?>
                                <div style="background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 4px; padding: 8px;">
                                    <?php $__currentLoopData = $tData['whys']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $why): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(!empty($why)): ?>
                                            <div style="margin-bottom: 5px;">
                                                <span style="font-weight: bold; color: #6d28d9; font-size: 9px;"><?php echo e($index + 1); ?>. Neden?</span>
                                                <div style="font-size: 9px; padding-left: 10px; border-left: 2px solid #ddd6fe; margin-top: 2px;"><?php echo e($why); ?></div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!empty($tData['rootCause'])): ?>
                                        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #c4b5fd;">
                                            <span style="font-weight: bold; color: #b91c1c; font-size: 9px;">KÖK NEDEN:</span>
                                            <div style="font-size: 9px; padding-left: 10px; color: #b91c1c;"><?php echo e($tData['rootCause']); ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                    
                    <?php elseif($tool->tool_type === 'chart'): ?>
                        <?php if(!empty($tData['labels']) && (!empty($tData['values']) || !empty($tData['series']))): ?>
                            <div style="margin-top: 10px; margin-bottom: 15px;">
                                <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #e11d48;"><?php echo e($tool->title ?? 'Grafik Analizi'); ?> <?php if(!empty($tData['chartTitle'])): ?> - <?php echo e($tData['chartTitle']); ?> <?php endif; ?></div>
                                <table style="border: 1px solid #cbd5e1; width: 100%; table-layout: fixed;">
                                    <thead>
                                        <tr style="background: #f1f5f9;">
                                            <th style="font-size: 9px; padding: 4px; word-wrap: break-word;">Kategori</th>
                                            <?php if(!empty($tData['series'])): ?>
                                                <?php $__currentLoopData = $tData['series']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <th style="font-size: 9px; padding: 4px;"><?php echo e($s['name'] ?? 'Değer'); ?></th>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                <th style="font-size: 9px; padding: 4px;">Değer</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $tData['labels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td style="font-size: 9px; padding: 4px;"><?php echo e($label); ?></td>
                                                <?php if(!empty($tData['series'])): ?>
                                                    <?php $__currentLoopData = $tData['series']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <td style="font-size: 9px; padding: 4px;"><?php echo e($s['data'][$index] ?? 0); ?></td>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php else: ?>
                                                    <td style="font-size: 9px; padding: 4px;"><?php echo e($tData['values'][$index] ?? 0); ?></td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                    <?php endif; ?>
                <?php else: ?>
                    <p style="font-style: italic; color: #94a3b8; font-size: 9px; text-align: center; padding: 10px; background: #f8fafc;">Bu adım henüz tamamlanmamıştır.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php if(isset($iade) && $iade): ?>
    <div class="section">
        <div class="section-title">4. İade ve Hurda Bildirimi</div>
        <table>
            <tr>
                <th width="20%">Ürün Grubu</th>
                <td width="30%"><?php echo e($iade->urun_turu ?? '-'); ?></td>
                <th width="20%">İade Sebebi</th>
                <td width="30%"><?php echo e($iade->iade_sebebi ?? '-'); ?></td>
            </tr>
            <tr>
                <th>İade Miktarı</th>
                <td><?php echo e(floatval($iade->miktar)); ?> <?php echo e($iade->birim ?? ''); ?></td>
                <th>Toplam Parti</th>
                <td><?php echo e(floatval($iade->toplam_parti_miktari)); ?> <?php echo e($iade->birim ?? ''); ?></td>
            </tr>
            <tr>
                <th>İade Oranı</th>
                <td>
                    <?php
                        $toplam = floatval($iade->toplam_parti_miktari);
                        $miktar = floatval($iade->miktar);
                        $oran = ($toplam > 0) ? ($miktar / $toplam) * 100 : 0;
                    ?>
                    %<?php echo e(number_format($oran, 1)); ?>

                </td>
                <th>İade Tarihi</th>
                <td><?php echo e($iade->iade_tarihi ? $iade->iade_tarihi->format('d.m.Y') : '-'); ?></td>
            </tr>
            <?php if($iade->aciklama): ?>
            <tr>
                <th>Açıklama</th>
                <td colspan="3">
                    <?php if(isset($isCustomerView) && $isCustomerView && !$iade->musteri_gorebilir_mi): ?>
                        <span style="color: #94a3b8; font-style: italic;">Bu bölüm sadece şirket içi bilgilendirme amaçlıdır.</span>
                    <?php else: ?>
                        <?php echo e($iade->aciklama); ?>

                    <?php endif; ?>
                </td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
    <?php endif; ?>

    
    <?php if(isset($visitData) && $visitData): ?>
    <div class="section">
        <div class="section-title"><?php echo e(isset($iade) && $iade ? '5' : '4'); ?>. Müşteri Ziyaret Bilgileri</div>
        <table>
            <tr>
                <th width="20%">Ziyaret Tarihi</th>
                <td width="30%"><?php echo e(isset($visitData['visit_date']) ? \Carbon\Carbon::parse($visitData['visit_date'])->format('d.m.Y H:i') : '-'); ?></td>
                <th width="20%">Ziyaret Sebebi</th>
                <td width="30%"><?php echo e($visitData['visit_reason'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Lot No</th>
                <td><?php echo e($visitData['lot_no'] ?? '-'); ?></td>
                <th>Barkod</th>
                <td><?php echo e($visitData['barcode'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Görüşülen Kişiler</th>
                <td colspan="3">
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
                <th>Tespitler</th>
                <td colspan="3"><?php echo e($visitData['findings']); ?></td>
            </tr>
            <?php endif; ?>
            <?php if(!empty($visitData['result'])): ?>
            <tr>
                <th>Sonuç</th>
                <td colspan="3"><?php echo e($visitData['result']); ?></td>
            </tr>
            <?php endif; ?>
            <?php if(!empty($visitData['visit_notes'])): ?>
                <?php if(!(isset($isCustomerView) && $isCustomerView)): ?>
                <tr>
                    <th>Notlar</th>
                    <td colspan="3"><?php echo e($visitData['visit_notes']); ?></td>
                </tr>
                <?php endif; ?>
            <?php endif; ?>
        </table>
    </div>
    <?php endif; ?>

    <div class="footer">
        Bu rapor sistemsel olarak <?php echo e(auth()->user()->name); ?> tarafından oluşturulmuştur. | Sayfa <script type="text/php">echo $PAGE_NUM . " / " . $PAGE_COUNT;</script>
    </div>
</body>
</html>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/export/pdf.blade.php ENDPATH**/ ?>