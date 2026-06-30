<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f7f6;
            padding-bottom: 40px;
        }

        .main-table {
            background-color: #ffffff;
            margin: 0 auto;
            width: 600px;
            max-width: 600px;
            border-spacing: 0;
            font-family: sans-serif;
            color: #333333;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        /* HEADER */
        .header {
            background-color: #4f46e5;
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ffffff !important;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 13px;
            opacity: 0.8;
            color: #e0e7ff !important;
        }

        /* SECTIONS */
        .section-title {
            padding: 20px 25px 10px;
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            border-bottom: 2px solid #f3f4f6;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .dot {
            height: 12px;
            width: 12px;
            background-color: #374151;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }

        .content-block {
            padding: 0 25px 20px;
        }

        /* GRID / TABLES */
        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
        }

        .summary-item {
            display: table-cell;
            width: 25%;
            background: #f9fafb;
            padding: 10px;
            text-align: center;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .summary-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
        }

        /* TABLO AYARLARI */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 13px;
        }

        /* Sayılar SAĞA yaslı */
        .data-table th,
        .data-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
            text-align: right;
        }

        .data-table th {
            background-color: #f3f4f6;
            color: #4b5563;
            font-weight: 600;
            border-bottom: 2px solid #e5e7eb;
        }

        /* İlk sütun SOLA yaslı (Metinler) */
        .data-table th:first-child,
        .data-table td:first-child {
            text-align: left;
            width: 40%;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        /* STATUS COLORS */
        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            color: white;
            display: inline-block;
            white-space: nowrap;
        }

        .bg-new {
            background-color: #ef4444;
        }

        .bg-process {
            background-color: #f59e0b;
        }

        .bg-done {
            background-color: #10b981;
        }

        .bg-blue {
            background-color: #3b82f6;
        }

        /* EKSTRA BİLGİ (Geçmişten Kapanan) */
        .extra-info {
            font-size: 9px;
            color: #10b981;
            display: block;
            font-weight: normal;
            margin-top: 2px;
        }

        /* HIGHLIGHT BOX */
        .highlight-box {
            background-color: #f0fdf4;
            border: 1px solid #dcfce7;
            border-radius: 6px;
            padding: 10px;
            font-size: 12px;
            color: #166534;
            margin-bottom: 5px;
            text-align: left;
        }

        .highlight-title {
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
            text-transform: uppercase;
            font-size: 10px;
        }

        /* FOOTER */
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>
    <center class="wrapper">
        <table class="main-table">
            <tr>
                <td class="header">
                    <h1><?php echo e($raporBasligi); ?></h1>
                    <p><?php echo e($tarih); ?> tarihli sistem durum raporudur.</p>
                </td>
            </tr>

            <?php if(isset($raporData['sikayet_genel'])): ?>
                <tr>
                    <td>
                        <div class="section-title">
                            <span class="dot" style="background-color: #db2777;"></span> MÜŞTERİ ŞİKAYETLERİ
                        </div>
                        <div class="content-block">

                            <div class="summary-grid">
                                <div class="summary-item" style="background-color: #fdf2f8; border-color: #fce7f3;">
                                    <div class="summary-label" style="color: #be185d;">Toplam</div>
                                    <div class="summary-value" style="color: #be185d;">
                                        <a href="<?php echo e(route('admin.reports.daily_complaints')); ?>"
                                            style="text-decoration:none; color:inherit;">
                                            <?php echo e($raporData['sikayet_genel']['toplam_kayit']); ?>

                                        </a>
                                    </div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Yeni</div>
                                    <div class="summary-value" style="color: #ef4444;">
                                        <a href="<?php echo e(route('admin.reports.daily_complaints')); ?>"
                                            style="text-decoration:none; color:inherit;">
                                            <?php echo e($raporData['sikayet_genel']['bekleyen_yeni']); ?>

                                        </a>
                                    </div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">İşlemde</div>
                                    <div class="summary-value" style="color: #f59e0b;">
                                        <a href="<?php echo e(route('admin.reports.daily_complaints')); ?>"
                                            style="text-decoration:none; color:inherit;">
                                            <?php echo e($raporData['sikayet_genel']['islemde_olan']); ?>

                                        </a>
                                    </div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Çözülen</div>
                                    <div class="summary-value" style="color: #10b981;">
                                        <a href="<?php echo e(route('admin.reports.daily_complaints')); ?>"
                                            style="text-decoration:none; color:inherit;">
                                            <?php echo e($raporData['sikayet_genel']['cozumlenen']); ?>

                                        </a>
                                    </div>
                                </div>
                            </div>

                            <table class="data-table" style="margin-top: 15px;">
                                <thead>
                                    <tr>
                                        <th>Dönem</th>
                                        <th>Gelen</th>
                                        <th>Kapanan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Bugün</strong></td>
                                        <td><?php echo e($raporData['sikayet_zaman']['bugun']['gelen']); ?></td>
                                        <td>
                                            <?php echo e($raporData['sikayet_zaman']['bugun']['kapanan']); ?>

                                            <?php if($raporData['sikayet_zaman']['bugun']['kapanan'] > $raporData['sikayet_zaman']['bugun']['gelen']): ?>
                                                <span class="extra-info">(Geçmişten:
                                                    +<?php echo e($raporData['sikayet_zaman']['bugun']['kapanan'] - $raporData['sikayet_zaman']['bugun']['gelen']); ?>)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bu Hafta / Geçen H.</td>
                                        <td><?php echo e($raporData['sikayet_zaman']['bu_hafta']['gelen']); ?> / <span
                                                style="color:#9ca3af;"><?php echo e($raporData['sikayet_zaman']['gecen_hafta']['gelen']); ?></span>
                                        </td>
                                        <td>
                                            <?php echo e($raporData['sikayet_zaman']['bu_hafta']['kapanan']); ?> / <span
                                                style="color:#9ca3af;"><?php echo e($raporData['sikayet_zaman']['gecen_hafta']['kapanan']); ?></span>
                                            
                                            <?php if($raporData['sikayet_zaman']['bu_hafta']['kapanan'] > $raporData['sikayet_zaman']['bu_hafta']['gelen']): ?>
                                                <span class="extra-info">(Geçmişten:
                                                    +<?php echo e($raporData['sikayet_zaman']['bu_hafta']['kapanan'] - $raporData['sikayet_zaman']['bu_hafta']['gelen']); ?>)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bu Ay / Geçen Ay</td>
                                        <td><?php echo e($raporData['sikayet_zaman']['bu_ay']['gelen']); ?> / <span
                                                style="color:#9ca3af;"><?php echo e($raporData['sikayet_zaman']['gecen_ay']['gelen']); ?></span>
                                        </td>
                                        <td>
                                            <?php echo e($raporData['sikayet_zaman']['bu_ay']['kapanan']); ?> / <span
                                                style="color:#9ca3af;"><?php echo e($raporData['sikayet_zaman']['gecen_ay']['kapanan']); ?></span>
                                            <?php if($raporData['sikayet_zaman']['bu_ay']['kapanan'] > $raporData['sikayet_zaman']['bu_ay']['gelen']): ?>
                                                <span class="extra-info">(Geçmişten:
                                                    +<?php echo e($raporData['sikayet_zaman']['bu_ay']['kapanan'] - $raporData['sikayet_zaman']['bu_ay']['gelen']); ?>)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bu Yıl / Geçen Yıl</td>
                                        <td><?php echo e($raporData['sikayet_zaman']['bu_yil']['gelen']); ?> / <span
                                                style="color:#9ca3af;"><?php echo e($raporData['sikayet_zaman']['gecen_yil']['gelen']); ?></span>
                                        </td>
                                        <td>
                                            <?php echo e($raporData['sikayet_zaman']['bu_yil']['kapanan']); ?> / <span
                                                style="color:#9ca3af;"><?php echo e($raporData['sikayet_zaman']['gecen_yil']['kapanan']); ?></span>
                                            <?php if($raporData['sikayet_zaman']['bu_yil']['kapanan'] > $raporData['sikayet_zaman']['bu_yil']['gelen']): ?>
                                                <span class="extra-info">(Geçmişten:
                                                    +<?php echo e($raporData['sikayet_zaman']['bu_yil']['kapanan'] - $raporData['sikayet_zaman']['bu_yil']['gelen']); ?>)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <?php if(isset($raporData['sikayet_ceyrekler'])): ?>
                                <table class="data-table" style="margin-top: 15px;">
                                    <thead>
                                        <tr>
                                            <th>Çeyrek</th>
                                            <th>Gelen</th>
                                            <th>Kapanan</th>
                                            <th>Başarı</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $raporData['sikayet_ceyrekler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $qData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><strong><?php echo e(date('Y')); ?> <?php echo e($key); ?></strong></td>
                                                <td><?php echo e($qData['gelen']); ?></td>
                                                <td>
                                                    <?php echo e($qData['kapanan']); ?>

                                                    
                                                    <?php if($qData['kapanan'] > $qData['gelen']): ?>
                                                        <span class="extra-info" style="color:#10b981;">(Geçmişten:
                                                            +<?php echo e($qData['kapanan'] - $qData['gelen']); ?>)</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($qData['gelen'] > 0): ?>
                                                        <span
                                                            style="font-size:10px; color:#6b7280;">%<?php echo e(round(($qData['kapanan'] / $qData['gelen']) * 100)); ?></span>
                                                    <?php else: ?>
                                                        <span style="font-size:10px; color:#ccc;">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>

                            <?php if(isset($raporData['sikayet_bolumler'])): ?>
                                <div
                                    style="margin-top: 15px; font-weight: bold; font-size: 13px; color: #374151; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px;">
                                    Kategori Bazlı Dağılım</div>
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Kategori</th>
                                            <th>Top.</th>
                                            <th>Yeni</th>
                                            <th>İşl.</th>
                                            <th>Bit.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $raporData['sikayet_bolumler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <a href="<?php echo e(route('admin.reports.daily_complaints', ['search' => $row['kategori_adi']])); ?>"
                                                        style="text-decoration:none; color:inherit;">
                                                        <?php echo e($row['kategori_adi']); ?>

                                                    </a>
                                                </td>
                                                <td style="font-weight:bold;">
                                                    <a href="<?php echo e(route('admin.reports.daily_complaints', ['search' => $row['kategori_adi']])); ?>"
                                                        style="text-decoration:none; color:inherit;">
                                                        <?php echo e($row['toplam']); ?>

                                                    </a>
                                                </td>
                                                <td><?php if($row['yeni'] > 0): ?> <span class="badge bg-new"><?php echo e($row['yeni']); ?></span> <?php else: ?>
                                                - <?php endif; ?></td>
                                                <td><?php if($row['islemde'] > 0): ?> <span
                                                class="badge bg-process"><?php echo e($row['islemde']); ?></span> <?php else: ?> - <?php endif; ?></td>
                                                <td><?php if($row['kapali'] > 0): ?> <span class="badge bg-done"><?php echo e($row['kapali']); ?></span>
                                                <?php else: ?> - <?php endif; ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>

                        </div>
                    </td>
                </tr>
            <?php endif; ?>

            <?php if(isset($raporData['iaa_ozet'])): ?>
                <tr>
                    <td>
                        <div class="section-title">
                            <span class="dot" style="background-color: #2563eb;"></span> İAA ÖNERİ SİSTEMİ
                        </div>
                        <div class="content-block">

                            <div class="summary-grid" style="margin-bottom: 10px;">
                                <div class="summary-item"
                                    style="background-color: #eff6ff; border-color: #dbeafe; width: 33%;">
                                    <div class="summary-label" style="color: #1e40af;">Genel Toplam Tamamlanan</div>
                                    <div class="summary-value" style="color: #1e40af;">
                                        <?php echo e($raporData['iaa_ozet']['genel_tamamlanan']); ?> <span
                                            style="font-size:10px; color:#93c5fd; font-weight:normal;">/
                                            <?php echo e($raporData['iaa_ozet']['toplam']); ?></span>
                                    </div>
                                </div>
                                <div class="summary-item" style="width: 33%;">
                                    <div class="summary-label">Bu Yıl Tamamlanan</div>
                                    <div class="summary-value" style="color: #2563eb;">
                                        <?php echo e($raporData['iaa_ozet']['bu_yil_biten']); ?>

                                    </div>
                                </div>
                                <div class="summary-item" style="width: 33%;">
                                    <div class="summary-label">Bu Ay Tamamlanan</div>
                                    <div class="summary-value" style="color: #3b82f6;">
                                        <?php echo e($raporData['iaa_ozet']['bu_ay_biten']); ?>

                                    </div>
                                </div>
                            </div>

                            <div style="font-size: 11px; color: #6b7280; margin-bottom: 5px;"><strong>Güncel
                                    Bekleyenler:</strong></div>
                            <div class="summary-grid">
                                <div class="summary-item" style="width: 33%;">
                                    <div class="summary-label">Havuzda</div>
                                    <div class="summary-value"><?php echo e($raporData['iaa_ozet']['havuz']); ?></div>
                                </div>
                                <div class="summary-item" style="width: 33%;">
                                    <div class="summary-label">Devam Eden</div>
                                    <div class="summary-value" style="color: #f59e0b;"><?php echo e($raporData['iaa_ozet']['devam']); ?>

                                    </div>
                                </div>
                                <div class="summary-item" style="width: 33%;">
                                    <div class="summary-label">Ort. Çözüm Hızı</div>
                                    <div class="summary-value" style="color: #059669; font-size: 14px;">
                                        <?php echo e($raporData['iaa_hiz']); ?>

                                    </div>
                                </div>
                            </div>

                            <table class="data-table" style="margin-top: 15px;">
                                <thead>
                                    <tr>
                                        <th>Dönem</th>
                                        <th>Gelen Öneri</th>
                                        <th>Tamamlanan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Bu Hafta / Geçen H.</td>
                                        <td><?php echo e($raporData['iaa_zaman']['bu_hafta']['gelen']); ?> / <span
                                                style="color:#9ca3af;"><?php echo e($raporData['iaa_zaman']['gecen_hafta']['gelen']); ?></span>
                                        </td>
                                        <td><?php echo e($raporData['iaa_zaman']['bu_hafta']['biten']); ?> / <span
                                                style="color:#9ca3af;"><?php echo e($raporData['iaa_zaman']['gecen_hafta']['biten']); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bu Ay / Geçen Ay</td>
                                        <td><?php echo e($raporData['iaa_zaman']['bu_ay']['gelen']); ?> / <span
                                                style="color:#9ca3af;"><?php echo e($raporData['iaa_zaman']['gecen_ay']['gelen']); ?></span>
                                        </td>
                                        <td><?php echo e($raporData['iaa_zaman']['bu_ay']['biten']); ?> / <span
                                                style="color:#9ca3af;"><?php echo e($raporData['iaa_zaman']['gecen_ay']['biten']); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bu Yıl / Geçen Yıl</td>
                                        <td><?php echo e($raporData['iaa_zaman']['bu_yil']['gelen']); ?> / <span
                                                style="color:#9ca3af;"><?php echo e($raporData['iaa_zaman']['gecen_yil']['gelen']); ?></span>
                                        </td>
                                        <td><?php echo e($raporData['iaa_zaman']['bu_yil']['biten']); ?> / <span
                                                style="color:#9ca3af;"><?php echo e($raporData['iaa_zaman']['gecen_yil']['biten']); ?></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <?php if(isset($raporData['iaa_durum_detay']) && count($raporData['iaa_durum_detay']) > 0): ?>
                                <div
                                    style="margin-top:15px; font-size:11px; font-weight:bold; color:#374151; border-bottom:1px solid #e5e7eb; padding-bottom:5px;">
                                    Detaylı Durum Dağılımı
                                </div>
                                <table class="data-table">
                                    <?php $__currentLoopData = $raporData['iaa_durum_detay']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $durum => $sayi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($durum); ?></td>
                                            <td><strong><?php echo e($sayi); ?></strong></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </table>
                            <?php endif; ?>

                            <div
                                style="margin-top: 15px; background: #f9fafb; padding: 10px; border-radius: 6px; font-size: 11px; color: #4b5563;">
                                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                                    <div style="flex: 1;">
                                        <div class="highlight-box">
                                            <span class="highlight-title">🏆 En Çok Öneri Veren Bölüm</span>
                                            <?php echo e($raporData['iaa_en_cok_bolum']); ?>

                                        </div>
                                    </div>
                                    <div style="flex: 1;">
                                        <div class="highlight-box"
                                            style="background-color:#fff7ed; border-color:#ffedd5; color:#9a3412;">
                                            <span class="highlight-title">🚀 En Hızlı Çözen Takım</span>
                                            <?php echo e($raporData['iaa_en_cok_takim']); ?>

                                        </div>
                                    </div>
                                </div>
                                <?php if($raporData['iaa_son']): ?>
                                    <div style="border-top: 1px dashed #e5e7eb; padding-top: 5px;">
                                        <strong>📅 Son Öneri:</strong> <?php echo e($raporData['iaa_son']['tarih']); ?>

                                        <span class="badge"
                                            style="background-color: #6b7280; font-size: 9px; padding: 1px 4px;"><?php echo e($raporData['iaa_son']['tur']); ?></span>
                                        <br>
                                        <i>"<?php echo e(Str::limit($raporData['iaa_son']['baslik'], 50)); ?>"</i>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </td>
                </tr>
            <?php endif; ?>

            <?php if(isset($raporData['disiplin']) || isset($raporData['arabuluculuk'])): ?>
                <tr>
                    <td>
                        <div class="section-title">
                            <span class="dot" style="background-color: #d97706;"></span> DİĞER SÜREÇLER
                        </div>
                        <div class="content-block">
                            <table class="data-table">
                                <?php if(isset($raporData['disiplin'])): ?>
                                    <tr>
                                        <td><strong>Disiplin Süreçleri</strong></td>
                                        <td>
                                            <span class="badge bg-new"><?php echo e($raporData['disiplin']['acik']); ?> Açık</span>
                                            <?php if($raporData['disiplin']['savunma'] > 0): ?>
                                                <span class="badge bg-process"><?php echo e($raporData['disiplin']['savunma']); ?>

                                                    Savunma</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if(isset($raporData['arabuluculuk'])): ?>
                                    <tr>
                                        <td><strong>Arabuluculuk</strong></td>
                                        <td>
                                            <span class="badge bg-blue"><?php echo e($raporData['arabuluculuk']['aktif']); ?> Aktif</span>
                                            <?php if($raporData['arabuluculuk']['odeme'] > 0): ?>
                                                <span class="badge bg-process"
                                                    style="background-color: #d97706;"><?php echo e($raporData['arabuluculuk']['odeme']); ?>

                                                    Ödeme</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>

            <tr>
                <td class="footer">
                    <p>© <?php echo e(date('Y')); ?> Köksan Portal Yönetim Sistemi</p>
                    <p>Bu rapor sistem tarafından otomatik oluşturulmuştur.</p>
                </td>
            </tr>
        </table>
    </center>
</body>

</html><?php /**PATH /var/www/kys_koksan/iaa/resources/views/emails/raporlar/otomatik-ozet.blade.php ENDPATH**/ ?>