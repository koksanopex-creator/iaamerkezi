<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>İAA Raporu</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; }
        .page-break { page-break-after: always; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 18px; }
        .header p { font-size: 10px; color: #666; }
        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        thead th { background-color: #f2f2f2; font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 8px;
            font-weight: bold;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
        }
        /* Renkli Durum Stilleri */
        .badge.onay-bekliyor { background-color: #FBBF24; color: #78350F; } /* Amber-400 */
        .badge.havuzda { background-color: #D1D5DB; color: #374151; }       /* Gray-300 */
        .badge.atandi { background-color: #34D399; color: #064E3B; }         /* Emerald-400 */
        .badge.reddedildi { background-color: #F87171; color: #7F1D1D; }      /* Red-400 */
    </style>
</head>
<body>
    <div class="header">
        <h1>İyileştirme Önerileri Raporu</h1>
        <p>Rapor Tarihi: {{ now()->format('d.m.Y H:i') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 35%;">Başlık</th>
                <th style="width: 15%;">Durum</th>
                <th style="width: 20%;">Öneren</th>
                <th style="width: 15%;">Tarih</th>
                <th style="width: 10%;">Puan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($iaas as $index => $iaa)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $iaa->baslik }}</td>
                    <td>
                        @php
                            $durumClass = strtolower(str_replace(' ', '-', $iaa->durum));
                        @endphp
                        <span class="badge {{ $durumClass }}">{{ $iaa->durum }}</span>
                    </td>
                    <td>{{ $iaa->gonderen->name ?? $iaa->guest_name }}</td>
                    <td>{{ $iaa->created_at->format('d.m.Y') }}</td>
                    <td>{{ $iaa->puan ? number_format($iaa->puan, 2) : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Filtrelenen kriterlere uygun veri bulunamadı.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>