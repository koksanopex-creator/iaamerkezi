<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MusteriSikayeti;
use App\Models\Iaa;
use App\Models\DisciplinaryCase;
use App\Models\ArabuluculukCase;
use App\Models\ArabuluculukMeeting; // Bu modelin olduğunu varsayıyoruz, yoksa DB facade kullanacağız
use App\Models\Arabulucu;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TestVerisiSeeder extends Seeder
{
    public function run()
    {
        // 1. TEMİZLİK AŞAMASI
        $this->command->warn('Tablolar ve Foreign Key kısıtlamaları temizleniyor...');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MusteriSikayeti::truncate();
        Iaa::truncate();
        DisciplinaryCase::truncate();
        ArabuluculukCase::truncate();
        
        // Toplantı tablosunu da temizleyelim (Model ismi farklı olabilir, garanti olsun diye tablo adıyla siliyorum)
        DB::table('arabuluculuk_meetings')->truncate(); 
        
        DB::table('sikayet_kategorileri')->truncate();
        DB::table('disciplinary_behaviors')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. HAZIRLIK AŞAMASI
        $faker = Faker::create('tr_TR');
        $users = User::all();

        if ($users->count() === 0) {
            $this->command->error('Sistemde hiç kullanıcı yok! Lütfen önce kullanıcı oluşturun.');
            return;
        }

        // Kategori Oluştur
        $kategoriIds = [];
        $kategoriler = ['Ürün Kalitesi', 'Lojistik/Sevkiyat', 'İletişim/Davranış', 'Diğer'];
        foreach($kategoriler as $kat) {
            $kategoriIds[] = DB::table('sikayet_kategorileri')->insertGetId([
                'ad' => $kat, 
                'created_at' => now(), 
                'updated_at' => now()
            ]);
        }

        // Disiplin Maddesi Oluştur
        $catId = DB::table('disciplinary_categories')->insertGetId(['ad' => 'Genel Davranış', 'created_at' => now(), 'updated_at' => now()]);
        $behaviorId = DB::table('disciplinary_behaviors')->insertGetId([
            'category_id' => $catId,
            'tanim' => 'Mesai saatlerine uymamak',
            'aktif_mi' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Arabulucu Kontrolü
        if (Arabulucu::count() === 0) {
            Arabulucu::create([
                'name' => 'Av. Test Arabulucu',
                'sicil_no' => '12345',
                'email' => 'arabulucu@test.com',
                'is_active' => true,
                'created_by' => $users->first()->id
            ]);
        }
        $arabulucuId = Arabulucu::first()->id;


        // 3. İAA ÖNERİLERİ
        $this->command->info('1. İAA Önerileri Oluşturuluyor...');
        $olusturulanIaaIds = [];

        // Personel
        for ($i = 0; $i < 15; $i++) {
            $tarih = ($i < 10) ? $faker->dateTimeBetween('-6 months', 'last week') : $faker->dateTimeBetween('this week', 'now');
            $durum = $faker->randomElement(['Havuzda', 'Atandı', 'Devam Ediyor', 'Tamamlandı']);
            $updatedAt = ($durum == 'Tamamlandı') ? Carbon::parse($tarih)->addDays(rand(5, 20)) : $tarih;

            $iaa = Iaa::create([
                'gonderen_user_id' => $users->random()->id,
                'baslik' => 'Öneri: ' . $faker->sentence(3),
                'mevcut_durum' => $faker->paragraph,
                'oneri' => $faker->paragraph,
                'durum' => $durum,
                'created_at' => $tarih,
                'updated_at' => $updatedAt,
            ]);
            $olusturulanIaaIds[] = $iaa->id;
        }

        // Misafir
        for ($i = 0; $i < 5; $i++) {
            $tarih = $faker->dateTimeBetween('-2 months', 'now');
            Iaa::create([
                'gonderen_user_id' => null,
                'baslik' => 'Misafir Görüşü: ' . $faker->word,
                'mevcut_durum' => 'Dışarıdan gözlem...',
                'oneri' => $faker->paragraph,
                'durum' => 'Havuzda',
                'created_at' => $tarih,
                'updated_at' => $tarih,
            ]);
        }


        // 4. MÜŞTERİ ŞİKAYETLERİ
        $this->command->info('2. Müşteri Şikayetleri Oluşturuluyor...');
        for ($i = 0; $i < 20; $i++) {
            $tarih = ($i < 14) ? $faker->dateTimeBetween('-1 year', 'last month') : $faker->dateTimeBetween('first day of this month', 'now');
            $durum = $faker->randomElement(['Yeni', 'İşlemde', 'Çözümlendi', 'Kapatıldı']);
            $updatedAt = ($durum == 'Kapatıldı' || $durum == 'Çözümlendi') ? Carbon::parse($tarih)->addDays(rand(2, 10)) : $tarih;
            
            $baglanacakIaaId = ($i < 5 && isset($olusturulanIaaIds[$i])) ? $olusturulanIaaIds[$i] : null;

            MusteriSikayeti::create([
                'sikayet_kategorisi_id' => $faker->randomElement($kategoriIds),
                'musteri_adi' => $faker->company,
                'musteri_iletisim' => $faker->companyEmail,
                'konum_tipi' => $faker->randomElement(['Yurt İçi', 'Yurt Dışı']),
                'musteri_sikayet_tarihi' => $tarih,
                'musteri_sikayet_konusu' => 'Hata Bildirimi: ' . $faker->word,
                'musteri_sikayet_detayi' => $faker->realText(150),
                'musteri_durum' => $durum,
                'created_at' => $tarih,
                'updated_at' => $updatedAt,
                'iaa_id' => $baglanacakIaaId,
            ]);
        }


        // 5. DİSİPLİN
        $this->command->info('3. Disiplin Dosyaları Oluşturuluyor...');
        for ($i = 0; $i < 15; $i++) {
            $tarih = $faker->dateTimeBetween('-1 year', 'now');
            $durum = $faker->randomElement(['savunma_bekleniyor', 'kurul_karari_bekleniyor', 'dosya_kapatildi']);
            
            DisciplinaryCase::create([
                'user_id' => $users->random()->id,
                'reporter_id' => $users->random()->id,
                'behavior_id' => $behaviorId,
                'olay_tarihi' => $tarih,
                'olay_aciklamasi' => $faker->sentence,
                'durum' => $durum,
                'created_at' => $tarih,
                'updated_at' => $tarih,
            ]);
        }


        // 6. ARABULUCULUK (DÜZELTİLEN KISIM)
        $this->command->info('4. Arabuluculuk Dosyaları ve Toplantıları Oluşturuluyor...');
        for ($i = 0; $i < 15; $i++) {
            $tarih = $faker->dateTimeBetween('-1 year', 'now');
            $durum = $faker->randomElement(['gorusme_suruyor', 'odeme_bekliyor', 'anlasildi', 'kapatildi']);
            
            // A. Önce Dosyayı Oluştur (toplanti_tarihi OLMADAN)
            $case = ArabuluculukCase::create([
                'type' => $faker->randomElement(['ihtiyari', 'zorunlu']),
                'arabulucu_id' => $arabulucuId,
                'calisan_user_id' => $users->random()->id,
                'created_by' => $users->first()->id,
                'dosya_no' => '2025/' . $faker->numberBetween(100, 999),
                'status' => $durum,
                'created_at' => $tarih,
                'updated_at' => $tarih,
            ]);

            // B. Sonra Bu Dosyaya Bağlı Toplantı Oluştur (Varsa tablosuna ekle)
            // Yaklaşan veya geçmiş bir toplantı
            $toplantiTarihi = ($i < 10) ? $faker->dateTimeBetween('-1 month', '-1 day') : $faker->dateTimeBetween('now', '+1 week');
            
            DB::table('arabuluculuk_meetings')->insert([
                'case_id' => $case->id, // Oluşturulan dosyanın ID'si
                'meeting_date' => $toplantiTarihi,
                'meeting_notes' => 'İlk görüşme notları...',
                'created_at' => $tarih,
                'updated_at' => $tarih
            ]);
        }

        $this->command->info('------------------------------------------');
        $this->command->info(' TÜM VERİLER (TOPLANTILAR DAHİL) SORUNSUZ OLUŞTURULDU! ');
        $this->command->info('------------------------------------------');
    }
}