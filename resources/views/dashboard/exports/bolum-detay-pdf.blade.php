<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $bolum->ad }} Analiz Raporu</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
        .page-container { padding: 30px; }
        
        /* Header & Logo */
        .header { border-bottom: 2px solid #4F46E5; padding-bottom: 15px; margin-bottom: 20px; position: relative; }
        .logo { position: absolute; left: 0; top: 0; height: 40px; }
        .report-title { text-align: right; }
        .report-title h2 { color: #4F46E5; margin: 0; font-size: 18px; text-transform: uppercase; }
        .report-title p { color: #666; margin: 5px 0 0 0; font-size: 9px; }

        /* Summary Grid */
        .summary-container { margin-bottom: 25px; }
        .summary-table { width: 100%; border-collapse: collapse; background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; }
        .summary-table th { background: #F3F4F6; color: #374151; padding: 10px; text-align: left; border-bottom: 1px solid #E5E7EB; font-size: 9px; }
        .summary-table td { padding: 12px 10px; border-bottom: 1px solid #E5E7EB; font-size: 12px; font-weight: bold; }
        .text-success { color: #059669; }
        .text-danger { color: #DC2626; }
        .text-primary { color: #4F46E5; }

        /* Breakdown Cards (Modernized) */
        .section-title { font-size: 11px; font-weight: bold; color: #1F2937; margin: 20px 0 10px 0; border-left: 4px solid #4F46E5; padding-left: 8px; }
        .grid-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .grid-table th { background-color: #4F46E5; color: white; padding: 8px; text-align: left; font-size: 9px; }
        .grid-table td { border: 1px solid #E5E7EB; padding: 8px; font-size: 9px; vertical-align: middle; }
        .grid-table tr:nth-child(even) { background-color: #F9FAFB; }

        /* Scoring Table */
        .ranking-table { width: 100%; border-collapse: collapse; }
        .ranking-table th { background-color: #1E1B4B; color: white; padding: 10px; text-align: left; font-size: 9px; }
        .ranking-table td { border-bottom: 1px solid #EEE; padding: 8px; font-size: 9px; }
        .rank { font-weight: bold; color: #666; }
        .score { font-weight: bold; color: #4F46E5; text-align: right; font-size: 10px; }

        /* Footer */
        .footer { position: fixed; bottom: 20px; left: 30px; right: 30px; border-top: 1px solid #EEE; padding-top: 10px; text-align: center; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="header">
            @php
                $logoPath = public_path('logo.svg');
            @endphp
            <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo transition-transform" alt="Logo">
            
            <div class="report-title">
                <h2>BÖLÜM PERSONEL ANALİZİ</h2>
                <p><strong>Bölüm:</strong> {{ $bolum->ad }}</p>
                <p>{{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d.m.Y') : 'Tüm Zamanlar' }} - {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d.m.Y') : now()->format('d.m.Y') }}</p>
            </div>
        </div>

        {{-- 1. ÖZET TABLOSU --}}
        <div class="summary-container">
            <div class="section-title">BÖLÜM PERFORMANS ÖZETİ</div>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th width="33.3%">Kazanılan Brüt Başarı</th>
                        <th width="33.3%">Disiplin Kesintileri</th>
                        <th width="33.3%">Net Sonuç</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-success">+{{ number_format($grossTotal, 0) }}</td>
                        <td class="text-danger">-{{ number_format($penaltyTotal, 0) }}</td>
                        <td class="text-primary">{{ number_format($netTotal, 0) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- 2. KATEGORİ BAZLI DÖKÜM --}}
        <div class="section-title">KATEGORİ BAZLI PUAN DAĞILIMI</div>
        <table class="grid-table">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th style="text-align: center;">Puan Değeri</th>
                    <th style="text-align: center;">Bölüm İçi Katkı Oranı</th>
                </tr>
            </thead>
            <tbody>
                @foreach($breakdown as $cat)
                    <tr>
                        <td><strong>{{ $cat['label'] }}</strong></td>
                        <td style="text-align: center; font-weight: bold;" class="{{ $cat['score'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $cat['score'] > 0 ? '+' : '' }}{{ number_format($cat['score'], 0) }}
                        </td>
                        <td style="text-align: center; color: #666;">
                            %{{ $cat['percentage'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- 3. PERSONEL SIRALAMASI --}}
        <div class="section-title">PERSONEL BAŞARI SIRALAMASI</div>
        <table class="ranking-table">
            <thead>
                <tr>
                    <th width="5%">SIRA</th>
                    <th width="50%">PERSONEL ADI SOYADI</th>
                    <th width="25%" style="text-align: right;">DÖNEM PUANI</th>
                    <th width="20%" style="text-align: right;">BÖLÜM PAYI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                    <tr>
                        <td class="rank">#{{ $index + 1 }}</td>
                        <td style="font-weight: bold; color: #1F2937;">{{ $user->name }}</td>
                        <td class="score">{{ number_format($user->period_puan, 0) }}</td>
                        <td style="text-align: right; color: #666;">%{{ round(($user->period_puan / max(1, $totalBolumPuan)) * 100, 1) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            Bu rapor otomatik olarak oluşturulmuştur. &copy; {{ date('Y') }} <strong>Köksan Portal</strong> - Performans Yönetim Sistemi
        </div>
    </div>
</body>
</html>
