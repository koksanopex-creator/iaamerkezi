<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Bolum;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // Role modelini import et

class UserSeeder extends Seeder
{
    use WithoutModelEvents; // Eklendi

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Varsayılan kullanıcıların listesi
        $kullanicilar = [
            [ 'name' => 'Super Admin', 'email' => 'admin@iaa.com', 'bolum' => 'Diğer', 'roller' => ['Superadmin'] ], // Roller array oldu
            [ 'name' => 'Serkan Akardeniz', 'email' => 'serkan.akardeniz@koksan.com', 'bolum' => 'İş Sağlığı ve Yangın', 'roller' => ['Kullanıcı'] ],
            [ 'name' => 'Barış YALÇIN', 'email' => 'baris.yalcin@koksan.com', 'bolum' => 'İş Sağlığı ve Yangın', 'roller' => ['Bölüm Lideri'] ],
            [ 'name' => 'Üzeyir Demir', 'email' => 'uzeyir.demir@koksan.com', 'bolum' => 'Geri Dönüşüm', 'roller' => ['Kullanıcı'] ],
            [ 'name' => 'Yusuf Aydın ZEHİR', 'email' => 'yusuf.zehir@koksan.com', 'bolum' => 'Geri Dönüşüm', 'roller' => ['Bölüm Lideri'] ],
            [ 'name' => 'Serkan Güzelçiftçi', 'email' => 'serkan.guzelciftci@koksan.com', 'bolum' => 'Preform', 'roller' => ['Kullanıcı'] ],
            [ 'name' => 'Furkan Özbiçer', 'email' => 'furkan.ozbicer@koksan.com', 'bolum' => 'Preform', 'roller' => [] ], // Boş array
            [ 'name' => 'Cihangir Kaplan', 'email' => 'cihangir.kaplan@koksan.com', 'bolum' => 'Kapak', 'roller' => [] ],
            [ 'name' => 'Serkan ATAK', 'email' => 'serkan.atak@koksan.com', 'bolum' => 'Kapak', 'roller' => ['Bölüm Lideri'] ],
            [ 'name' => 'Mehmet Türkaslan', 'email' => 'mehmet.turkaslan@koksan.com', 'bolum' => 'Kapak', 'roller' => [] ],
            [ 'name' => 'Sinan Poyraz', 'email' => 'sinan.poyraz@koksan.com', 'bolum' => 'Kapak', 'roller' => ['Kullanıcı'] ],
             // Örnek: Yeni rolü olan bir kullanıcı ekleyelim
            [ 'name' => 'Çözüm Lideri Örnek', 'email' => 'cozum.lideri@iaa.com', 'bolum' => 'Diğer', 'roller' => ['Müşteri Şikayeti Çözüm Lideri'] ],
        ];

        foreach ($kullanicilar as $kullaniciData) {
            // Bölüm adından bölüm ID'sini bul
            $bolum = Bolum::where('ad', $kullaniciData['bolum'])->first();

            // === DEĞİŞTİ: create yerine updateOrCreate kullanıldı ===
            // E-postaya göre kullanıcıyı bul veya oluştur/güncelle
            $user = User::updateOrCreate(
                ['email' => $kullaniciData['email']], // Eşleşme koşulu
                [                                     // Güncellenecek veya oluşturulacak veriler
                    'name' => $kullaniciData['name'],
                    'password' => Hash::make($kullaniciData['email']), // Şifre, e-posta ile aynı (her çalıştığında güncellenir)
                    'bolum_id' => $bolum ? $bolum->id : null,
                    'onaylandi_mi' => true,
                ]
            );
            // =======================================================

            // === DEĞİŞTİ: Rol ataması syncRoles ile yapıldı ===
            // Kullanıcının rollerini güncelle (öncekileri silip yenilerini ekler)
             // Rollerin veritabanında var olduğundan emin olalım (Seeder sırası önemli)
            $mevcutRoller = Role::whereIn('name', $kullaniciData['roller'])->pluck('name')->toArray();
            if(!empty($mevcutRoller)){
                 $user->syncRoles($mevcutRoller);
            } else {
                 $user->syncRoles([]); // Rol yoksa temizle
            }
            // ==================================================
        }
    }
}
