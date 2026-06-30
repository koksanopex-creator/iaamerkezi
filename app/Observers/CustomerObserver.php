<?php

namespace App\Observers;

use App\Models\Customer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        $this->syncWithTakvim($customer, 'created');
        $this->syncWithMerkeziAPI($customer, 'create');
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        $this->syncWithTakvim($customer, 'updated');
        $this->syncWithMerkeziAPI($customer, 'update');
    }

    private function syncWithTakvim(Customer $customer, string $action)
    {
        try {
            $takvimUrl = config('services.takvim.url');
            $url = $takvimUrl . '/api/customers/sync';
            
            Http::timeout(3)->post($url, [
                'is_syncing' => true,
                'action' => $action,
                'data' => [
                    'remote_id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'address' => $customer->address,
                    'is_active' => $customer->is_active,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sync customer to Takvim: ' . $e->getMessage());
        }
    }

    private function syncWithMerkeziAPI(Customer $customer, string $action)
    {
        try {
            $merkeziUrl = 'http://localhost:8001'; // Default, ideally from config
            if (config('services.merkezi.url')) {
                $merkeziUrl = config('services.merkezi.url');
            }
            $url = rtrim($merkeziUrl, '/') . '/api/internal/companies/sync';
            
            $apiKey = env('CENTRAL_SSO_API_KEY', 'random_secret_key_123');

            Http::timeout(3)->withHeaders([
                'X-App-Key' => $apiKey
            ])->post($url, [
                'iaa_customer_id' => $customer->id,
                'action' => $action,
                'name' => $customer->name,
                'tax_no' => $customer->tax_number,
                'tax_office' => $customer->tax_office,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'address' => $customer->address,
                'is_active' => $customer->is_active,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sync customer to Merkezi API: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        $this->syncWithMerkeziAPI($customer, 'delete');
    }

    /**
     * Handle the Customer "restored" event.
     */
    public function restored(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "force deleted" event.
     */
    public function forceDeleted(Customer $customer): void
    {
        //
    }
}
