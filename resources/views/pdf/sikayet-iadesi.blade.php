<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Müşteri Şikayeti Kaynaklı İade Raporu</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a202c; margin: 0; padding: 20px; }
        
        /* HEADER */
        .header { margin-bottom: 30px; border-bottom: 2px solid #1a202c; padding-bottom: 15px; position: relative; }
        .logo-container { position: absolute; left: 0; top: 0; width: 120px; height: 60px; }
        .logo { max-height: 60px; max-width: 120px; }
        .header-text { text-align: center; margin-left: 130px; }
        .title { font-size: 18px; font-weight: bold; color: #1a202c; margin-bottom: 5px; text-transform: uppercase; }
        .date { font-size: 12px; font-style: italic; color: #4a5568; }
        
        /* INFO */
        .meta-info { font-size: 8px; color: #718096; margin-top: 10px; text-align: right; }
        
        /* TABLO */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed; }
        th { background-color: #1a1f2e; color: #ffffff; padding: 10px 5px; text-align: left; font-size: 9px; border: 1px solid #1a202c; font-weight: bold; }
        td { padding: 8px 5px; border: 1px solid #e2e8f0; vertical-align: middle; font-size: 8px; word-wrap: break-word; }
        
        .center { text-align: center; }
        .right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        footer { position: fixed; bottom: -20px; left: 0; right: 0; height: 30px; text-align: center; font-size: 8px; color: #a0aec0; }
    </style>
</head>
<body>

    @php
        $siteLogo = \App\Models\Setting::get('site_logo');
        $logoPath = null;
        if ($siteLogo && file_exists(storage_path('app/public/' . $siteLogo))) {
            $logoPath = storage_path('app/public/' . $siteLogo);
        } else {
            $logoPath = public_path('logo.svg');
            if (!file_exists($logoPath)) {
                $logoPath = public_path('favicon.png');
            }
        }
    @endphp

    <div class="header">
        <div class="logo-container">
            @if(file_exists($logoPath))
                <img src="{{ $logoPath }}" class="logo">
            @endif
        </div>
        <div class="header-text">
            <div class="title">Müşteri Şikayeti Kaynaklı İade Raporu</div>
            <div class="date">Rapor Dönemi: {{ $tarihBilgisi }}</div>
        </div>
        <div class="meta-info">Rapor Oluşturma: {{ now()->format('d.m.Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">İade Tarihi</th>
                <th style="width: 18%;">Müşteri Adı</th>
                <th style="width: 10%;">Şikayet No</th>
                <th style="width: 12%;">Bölüm</th>
                <th style="width: 8%;">Miktar</th>
                <th style="width: 8%;">Birim</th>
                <th style="width: 15%;">İade Sebebi</th>
                <th style="width: 17%;">Açıklama</th>
            </tr>
        </thead>
        <tbody>
            @foreach($iadeler as $iade)
                <tr>
                    <td class="center">{{ $iade->iade_tarihi->format('d.m.Y') }}</td>
                    <td class="font-bold">{{ $iade->musteriSikayeti->musteri_adi ?? '-' }}</td>
                    <td class="center">#{{ $iade->musteri_id }}</td>
                    <td>{{ $iade->musteriSikayeti->sikayetKategori->bolum->ad ?? '-' }}</td>
                    <td class="right font-bold">{{ number_format($iade->miktar, 2, ',', '.') }}</td>
                    <td class="center">{{ $iade->birim }}</td>
                    <td>{{ $iade->iade_sebebi }}</td>
                    <td>{{ $iade->aciklama }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <footer>
        Bu rapor Köksan CRM sistemi tarafından otomatik olarak üretilmiştir. Sayfa: {PAGENO}/{NB}
    </footer>

</body>
</html>
