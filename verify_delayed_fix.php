<?php

use App\Models\Iaa;
use App\Models\Bolum;
use App\Models\User;

$bolumAd = 'Kapak';
$bolum = Bolum::where('ad', $bolumAd)->first();

$personelIds = User::where('bolum_id', $bolum->id)->pluck('id');

$inclusiveQuery = function ($q) use ($bolum, $personelIds) {
    $q->where('bolum_id', $bolum->id)
        ->orWhereHas('projeEkibi', fn($sq) => $sq->whereIn('users.id', $personelIds))
        ->orWhereHas('atananTakim', fn($sq) => $sq->whereIn('lider_user_id', $personelIds));
};

$baseQuery = Iaa::where(function ($q) use ($bolum, $personelIds, $inclusiveQuery) {
    $inclusiveQuery($q);
    $q->orWhereHas('musteriSikayeti.sikayetKategori', function ($sq) use ($bolum) {
        $sq->where('bolum_id', $bolum->id);
    });
});

// GÜNCELLENEN MANTIK
$excludeStatuses = ['Tamamlandı', 'Talep Olarak Kapatıldı', 'talep_olarak_kapatildi', 'Reddedildi', 'İptal Edildi'];

$gecikenBaseQuery = (clone $baseQuery)->whereNotIn('durum', $excludeStatuses)
    ->whereHas('talepEdenTakimlar', function ($q) {
        $q->where('iaa_talepleri.due_date', '<', now());
    });

$iaaGecikenCount = (clone $gecikenBaseQuery)->whereDoesntHave('musteriSikayeti')->count();
$sikayetGecikenCount = (clone $gecikenBaseQuery)->whereHas('musteriSikayeti')->count();

echo "Bölüm: $bolumAd (ID: {$bolum->id})\n";
echo "Geciken Saf İAA Sayısı: $iaaGecikenCount\n";
echo "Geciken Şikayet Sayısı: $sikayetGecikenCount\n";
echo "Toplam Geciken (Grafik değeri): " . ($iaaGecikenCount + $sikayetGecikenCount) . "\n";

$sikayetler = (clone $gecikenBaseQuery)->whereHas('musteriSikayeti')->with('musteriSikayeti')->get();
foreach ($sikayetler as $s) {
    echo "- Geciken Şikayet: " . ($s->musteriSikayeti->musteri_sikayet_konusu ?? $s->baslik) . " (ID: $s->id) | Durum: $s->durum\n";
}
