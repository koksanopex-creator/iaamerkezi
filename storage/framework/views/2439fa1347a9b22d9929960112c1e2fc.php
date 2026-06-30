<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disiplin Karar Tutanağı - #<?php echo e($case->id); ?></title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url(http://liberationserif.com/fonts/ttf/DejaVuSans.ttf) format('truetype');
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #1a1a1a;
            font-size: 13px;
            line-height: 1.5;
            background-color: white;
        }
        @page {
            size: A4;
            margin: 0;
        }
        
        /* HEADER - Ref-Image-4 Standartlarına Hassas Ayar */
        .page-header {
            width: 100%;
            height: 90px;
            position: relative;
            margin-bottom: 20px;
            padding: 0;
            border-bottom: 3px solid #eee; /* İnce bir ayrım çizgisi */
        }
        .blue-stripe {
            position: absolute;
            left: 20px;
            top: 20px;
            width: 45px;
            height: 45px;
            background-color: #005aab;
            z-index: 5;
        }
        .logo-container {
            position: absolute;
            left: 85px;
            top: 20px; /* Hizalama için ayarlandı */
            z-index: 10;
        }
        .logo-container img {
            max-height: 45px; /* Hizalama için ayarlandı */
            width: auto;
        }
        .red-stripe {
            position: absolute;
            right: 0;
            top: 20px; /* Logo ve mavi kutu ile aynı hizaya getirildi */
            width: 45%;
            height: 45px;
            background-color: #ed1c24;
            z-index: 5;
        }

        .header-date {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 14px;
            font-weight: bold;
            color: #1a1a1a;
            z-index: 10;
        }

        .content {
            padding: 20px 60px 40px 60px;
        }
        
        .main-title {
            text-align: center;
            font-weight: bold;
            font-size: 22px;
            text-transform: uppercase;
            margin: 30px 0;
            text-decoration: none;
            letter-spacing: 1px;
        }
        
        .section-header {
            font-weight: bold;
            font-size: 15px;
            text-decoration: underline;
            margin-bottom: 15px;
            margin-top: 25px;
            display: block;
        }
        
        .info-row {
            margin-bottom: 6px;
            width: 100%;
        }
        .info-label {
            display: inline-block;
            width: 150px;
            font-weight: bold;
            color: #444;
        }
        .info-colon {
            display: inline-block;
            width: 15px;
        }
        .info-value {
            display: inline-block;
        }

        .decision-body {
            margin: 40px 0;
            text-align: justify;
            min-height: 250px;
            line-height: 1.8;
        }
        
        .date-container {
            text-align: right;
            font-weight: bold;
            margin: 30px 0;
            font-size: 14px;
        }

        .signatures-area {
            width: 100%;
            margin-top: 60px;
            page-break-inside: avoid;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .sig-table th {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            padding-bottom: 60px;
            font-size: 12px;
        }
        .sig-table td {
            text-align: center;
            vertical-align: top;
            font-size: 13px;
        }

        /* FOOTER */
        .page-footer {
            position: fixed;
            bottom: 15px;
            left: 0;
            right: 0;
            padding: 0 60px;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-left { text-align: left; }
        .footer-right { text-align: right; line-height: 1.4; }

        .no-print {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 100;
        }
        .print-btn {
            background-color: #4f46e5;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        
        .content-section {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <?php if(!isset($isPdf) || !$isPdf): ?>
        <div class="no-print">
            <button onclick="window.print()" class="print-btn">Belgeyi Yazdır</button>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <div class="blue-stripe"></div>
        <div class="logo-container">
            <?php if(isset($logoBase64) && $logoBase64): ?>
                <img src="<?php echo e($logoBase64); ?>" alt="Logo">
            <?php elseif($logo): ?>
                <img src="<?php echo e(asset('storage/' . $logo)); ?>" alt="Logo">
            <?php else: ?>
                <span style="font-weight:900; color:#005aab; font-size:24px; line-height:75px;">KÖKSAN</span>
            <?php endif; ?>
        </div>
        <div class="red-stripe"></div>

        <?php if($case->durum == 'Karar Verildi' && $case->karar_tarihi): ?>
            <div class="header-date">
                Tarih: <?php echo e($case->karar_tarihi->format('d.m.Y')); ?>

            </div>
        <?php endif; ?>
    </div>

    <div class="content">
        <?php if($case->durum === 'Savunma Bekleniyor'): ?>
            <div class="main-title">DİSİPLİN SORUŞTURMA VE SAVUNMA TALEP FORMU</div>
            <div style="text-align: center; font-weight: bold; font-size: 16px; margin-top: -20px; margin-bottom: 30px; color: #666;">(Sistem Önerisi: <?php echo e($cezaAdi); ?>)</div>
        <?php else: ?>
            <div class="main-title">DİSİPLİN KURULU KARAR TUTANAĞI</div>
            <div style="text-align: center; font-weight: bold; font-size: 16px; margin-top: -20px; margin-bottom: 30px; color: #666;">(<?php echo e($cezaAdi); ?>)</div>
        <?php endif; ?>

        <div class="section-header">İhtar Eden İşveren</div>
        <div class="info-row">
            <span class="info-label">İşveren Unvanı</span>
            <span class="info-colon">:</span>
            <span class="info-value" style="font-weight:bold;">KÖKSAN Pet ve Plastik Ambalaj San. ve Tic. A.Ş</span>
        </div>
        <div class="info-row">
            <span class="info-label">Adresi</span>
            <span class="info-colon">:</span>
            <span class="info-value">4. Organize Sanayi Bölgesi 83422 Nolu Cad. No:10 Şehitkamil/GAZİANTEP</span>
        </div>

        <div class="section-header">İhtar Edilen Personel</div>
        <div class="info-row">
            <span class="info-label">İşçi Adı Soyadı</span>
            <span class="info-colon">:</span>
            <span class="info-value" style="font-weight:bold; text-decoration: underline;"><?php echo e($case->user->name); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">T.C. Kimlik No</span>
            <span class="info-colon">:</span>
            <span class="info-value"><?php echo e($case->user->tc_kimlik_no ?? ''); ?></span>
        </div>
        <?php if($case->user->bolum): ?>
        <div class="info-row">
            <span class="info-label">Bölümü / Görevi</span>
            <span class="info-colon">:</span>
            <span class="info-value"><?php echo e($case->user->bolum->ad ?? '-'); ?> / <?php echo e($case->user->unvan ?? '-'); ?></span>
        </div>
        <?php endif; ?>
        


        <div class="info-row">
            <span class="info-label">Olay Tarihi</span>
            <span class="info-colon">:</span>
            <span class="info-value"><?php echo e($case->olay_tarihi->format('d.m.Y')); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Tutanak Tarihi</span>
            <span class="info-colon">:</span>
            <span class="info-value"><?php echo e($case->created_at->format('d.m.Y H:i')); ?></span>
        </div>

        <div>
            <div class="section-header">Olay Açıklaması / Tutanak Metni</div>
            <div style="margin-bottom: 30px; text-align: justify; line-height: 1.6;">
                <?php if(isset($kararMetni) && !empty($kararMetni)): ?>
                    <?php echo $kararMetni; ?>

                <?php else: ?>
                    <?php echo nl2br(e(trim($case->olay_aciklamasi))); ?>

                <?php endif; ?>
            </div>
        </div>



        <div style="font-style: italic; margin-bottom: 30px;">Bilgilerinizi rica ederim.</div>

        <?php if(isset($canSeeSensitiveInfo) && $canSeeSensitiveInfo): ?>
            <div class="signatures-area">
                <table class="sig-table">
                    <thead>
                        <tr>
                            <?php $__currentLoopData = $councilMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th><?php echo e($member->hasRole('Disiplin Kurulu Başkanı') ? 'BAŞKAN' : 'ÜYE'); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php $__currentLoopData = $councilMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td>
                                    <div style="font-weight:bold;"><?php echo e($member->name); ?></div>
                                    <div style="font-size:11px; color:#999; margin-top:10px; font-style:italic;">İmza</div>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="signatures-area">
                <table class="sig-table">
                    <tr>
                        <td style="text-align: left;">
                            <div style="font-weight:bold; text-decoration: underline;">TEBELLÜĞ EDEN (PERSONEL)</div>
                            <div style="margin-top: 10px;">Adı Soyadı: .................................................</div>
                            <div style="margin-top: 5px;">Tarih: ...... / ...... / 20......</div>
                            <div style="margin-top: 10px; font-style: italic; color: #999;">İmza</div>
                        </td>
                        <td style="text-align: left; padding-left: 50px;">
                            <div style="font-weight:bold; text-decoration: underline;">TEBLİĞ EDEN (İŞVEREN VEKİLİ)</div>
                            <div style="margin-top: 10px;">Adı Soyadı: .................................................</div>
                            <div style="margin-top: 5px;">Tarih: ...... / ...... / 20......</div>
                            <div style="margin-top: 10px; font-style: italic; color: #999;">İmza</div>
                        </td>
                    </tr>
                </table>
            </div>
        <?php endif; ?>

        
        <div style="height: 50px;"></div>
    </div>


    </div>

    <div class="page-footer">
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    4. Org. San. Böl. 83422. Cd. No:10 P.K. 39 Şehitkamil/GAZİANTEP | (0.342) 357 03 30 | www.koksan.com
                </td>
                <td class="footer-right">
                    Ticaret Sicil No: 14787<br>Mersis No: 0585 0003 3200 0017
                </td>
            </tr>
        </table>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $x = 520;
            $y = 820;
            $text = "{PAGE_NUM} / {PAGE_COUNT}";
            $font = $fontMetrics->get_font("DejaVu Sans", "bold");
            $size = 9;
            $color = array(0, 0, 0);
            $word_space = 0.0;
            $char_space = 0.0;
            $angle = 0.0;
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>
    <?php if(!isset($isPdf) || !$isPdf): ?>
        <script>
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            };
        </script>
    <?php endif; ?>
</body>
</html>
<?php /**PATH /var/www/kys_koksan/iaa/resources/views/admin/disiplin/print.blade.php ENDPATH**/ ?>