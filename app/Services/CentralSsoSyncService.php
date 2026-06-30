<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CentralSsoSyncService
{
    /**
     * Yerel veritabanında oluşturulan bir kullanıcıyı Merkezi SSO sistemine eşitler.
     *
     * @param User $user Eşitlenecek kullanıcı modeli
     * @param string|null $plainPassword Hashing yapılmadan önceki düz şifre
     * @param string $profession Kullanıcı tipi: 'customer' veya 'personnel'
     * @return bool Senkronizasyon başarılı mı
     */
    public function syncUser(User $user, ?string $plainPassword = null, string $profession = 'personnel'): bool
    {
        $ssoUrl = config('services.central_sso.url');
        $apiKey = config('services.central_sso.api_key');

        if (empty($ssoUrl) || empty($apiKey)) {
            Log::warning('Central SSO Sync: SSO URL veya API Anahtarı tanımlı değil. Senkronizasyon atlandı.');
            return false;
        }

        // İsim kolonundan ad ve soyadı çıkar
        $parts = explode(' ', trim($user->name));
        if (count($parts) > 1) {
            $lastName = array_pop($parts);
            $firstName = implode(' ', $parts);
        } else {
            $firstName = $user->name;
            $lastName = '';
        }

        try {
            $endpoint = rtrim($ssoUrl, '/') . '/api/internal/sync-user';
            
            $payload = [
                'email' => $user->email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'verified' => true,
                'profession' => $profession,
                'tc_no' => $user->tc_kimlik_no ?? null,
                'phone' => $user->telefon ?? null,
                'registration_no' => $user->sicil_no ?? null,
                'hire_date' => $user->hire_date ? \Carbon\Carbon::parse($user->hire_date)->format('Y-m-d') : null,
                'termination_date' => $user->termination_date ? \Carbon\Carbon::parse($user->termination_date)->format('Y-m-d') : null,
                'job_title' => $user->unvan ?? null,
                'iaa_customer_id' => ($profession === 'customer') ? $user->customer_id : null,
            ];

            if ($plainPassword !== null) {
                $payload['password'] = $plainPassword;
            }

            Log::info('Central SSO Sync: Gönderilen veri: ' . json_encode(array_merge($payload, ['password' => '******'])));

            $response = Http::timeout(10)
                ->withHeaders([
                    'X-App-Key' => $apiKey,
                    'Accept' => 'application/json',
                ])
                ->post($endpoint, $payload);

            if ($response->successful()) {
                Log::info('Central SSO Sync: Kullanıcı başarıyla eşitlendi. Response: ' . $response->body());
                return true;
            }

            Log::error('Central SSO Sync: Eşitleme başarısız oldu. Hata Kodu: ' . $response->status() . ' - Mesaj: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('Central SSO Sync: Senkronizasyon sırasında istisna oluştu: ' . $e->getMessage());
            return false;
        }
    }
}
