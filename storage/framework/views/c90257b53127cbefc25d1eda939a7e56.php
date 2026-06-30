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
        th, td { border: 1px solid #cbd5e1; padding: 5px; text-align: left; word-wrap: break-word; }
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
            <div style="margin-bottom: 20px; border: 1px solid #e2e8f0; padding: 10px; page-break-inside: avoid;">
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

                        
                        <div style="margin-top: 10px;">
                            <?php if(isset($content['form_data']) && is_array($content['form_data'])): ?>
                                <?php $__currentLoopData = $content['form_data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(isset($field['text']) && !empty($field['text'])): ?>
                                        <div style="margin-bottom: 8px; padding: 8px; background: #f8fafc; border-radius: 4px; border-left: 3px solid #1e40af; font-size: 9px;">
                                            <?php echo e($field['text']); ?>

                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p style="font-style: italic; color: #94a3b8; font-size: 9px; text-align: center; padding: 10px; background: #f8fafc;">Bu adım henüz tamamlanmamıştır.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php if(isset($iade) && $iade): ?>
    <div class="section" style="page-break-inside: avoid;">
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
    <div class="section" style="page-break-inside: avoid;">
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
<?php /**PATH /var/www/kys_koksan/iaa/resources/views/proje-calisma-alani/export/pdf.blade.php ENDPATH**/ ?>