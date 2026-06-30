<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\User;
use App\Models\MusteriSikayeti;
use App\Models\SikayetIadesi;
use Illuminate\Support\Facades\Http;

class SyncAllToTakvim extends Command
{
    protected $signature = 'sync:all-to-takvim';
    protected $description = 'Sync all customers, contacts, complaints and returns to Takvim system';

    public function handle()
    {
        $this->info('Starting bulk synchronization...');
        
        $bulkData = [
            'customers' => [],
            'contacts' => [],
            'users' => [],
            'complaints' => [],
            'returns' => [],
        ];

        // 1. Prepare Customers
        $customers = Customer::all();
        $this->info('Preparing ' . $customers->count() . ' customers...');
        foreach ($customers as $customer) {
            $bulkData['customers'][] = [
                'remote_id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'is_active' => $customer->is_active,
            ];
        }

        // 2. Prepare Contacts
        $contacts = User::where('is_personnel', false)->whereNotNull('customer_id')->get();
        $this->info('Preparing ' . $contacts->count() . ' contacts...');
        foreach ($contacts as $contact) {
            $customer = $contact->customer;
            if ($customer) {
                $bulkData['contacts'][] = [
                    'remote_id' => $contact->id,
                    'name' => $contact->name,
                    'email' => $contact->email,
                    'phone' => $contact->telefon,
                    'title' => $contact->unvan,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'customer_remote_id' => $customer->id,
                ];
            }
        }

        // 2.1 Prepare Personnel
        $personnel = User::where('is_personnel', true)->get();
        $this->info('Preparing ' . $personnel->count() . ' personnel members...');
        foreach ($personnel as $person) {
            $bulkData['users'][] = [
                'name' => $person->name,
                'email' => $person->email,
            ];
        }

        // 3. Prepare Complaints
        $complaints = MusteriSikayeti::with(['customer', 'bolum'])->get();
        $this->info('Preparing ' . $complaints->count() . ' complaints...');
        foreach ($complaints as $complaint) {
            $customer = $complaint->customer;
            if ($customer) {
                $attachments = [];
                foreach ($complaint->dosyalar as $dosya) {
                    $attachments[] = [
                        'name' => $dosya->orijinal_adi,
                        'url' => asset('storage/' . $dosya->dosya_yolu),
                        'mime_type' => $dosya->mime_tipi
                    ];
                }

                $payload = [
                    'remote_id' => $complaint->id,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'title' => $complaint->musteri_sikayet_konusu,
                    'description' => $complaint->musteri_sikayet_detayi,
                    'status' => $complaint->musteri_durum,
                    'attachments' => [], // Dosyaları bulk-sync sırasında gönderme (deadlock önlemek için)
                    'remote_creator_name' => $complaint->olusturanKurulUyesi ? $complaint->olusturanKurulUyesi->name : 'Sistem',
                    'remote_url' => $complaint->iaa_id 
                                    ? route('proje.workspace.show', $complaint->iaa_id) 
                                    : route('admin.sikayetler.show', $complaint->id),
                ];
                if ($complaint->bolum && $complaint->bolum->takvim_business_unit_id) {
                    $payload['business_unit_id'] = $complaint->bolum->takvim_business_unit_id;
                }
                $bulkData['complaints'][] = $payload;
            }
        }

        // 4. Prepare Returns
        $returns = SikayetIadesi::with(['musteriSikayeti.customer', 'musteriSikayeti.bolum'])->get();
        $this->info('Preparing ' . $returns->count() . ' returns...');
        foreach ($returns as $return) {
            $sikayet = $return->musteriSikayeti;
            if ($sikayet && $sikayet->customer) {
                $payload = [
                    'remote_id' => $return->id,
                    'remote_complaint_id' => $sikayet->id,
                    'customer_name' => $sikayet->customer->name,
                    'customer_email' => $sikayet->customer->email,
                    'product_name' => $return->urun_turu,
                    'quantity' => $return->miktar,
                    'unit' => $return->birim,
                    'shipped_quantity' => $return->toplam_parti_miktari,
                    'shipped_unit' => $return->birim,
                    'reason' => $return->iade_sebebi,
                    'return_date' => $sikayet->musteri_sikayet_tarihi ? $sikayet->musteri_sikayet_tarihi->format('Y-m-d') : null,
                    'remote_url' => $sikayet->iaa_id 
                                    ? route('proje.workspace.show', $sikayet->iaa_id) 
                                    : route('admin.sikayetler.show', $sikayet->id),
                ];
                if ($sikayet->bolum && $sikayet->bolum->takvim_business_unit_id) {
                    $payload['business_unit_id'] = $sikayet->bolum->takvim_business_unit_id;
                }
                $bulkData['returns'][] = $payload;
            }
        }

        $this->info('Sending bulk data to Takvim API...');
        
        try {
            $takvimUrl = config('services.takvim.url');
            $response = Http::timeout(120)->retry(2, 500)->post($takvimUrl . '/api/customers/bulk-sync', [
                'is_syncing' => true,
                'data' => $bulkData,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $stats = $responseData['stats'] ?? [
                    'customer' => ['success' => count($bulkData['customers']), 'error' => 0],
                    'contact' => ['success' => count($bulkData['contacts']), 'error' => 0],
                    'user' => ['success' => count($bulkData['users']), 'error' => 0],
                    'complaint' => ['success' => count($bulkData['complaints']), 'error' => 0],
                    'return' => ['success' => count($bulkData['returns']), 'error' => 0],
                ];
                
                $this->info('Synchronization complete!');
                $summary = "Sync Summary: Customer(" . $stats['customer']['success'] . "/" . $stats['customer']['error'] . "), " .
                           "Contact(" . $stats['contact']['success'] . "/" . $stats['contact']['error'] . "), " .
                           "User(" . $stats['user']['success'] . "/" . $stats['user']['error'] . "), " .
                           "Complaint(" . $stats['complaint']['success'] . "/" . $stats['complaint']['error'] . "), " .
                           "Return(" . $stats['return']['success'] . "/" . $stats['return']['error'] . ")";
                
                \Illuminate\Support\Facades\Log::channel('single')->info($summary);
                $this->line('RESULT_JSON:' . json_encode($stats));
            } else {
                \Illuminate\Support\Facades\Log::error("Failed to bulk sync - Status: " . $response->status() . " Response: " . $response->body());
                $this->error('Failed to sync. Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error in bulk sync: " . $e->getMessage());
            $this->error('Exception: ' . $e->getMessage());
        }
    }
}
