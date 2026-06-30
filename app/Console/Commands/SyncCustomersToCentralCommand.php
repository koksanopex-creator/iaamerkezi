<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Customer;

class SyncCustomersToCentralCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sso:sync-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mevcut İAA müşterilerini Merkezi API (Company tablosu) ile senkronize eder.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Müşteriler Merkezi API\'ye aktarılıyor...');
        
        $centralUrl = config('services.central_sso.url');
        $apiKey = config('services.central_sso.api_key');

        if (!$centralUrl || !$apiKey) {
            $this->error('HATA: .env dosyasında CENTRAL_SSO_URL veya CENTRAL_SSO_API_KEY eksik!');
            return 1;
        }

        $customers = Customer::all();
        $this->getOutput()->progressStart($customers->count());

        $successCount = 0;
        $errorCount = 0;

        foreach ($customers as $customer) {
            try {
                $response = Http::withHeaders([
                    'X-App-Key' => $apiKey,
                    'Accept' => 'application/json',
                ])->post($centralUrl . '/api/internal/companies/sync', [
                    'iaa_customer_id' => $customer->id,
                    'action' => 'update',
                    'name' => $customer->name,
                    'tax_no' => $customer->tax_number ?? null,
                    'tax_office' => $customer->tax_office ?? null,
                    'phone' => $customer->phone ?? null,
                    'email' => $customer->email ?? null,
                    'address' => $customer->address ?? null,
                    'is_active' => $customer->is_active ?? true,
                ]);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $this->newLine();
                    $this->error("Müşteri ID {$customer->id} gönderilirken hata: " . $response->body());
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("Müşteri ID {$customer->id} aktarım hatası: " . $e->getMessage());
            }

            $this->getOutput()->progressAdvance();
        }

        $this->getOutput()->progressFinish();
        $this->info("Müşteriler aktarıldı! Başarılı: $successCount, Hatalı: $errorCount");

        // İkinci Aşama: Müşteri Yetkililerini (Kullanıcıları) aktar
        $this->info('Müşteri yetkilileri Merkezi API\'ye aktarılıyor...');
        $customerUsers = \App\Models\User::whereHas('customers')->get();
        $this->getOutput()->progressStart($customerUsers->count());

        $userSuccess = 0;
        $userError = 0;

        $syncService = app(\App\Services\CentralSsoSyncService::class);

        foreach ($customerUsers as $user) {
            try {
                // Şifreyi boş göndererek mevcut şifreyi ezmesini önleriz (SyncService bunu hallediyor)
                $result = $syncService->syncUser($user, null, 'customer');
                if ($result) {
                    $userSuccess++;
                } else {
                    $userError++;
                }
            } catch (\Exception $e) {
                $userError++;
            }
            $this->getOutput()->progressAdvance();
        }

        $this->getOutput()->progressFinish();
        $this->info("Kullanıcılar aktarıldı! Başarılı: $userSuccess, Hatalı: $userError");

        $this->info("TÜM SENKRONİZASYON İŞLEMLERİ TAMAMLANDI!");

        return 0;
    }
}
