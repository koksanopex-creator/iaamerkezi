<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Müşteri Şikayetleri Raporu</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; }
        .header { position: relative; margin-bottom: 30px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; min-height: 60px; }
        .logo { position: absolute; left: 0; top: 0; height: 50px; }
        .header-content { text-align: center; }
        .header h1 { color: #4f46e5; margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; color: #666; font-style: italic; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        th { background-color: #4f46e5; color: white; padding: 8px; text-align: left; font-weight: bold; text-transform: uppercase; border: 1px solid #4f46e5; }
        td { padding: 8px; border: 1px solid #e2e8f0; vertical-align: top; word-wrap: break-word; }
        
        /* Durum Renkleri */
        .status-yeni { background-color: #fef3c7 !important; }
        .status-islemde { background-color: #dbeafe !important; }
        .status-kapatildi { background-color: #dcfce7 !important; }
        .status-talep { background-color: #f3e8ff !important; }
        .status-hatali { background-color: #ffedd5 !important; }

        .footer { text-align: right; font-size: 8px; color: #999; position: fixed; bottom: 0; width: 100%; }
    </style>
</head>
<body>
    @php
        $logo = \App\Models\Setting::where('key', 'site_logo')->first();
        $logoPath = ($logo && $logo->value) ? storage_path('app/public/' . $logo->value) : null;
    @endphp

    <div class="header">
        @if($logoPath && file_exists($logoPath))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo">
        @endif
        <div class="header-content">
            <h1>Müşteri Şikayetleri Raporu</h1>
            <p>Rapor Tarihi: {{ $tarih }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%">ID</th>
                <th style="width: 10%">Tarih</th>
                <th style="width: 15%">Müşteri</th>
                <th style="width: 15%">Kategori</th>
                <th style="width: 26%">Konu</th>
                <th style="width: 15%">Durum</th>
                <th style="width: 15%">Hedef / Kapanış</th>
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
                    elseif (str_contains(strtolower($durum), 'talep')) $rowClass = 'status-talep';
                    elseif (str_contains(strtolower($durum), 'hatali')) $rowClass = 'status-hatali';
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ $s->id }}</td>
                    <td>{{ $s->created_at->format('d.m.Y') }}</td>
                    <td>{{ $s->customer ? $s->customer->name : $s->musteri_adi }}</td>
                    <td>{{ $s->sikayetKategori ? $s->sikayetKategori->ad : 'Genel' }}</td>
                    <td><strong>{{ $s->musteri_sikayet_konusu }}</strong></td>
                    <td>{{ $s->musteri_durum }}</td>
                    <td>
                        {{ $s->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($s->musteri_cozum_son_tarihi)->format('d.m.Y') : '-' }}
                        @if($s->kurul_onay_tarihi ?? $s->musteri_onay_tarihi)
                            <br><small style="color: #166534">K: {{ ($s->kurul_onay_tarihi ?? $s->musteri_onay_tarihi)->format('d.m.Y') }}</small>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Bu belge sistem üzerinden otomatik olarak oluşturulmuştur.
    </div>
</body>
</html>
