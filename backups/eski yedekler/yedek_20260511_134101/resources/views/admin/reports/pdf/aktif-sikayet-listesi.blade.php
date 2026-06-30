<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Aktif Şikayet Süreç Takip Raporu</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8px; color: #333; margin: 0; padding: 0; }
        .header { position: relative; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding: 10px 0; min-height: 60px; }
        .logo { position: absolute; left: 0; top: 10px; height: 45px; }
        .header-content { text-align: center; margin-left: 60px; }
        .header h1 { color: #4f46e5; margin: 0; font-size: 14px; text-transform: uppercase; font-weight: bold; }
        .header .meta { margin-top: 5px; color: #666; font-size: 7px; }
        
        .stats-bar { background-color: #f8fafc; padding: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #e2e8f0; }
        .stats-item { display: inline-block; margin-right: 15px; font-weight: bold; color: #475569; }
        .stats-item span { color: #4f46e5; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        th { background-color: #4f46e5; color: white; padding: 5px; text-align: left; font-weight: bold; text-transform: uppercase; border: 1px solid #4f46e5; font-size: 7px; }
        td { padding: 5px; border: 1px solid #e2e8f0; vertical-align: top; word-wrap: break-word; }
        
        .status-yeni { background-color: #fef3c7 !important; }
        .status-islemde { background-color: #dbeafe !important; }
        .status-kapatildi { background-color: #dcfce7 !important; }
        
        .bekleme-kritik { color: #dc2626; font-weight: bold; }
        .link { color: #2563eb; text-decoration: underline; }
        
        .footer { text-align: right; font-size: 6px; color: #999; position: fixed; bottom: 10px; width: 100%; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    @php
        $logo = \App\Models\Setting::where('key', 'site_logo')->first();
        $logoPath = ($logo && $logo->value) ? storage_path('app/public/' . $logo->value) : null;
        
        $stats = [
            'Tümü' => $sikayetler->count(),
            'Yeni' => $sikayetler->where('musteri_durum', 'Yeni')->count(),
            'İşlemde' => $sikayetler->whereIn('musteri_durum', ['İşlemde', 'Atandı', 'Devam Ediyor'])->count(),
            'Onay Bekleyen' => $sikayetler->filter(fn($s) => str_contains(strtolower($s->iaaProjesi?->durum ?? ''), 'onay'))->count(),
            'Çözülenler' => $sikayetler->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])->count(),
        ];

        $mapping = [
            'hatali_bildirim_onayi_bekliyor_direktor' => 'Hatalı Bildirim (Direktör Onayı)',
            'talep_onayi_bekliyor_direktor' => 'Talep Onayı (Direktör)',
            'bolum_onayi_bekliyor' => 'Bölüm Onayı Bekliyor',
            'onay_bekliyor' => 'Onay Bekliyor'
        ];
    @endphp

    <div class="header">
        @if($logoPath && file_exists($logoPath))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo">
        @endif
        <div class="header-content">
            <h1>Aktif Şikayet Süreç Takip Raporu</h1>
            <div class="meta">
                Rapor Tarihi: {{ $tarih }} | 
                Filtre: {{ request('durum', 'Tümü') }}
            </div>
        </div>
    </div>

    <div class="stats-bar">
        @foreach($stats as $label => $val)
            <div class="stats-item">{{ $label }}: <span>{{ $val }}</span></div>
        @endforeach
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%">ID</th>
                <th style="width: 8%">Tarih</th>
                <th style="width: 14%">Bekleme/Süreç Durumu</th>
                <th style="width: 13%">Müşteri</th>
                <th style="width: 8%">Kategori</th>
                <th style="width: 20%">Konu</th>
                <th style="width: 10%">Şikayet Durum</th>
                <th style="width: 15%">Proje Durumu</th>
                <th style="width: 8%">Hedef</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sikayetler as $s)
                @php
                    $rowClass = '';
                    $durum = $s->musteri_durum;
                    if ($durum == 'Yeni') $rowClass = 'status-yeni';
                    elseif (in_array($durum, ['İşlemde', 'Atandı', 'Devam Ediyor'])) $rowClass = 'status-islemde';
                    elseif (in_array($durum, ['Çözümlendi', 'Kapatıldı'])) $rowClass = 'status-kapatildi';

                    $createdAt = \Carbon\Carbon::parse($s->created_at);
                    $now = now();
                    
                    if (in_array($durum, ['Çözümlendi', 'Kapatıldı'])) {
                        $finishDate = $s->updated_at ? \Carbon\Carbon::parse($s->updated_at) : $now;
                        $diff = ceil(abs($createdAt->diffInDays($finishDate, false)));
                        $beklemeMetni = $diff . " Günde Tamamlandı";
                    } elseif ($durum == 'Yeni') {
                        $diff = ceil(abs($createdAt->diffInDays($now, false)));
                        $beklemeMetni = $diff . " Gündür Atama Bekliyor";
                    } else {
                        $diff = ceil(abs($createdAt->diffInDays($now, false)));
                        $beklemeMetni = $diff . " Gündür İşlemde";
                    }
                    
                    $projeDurum = $s->iaaProjesi?->durum ?? '-';
                    $projeDurumLabel = $mapping[strtolower($projeDurum)] ?? $projeDurum;
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ $s->id }}</td>
                    <td>{{ $s->created_at->format('d.m.Y') }}</td>
                    <td class="{{ (str_contains($beklemeMetni, 'Bekliyor') || str_contains($beklemeMetni, 'İşlemde')) && intval($beklemeMetni) > 3 ? 'bekleme-kritik' : '' }}">
                        {{ $beklemeMetni }}
                    </td>
                    <td>
                        @if($s->customer_id)
                            <a href="{{ url('/musteri-profil/' . $s->customer_id) }}" class="link">
                                {{ $s->customer ? $s->customer->name : $s->musteri_adi }}
                            </a>
                        @else
                            {{ $s->musteri_adi }}
                        @endif
                    </td>
                    <td>{{ $s->sikayetKategori ? $s->sikayetKategori->ad : 'Genel' }}</td>
                    <td>
                        <a href="{{ $s->iaa_id ? url('/proje-calisma-alani/' . $s->iaa_id) : url('/sikayetler/' . $s->id) }}" class="link">
                            {{ $s->musteri_sikayet_konusu }}
                        </a>
                    </td>
                    <td>{{ $s->musteri_durum }}</td>
                    <td style="font-size: 6px;">{{ $projeDurumLabel }}</td>
                    <td>{{ $s->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($s->musteri_cozum_son_tarihi)->format('d.m.Y') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Köksan İAA Sistemi - Operasyonel Süreç Raporu | Sayfa: {PAGENO}
    </div>
</body>
</html>
