<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisciplinarySeeder extends Seeder
{
    public function run(): void
    {
        // 1. KATEGORİLER
        $categories = [
            ['ad' => 'İş Güvenliği (İSG)'],
            ['ad' => 'Genel Disiplin'],
            ['ad' => 'Etik ve Davranış'],
            ['ad' => 'Verimlilik ve Performans'],
        ];
        DB::table('disciplinary_categories')->insertOrIgnore($categories);

        // 2. ETKİ / ŞİDDET TANIMLARI
        $impacts = [
            ['tanim' => 'Kişiye Karşı (Hafif)', 'puan' => 2],
            ['tanim' => 'Kişiye Karşı (Ağır)', 'puan' => 5],
            ['tanim' => 'Gruba / Ekibe Karşı', 'puan' => 8],
            ['tanim' => 'Topluma / Kuruma Karşı', 'puan' => 10],
        ];
        DB::table('disciplinary_impacts')->insertOrIgnore($impacts);

        // 3. KAPSAM TANIMLARI
        $scopes = [
            ['tanim' => 'Sadece Kendi İşini Etkiler', 'puan' => 2],
            ['tanim' => 'Bir Grubun İşini Bozar', 'puan' => 5],
            ['tanim' => 'Tüm Sistemi / Üretimi Bozar', 'puan' => 10],
        ];
        DB::table('disciplinary_scopes')->insertOrIgnore($scopes);

        // 4. KATSAYILAR
        $multipliers = [
            ['tekrar_sayisi' => 1, 'katsayi' => 1.00],
            ['tekrar_sayisi' => 2, 'katsayi' => 2.00],
            ['tekrar_sayisi' => 3, 'katsayi' => 3.00],
            ['tekrar_sayisi' => 4, 'katsayi' => 5.00],
        ];
        DB::table('disciplinary_multipliers')->insertOrIgnore($multipliers);

        // 5. CEZA SKALASI
        $scales = [
            ['min_puan' => 0,   'max_puan' => 40,  'ceza_adi' => 'Sözlü Uyarı'],
            ['min_puan' => 41,  'max_puan' => 80,  'ceza_adi' => 'Yazılı Uyarı'],
            ['min_puan' => 81,  'max_puan' => 150, 'ceza_adi' => 'Kınama'],
            ['min_puan' => 151, 'max_puan' => 250, 'ceza_adi' => '1-3 Gün Uzaklaştırma'],
            ['min_puan' => 251, 'max_puan' => 9999,'ceza_adi' => 'Fesih Değerlendirme'],
        ];
        DB::table('disciplinary_penalty_scales')->insertOrIgnore($scales);
    }
}