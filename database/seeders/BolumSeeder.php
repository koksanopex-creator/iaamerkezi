<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bolum; // Bolum modelini kullanacağımızı belirtiyoruz

class BolumSeeder extends Seeder
{
    use WithoutModelEvents; // Eklendi

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Varsayılan bölümlerin listesi
        $bolumler = [
            'Diğer', 'Müşteri', 'İş Sağlığı ve Yangın', 'İnsan Kaynakları',
            'Geri Dönüşüm', 'Levha', 'Preform', 'Kapak'
        ];

        // Her bir bölüm adını veritabanına kaydet (varsa güncelle, yoksa oluştur)
        foreach ($bolumler as $bolumAdi) {
            // === DEĞİŞTİ: create yerine firstOrCreate kullanıldı ===
            Bolum::firstOrCreate(['ad' => $bolumAdi]);
        }
    }
}
