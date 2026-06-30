<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class MusteriSahaTemsilcisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Müşteri Saha Temsilcisi rolünü oluştur (varsa hata vermez)
        Role::firstOrCreate(['name' => 'Müşteri Saha Temsilcisi', 'guard_name' => 'web']);

        $this->command->info('Müşteri Saha Temsilcisi rolü başarıyla oluşturuldu.');
    }
}
