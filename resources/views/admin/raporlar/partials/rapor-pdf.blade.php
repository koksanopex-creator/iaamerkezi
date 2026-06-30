<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>İAA Raporu</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #333; }
        
        /* HEADER */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .logo { height: 55px; margin-bottom: 5px; }
        .title { font-size: 16px; font-weight: bold; color: #1a202c; margin-bottom: 2px; }
        .date-range { font-size: 11px; font-weight: bold; color: #4a5568; margin-bottom: 5px; }
        .meta { font-size: 9px; color: #718096; }
        .footer-note { font-size: 8px; color: #a0aec0; font-style: italic; margin-top: 3px; }
        
        /* TABLO */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2d3748; color: #fff; padding: 8px; text-align: left; font-size: 8px; border: 1px solid #1a202c; }
        td { padding: 8px; border: 1px solid #e2e8f0; vertical-align: middle; color: #1f2937; }
        
        /* BADGE */
        .badge { display: inline-block; padding: 3px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; text-align: center; white-space: nowrap; }
        
        /* DURUM RENKLERİ */
        .badge-onay-bekliyor { background-color: #fef08a; color: #854d0e; }
        .badge-havuzda       { background-color: #e5e7eb; color: #374151; }
        .badge-atandi        { background-color: #bfdbfe; color: #1e40af; }
        .badge-tamamlandi    { background-color: #bbf7d0; color: #166534; }
        .badge-reddedildi    { background-color: #fecaca; color: #991b1b; }
        .badge-revize        { background-color: #fed7aa; color: #9a3412; }
        .badge-yonetici      { background-color: #a5f3fc; color: #155e75; }

        /* RİSK YAZI RENKLERİ */
        .risk-text-1 { color: #16a34a; font-weight: bold; }
        .risk-text-2 { color: #2563eb; font-weight: bold; }
        .risk-text-3 { color: #d97706; font-weight: bold; }
        .risk-text-4 { color: #ea580c; font-weight: bold; }
        .risk-text-5 { color: #dc2626; font-weight: bold; }
        .risk-none   { color: #9ca3af; font-size: 14px; line-height: 1; }
        
        .center { text-align: center; }
        .text-muted { color: #6b7280; font-size: 8px; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png') }}" class="logo" alt="Logo">
        
        <div class="title">İyileştirmeye Açık Alan (İAA) Raporu</div>
        
        {{-- YENİ EKLENEN KISIM: TARİH ARALIĞI --}}
        <div class="date-range">Rapor Kapsamı: {{ $tarihBilgisi }}</div>
        
        <div class="meta">Oluşturulma Zamanı: {{ now()->format('d.m.Y H:i') }}</div>
        <div class="footer-note">Bu belge sistem tarafından otomatik olarak oluşturulmuştur.</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20px;">#</th>
                <th>Başlık</th>
                <th style="width: 80px;">Bölüm / Alan</th>
                <th style="width: 90px;">Öneren</th>
                <th style="width: 50px;" class="center">Risk</th>
                <th style="width: 80px;" class="center">Durum</th>
                <th style="width: 80px;">Atanan Takım</th>
                <th style="width: 30px;" class="center">Puan</th>
                <th style="width: 50px;" class="center">Tarih</th>
            </tr>
        </thead>
        <tbody>
            @foreach($iaas as $index => $iaa)
                @php
                    $badgeClass = match($iaa->durum) {
                        'Onay Bekliyor' => 'badge-onay-bekliyor',
                        'Havuzda' => 'badge-havuzda',
                        'Atandı' => 'badge-atandi',
                        'Tamamlandı' => 'badge-tamamlandi',
                        'Reddedildi', 'Tamamlanması Reddedildi' => 'badge-reddedildi',
                        'Revize Ediliyor' => 'badge-revize',
                        'Yönetici Onayı Bekliyor' => 'badge-yonetici',
                        default => 'badge-havuzda'
                    };

                    $riskLabel = match($iaa->risk) {
                        1 => 'Düşük', 2 => 'Düşük-Orta', 3 => 'Orta', 4 => 'Yüksek', 5 => 'Kritik', default => null
                    };
                    $riskTextClass = 'risk-text-' . ($iaa->risk ?? 0);
                @endphp

                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $iaa->baslik }}</td>
                    <td>{{ $iaa->bolum->ad ?? ($iaa->ilgili_alan ?? 'Genel') }}</td>
                    <td>
                        {{ $iaa->gonderen->name ?? $iaa->guest_name }}
                        <br><span class="text-muted">({{ $iaa->gonderen ? 'Personel' : 'Misafir' }})</span>
                    </td>
                    <td class="center">
                        @if($riskLabel) <span class="{{ $riskTextClass }}">{{ $riskLabel }}</span> @else <span class="risk-none">-</span> @endif
                    </td>
                    <td class="center">
                        <span class="badge {{ $badgeClass }}">{{ $iaa->durum }}</span>
                    </td>
                    <td>{{ $iaa->atananTakim->ad ?? '-' }}</td>
                    <td class="center" style="font-weight: bold; font-size: 10px;">
                        {{ $iaa->puan ? number_format($iaa->puan, 0) : '-' }}
                    </td>
                    <td class="center">{{ $iaa->created_at->format('d.m.Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>