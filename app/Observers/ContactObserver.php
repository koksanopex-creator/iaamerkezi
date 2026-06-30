<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContactObserver
{
    public function created(User $user)
    {
        $this->syncWithTakvim($user, 'created');
    }

    public function updated(User $user)
    {
        $this->syncWithTakvim($user, 'updated');
    }

    protected function syncWithTakvim(User $user, $action)
    {
        // Avoid infinite loops
        if (request()->has('is_syncing')) {
            return;
        }

        $type = $user->is_personnel ? 'user' : 'contact';
        $payload = [
            'remote_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        if (!$user->is_personnel) {
            $customer = $user->customer;
            if (!$customer) {
                return;
            }
            $payload['phone'] = $user->telefon;
            $payload['title'] = $user->unvan;
            $payload['customer_name'] = $customer->name;
            $payload['customer_email'] = $customer->email;
            $payload['customer_remote_id'] = $customer->id;
        }

        try {
            $takvimUrl = config('services.takvim.url');
            $response = Http::post($takvimUrl . '/api/customers/sync', [
                'is_syncing' => true,
                'type' => $type,
                'action' => $action,
                'data' => $payload
            ]);

            if (!$response->successful()) {
                Log::error("Failed to sync $type to Takvim: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Error syncing $type to Takvim: " . $e->getMessage());
        }
    }
}
