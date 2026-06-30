<table>
    <thead>
        {{-- Satır 1: Logo Alanı ve Başlık --}}
        <tr>
            <td></td> {{-- A1 (Merged with A1:B2) --}}
            <td></td> {{-- B1 --}}
            <td colspan="6" style="text-align: center; font-size: 18px; font-weight: bold; vertical-align: center;">
                MÜŞTERİ ŞİKAYETİ KAYNAKLI İADE RAPORU
            </td>
        </tr>
        
        {{-- Satır 2: Logo Alanı ve Tarih --}}
        <tr>
            <td></td> {{-- A2 (Merged with A1:B2) --}}
            <td></td> {{-- B2 --}}
            <td colspan="6" style="text-align: center; font-style: italic; font-size: 10px; vertical-align: center;">
                Rapor Dönemi: {{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}
            </td>
        </tr>
        
        {{-- Satır 3: Tablo Başlıkları (Kullanıcı Talebi: Boş satırlar silindi) --}}
        <tr style="background-color: #1a1f2e; color: #ffffff;">
            <th style="font-weight: bold; border: 1px solid #000000;">İade Tarihi</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Müşteri Adı</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Şikayet No</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Bölüm</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Miktar</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Birim</th>
            <th style="font-weight: bold; border: 1px solid #000000;">İade Sebebi</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Açıklama</th>
        </tr>
    </thead>
    <tbody>
        @foreach($iadeler as $iade)
            <tr>
                <td style="border: 1px solid #000000; text-align: left;">{{ $iade->iade_tarihi->format('d.m.Y') }}</td>
                <td style="border: 1px solid #000000;">{{ $iade->musteriSikayeti->musteri_adi ?? '-' }}</td>
                <td style="border: 1px solid #000000; text-align: center;">#{{ $iade->musteri_sikayeti_id }}</td>
                <td style="border: 1px solid #000000;">{{ $iade->musteriSikayeti->sikayetKategori->bolum->ad ?? '-' }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ number_format($iade->miktar, 2, ',', '.') }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $iade->birim }}</td>
                <td style="border: 1px solid #000000;">{{ $iade->iade_sebebi }}</td>
                <td style="border: 1px solid #000000;">{{ $iade->aciklama }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
