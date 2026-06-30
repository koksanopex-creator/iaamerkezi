<table>
    <thead>
    {{-- BAŞLIK --}}
    <tr>
        <th colspan="5" style="font-weight: bold; font-size: 16px; text-align: center; background-color: #4F46E5; color: #FFFFFF;">
            BÖLÜM PERSONEL ANALİZ RAPORU
        </th>
    </tr>
    <tr>
        <th colspan="5" style="text-align: center; background-color: #F3F4F6;">
            Bölüm: {{ $bolum->ad }} | Rapor Tarihi: {{ now()->format('d.m.Y H:i') }}
        </th>
    </tr>
    <tr></tr> {{-- Boş Satır --}}

    {{-- GENEL ÖZET TABLOSU --}}
    <tr>
        <th colspan="2" style="font-weight: bold; background-color: #F3F4F6;">Kazanılan Brüt Başarı</th>
        <th colspan="2" style="font-weight: bold; background-color: #F3F4F6;">Disiplin Kesintileri</th>
        <th style="font-weight: bold; background-color: #F3F4F6;">Net Sonuç</th>
    </tr>
    <tr>
        <td colspan="2" style="color: #059669; font-weight: bold;">+{{ number_format($grossTotal, 0) }}</td>
        <td colspan="2" style="color: #DC2626; font-weight: bold;">-{{ number_format($penaltyTotal, 0) }}</td>
        <td style="color: #4F46E5; font-weight: bold;">{{ number_format($netTotal, 0) }}</td>
    </tr>
    
    <tr></tr> {{-- Boş Satır --}}

    {{-- KATEGORİ BAZLI DÖKÜM --}}
    <tr>
        <th colspan="3" style="font-weight: bold; background-color: #4F46E5; color: #FFFFFF;">Kategori</th>
        <th style="font-weight: bold; background-color: #4F46E5; color: #FFFFFF; text-align: center;">Puan Değeri</th>
        <th style="font-weight: bold; background-color: #4F46E5; color: #FFFFFF; text-align: center;">Bölüm Payı</th>
    </tr>
    @foreach($breakdown as $cat)
        <tr>
            <td colspan="3">{{ $cat['label'] }}</td>
            <td style="text-align: center; font-weight: bold; color: {{ $cat['score'] >= 0 ? '#059669' : '#DC2626' }}">
                {{ $cat['score'] > 0 ? '+' : '' }}{{ number_format($cat['score'], 0) }}
            </td>
            <td style="text-align: center; color: #666666;">%{{ $cat['percentage'] }}</td>
        </tr>
    @endforeach

    <tr></tr> {{-- Boş Satır --}}

    {{-- ANA SIRALAMA TABLOSU --}}
    <tr>
        <th style="font-weight: bold; background-color: #1E1B4B; color: #FFFFFF;">Sıra</th>
        <th colspan="2" style="font-weight: bold; background-color: #1E1B4B; color: #FFFFFF;">Personel Adı Soyadı</th>
        <th style="font-weight: bold; background-color: #1E1B4B; color: #FFFFFF; text-align: center;">Dönem Puanı</th>
        <th style="font-weight: bold; background-color: #1E1B4B; color: #FFFFFF; text-align: center;">Bölüm Payı</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $index => $user)
        <tr>
            <td>#{{ $index + 1 }}</td>
            <td colspan="2" style="font-weight: bold;">{{ $user->name }}</td>
            <td style="font-weight: bold; text-align: center; color: #4F46E5;">{{ number_format($user->period_puan, 0) }}</td>
            <td style="text-align: center;">%{{ round(($user->period_puan / max(1, $totalBolumPuan)) * 100, 1) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
