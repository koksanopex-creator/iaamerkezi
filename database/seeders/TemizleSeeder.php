<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MusteriSikayeti;
use App\Models\Iaa;
use App\Models\DisciplinaryCase;
use App\Models\ArabuluculukCase;

class TemizleSeeder extends Seeder
{
    public function run()
    {
        $this->command->warn('VERİLER SİLİNİYOR...');

        // Foreign Key hatası almamak için kontrolü kapatıyoruz
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. İş Süreçleri Tabloları (Bunları kesin silmek istersin)
        MusteriSikayeti::truncate();
        Iaa::truncate();
        DisciplinaryCase::truncate();
        ArabuluculukCase::truncate();
        
        // Bağlı alt tabloları da temizle (Eğer varsa)
        DB::table('arabuluculuk_meetings')->truncate(); 
        // DB::table('iaa_talepleri')->truncate(); // Eğer varsa bunu da aç

        // 2. Ayar Tabloları (İstersen bunları silmeyebilirsin)
        // DB::table('sikayet_kategorileri')->truncate();
        // DB::table('disciplinary_behaviors')->truncate();

        // DİKKAT: Users tablosunu silersen sisteme giriş yapamazsın!
        // Eğer users tablosunu da silmek istersen aşağıdaki yorumu kaldır:
        // \App\Models\User::truncate(); 

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('------------------------------------------');
        $this->command->info(' TABLOLAR TERTEMİZ OLDU! ');
        $this->command->info('------------------------------------------');
    }
}