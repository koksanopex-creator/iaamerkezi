<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class CustomerSyncController extends Controller
{
    public function sync(Request $request)
    {
        // Avoid looping: check if the request is marked as a sync request from another system
        if ($request->input('is_syncing') === true) {
            
            $action = $request->input('action'); // 'created' or 'updated'
            $data = $request->input('data');

            // Basic validation
            if (!$data || !isset($data['email']) || empty($data['email'])) {
                // If there's no email, fallback to matching by phone, or just name
                // To keep it simple, we match by email if present, else by name
            }

            try {
                // Find existing customer by email or name
                $customer = null;
                if (!empty($data['email'])) {
                    $customer = Customer::where('email', $data['email'])->first();
                }
                if (!$customer && !empty($data['name'])) {
                    $customer = Customer::where('name', $data['name'])->first();
                }

                if (!$customer) {
                    $customer = new Customer();
                }

                // IMPORTANT: we disable firing events momentarily to prevent infinite loops, 
                // OR we just use a static flag in the observer.
                // Disabling model events here is the safest way to avoid triggering our own Observer.
                Customer::withoutEvents(function () use ($customer, $data) {
                    $customer->name = $data['name'] ?? $customer->name;
                    $customer->email = $data['email'] ?? $customer->email;
                    $customer->phone = $data['phone'] ?? $customer->phone;
                    $customer->address = $data['address'] ?? $customer->address;
                    
                    if (isset($data['is_active'])) {
                        $customer->is_active = $data['is_active'];
                    }

                    $customer->save();
                });

                return response()->json(['status' => 'success', 'message' => 'Synced successfully'])
                    ->withHeaders([
                        'Access-Control-Allow-Origin' => '*',
                        'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                        'Access-Control-Allow-Headers' => 'Content-Type, Accept',
                    ]);
 
            } catch (\Exception $e) {
                Log::error('Customer sync failed: ' . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500)
                    ->withHeaders([
                        'Access-Control-Allow-Origin' => '*',
                        'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                        'Access-Control-Allow-Headers' => 'Content-Type, Accept',
                    ]);
            }
        }

        return response()->json(['status' => 'ignored'])
            ->withHeaders([
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Accept',
            ]);
    }

    public function bulkSync(Request $request)
    {
        try {
            set_time_limit(300); 

            \Illuminate\Support\Facades\Artisan::call('sync:all-to-takvim');
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            // Extract JSON from output
            $stats = [];
            if (preg_match('/RESULT_JSON:(.*)/', $output, $matches)) {
                $stats = json_decode($matches[1], true);
            }

            return response()->json([
                'status' => 'success', 
                'message' => 'Senkronizasyon tamamlandı.',
                'stats' => $stats
            ])->withHeaders([
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Accept',
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk sync failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500)
                ->withHeaders([
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Content-Type, Accept',
                ]);
        }
    }
}
