<table>
    <thead>
    {{-- BAŞLIK --}}
    <tr>
        <th colspan="6" style="font-weight: bold; font-size: 16px; text-align: center; background-color: #4F46E5; color: #FFFFFF;">
            BÖLÜM ANALİZ RAPORU
        </th>
    </tr>
    <tr>
        <th colspan="6" style="text-align: center; color: #666666;">
            Rapor Tarihi: {{ now()->format('d.m.Y H:i') }}
        </th>
    </tr>
    <tr></tr> {{-- Boş Satır --}}

    {{-- GENEL ÖZET TABLOSU --}}
    <tr>
        <th colspan="2" style="font-weight: bold; background-color: #F3F4F6;">Kazanılan Brüt Başarı</th>
        <th colspan="2" style="font-weight: bold; background-color: #F3F4F6;">Disiplin Kesintileri</th>
        <th colspan="2" style="font-weight: bold; background-color: #F3F4F6;">Net Sonuç</th>
    </tr>
    <tr>
        <td colspan="2" style="color: #059669; font-weight: bold;">+{{ number_format($grossTotal, 0) }}</td>
        <td colspan="2" style="color: #DC2626; font-weight: bold;">-{{ number_format($penaltyTotal, 0) }}</td>
        <td colspan="2" style="color: #4F46E5; font-weight: bold;">{{ number_format($netTotal, 0) }}</td>
    </tr>
    
    <tr></tr> {{-- Boş Satır --}}

    {{-- KATEGORİ BAZLI DÖKÜM --}}
    <tr>
        <th colspan="3" style="font-weight: bold; background-color: #4F46E5; color: #FFFFFF;">Kategori</th>
        <th colspan="2" style="font-weight: bold; background-color: #4F46E5; color: #FFFFFF; text-align: center;">Puan Değeri</th>
        <th style="font-weight: bold; background-color: #4F46E5; color: #FFFFFF; text-align: center;">Katkı Oranı</th>
    </tr>
    @foreach($breakdown as $key => $cat)
        @if($key !== 'total')
        <tr>
            <td colspan="3">{{ $cat['label'] }}</td>
            <td colspan="2" style="text-align: center; font-weight: bold; color: {{ $cat['score'] >= 0 ? '#059669' : '#DC2626' }}">
                {{ $cat['score'] > 0 ? '+' : '' }}{{ number_format($cat['score'], 0) }}
            </td>
            <td style="text-align: center; color: #666666;">%{{ $cat['percentage'] }}</td>
        </tr>
        @endif
    @endforeach

    <tr></tr> {{-- Boş Satır --}}

    {{-- ANA SIRALAMA TABLOSU --}}
    <tr>
        <th style="font-weight: bold; background-color: #1E1B4B; color: #FFFFFF;">Sıra</th>
        <th style="font-weight: bold; background-color: #1E1B4B; color: #FFFFFF;">Bölüm Adı</th>
        <th style="font-weight: bold; background-color: #1E1B4B; color: #FFFFFF;">Bölüm Lideri</th>
        <th style="font-weight: bold; background-color: #1E1B4B; color: #FFFFFF;">Bölüm Birincisi (MVP)</th>
        <th style="font-weight: bold; background-color: #1E1B4B; color: #FFFFFF; text-align: center;">MVP Puanı</th>
        <th style="font-weight: bold; background-color: #1E1B4B; color: #FFFFFF; text-align: center;">Toplam Puan</th>
    </tr>
    </thead>
    <tbody>
    @foreach($bolumPuanListesi as $index => $item)
        <tr>
            <td>#{{ $index + 1 }}</td>
            <td style="font-weight: bold;">{{ $item->ad }}</td>
            <td>{{ $item->lider->name ?? 'Atanmamış' }}</td>
            <td>{{ $item->birinci->name ?? 'Yok' }}</td>
            <td style="text-align: center;">{{ $item->birinci ? number_format($item->birinci->period_puan, 0) : '0' }}</td>
            <td style="font-weight: bold; text-align: center; color: #4F46E5;">{{ number_format($item->total_score, 0) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
