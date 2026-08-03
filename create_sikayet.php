<?php
$user = App\Models\User::where('name', 'like', '%Ahmet Alan%')->first();
if (!$user) {
    echo "Ahmet Alan bulunamadi!\n";
    exit;
}

$takim = App\Models\Takim::where('lider_user_id', $user->id)->first();
if (!$takim) {
    $takim = $user->takimlar()->first();
}

$takim_id = $takim ? $takim->id : null;
if (!$takim_id) {
    echo "Takim bulunamadi!\n";
} else {
    echo "Takim ID: " . $takim_id . " - " . $takim->ad . "\n";
}

$sikayet = App\Models\MusteriSikayeti::create([
    'musteri_adi' => 'Test Müşterisi ' . rand(100, 999),
    'musteri_iletisim' => '05001234567',
    'musteri_sikayet_konusu' => 'Gecikmiş Teslimat Bildirimi',
    'musteri_sikayet_detayi' => 'Siparişimiz belirtilen tarihte tarafımıza ulaşmadı. Acil dönüş rica ediyoruz.',
    'musteri_urun_veya_hizmet' => 'Rastgele Ürün',
    'musteri_sikayet_tarihi' => now(),
    'musteri_durum' => 'Yeni',
    'musteri_oncelik' => 'Normal',
    'atanan_cozum_takimi_id' => $takim_id,
]);

echo "Sikayet olusturuldu ID: " . $sikayet->id . "\n";
