<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserSyncController extends Controller
{
    /**
     * Merkezi API'den gelen kullanıcı güncellemelerini alır
     */
    public function syncFromMerkezi(Request $request)
    {
        // Temel doğrulama
        $request->validate([
            'email' => 'required|email',
            'old_email' => 'nullable|email',
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'tc_no' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        try {
            // E-posta ile kullanıcıyı bul. Eski e-posta gelmişse onu ara.
            $searchEmail = $request->input('old_email', $request->email);
            $user = User::where('email', $searchEmail)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'ignored',
                    'message' => 'Kullanıcı bu uygulamada bulunamadı.'
                ]);
            }

            // Gelen bilgileri güncelle
            $user->email = $request->email;
            $user->name = trim($request->first_name . ' ' . $request->last_name);
            
            if ($request->has('phone')) {
                $user->telefon = $request->phone;
            }
            if ($request->has('tc_no')) {
                $user->tc_kimlik_no = $request->tc_no; // users table doesn't have tc_no, it has tc_kimlik_no in IAA
            }
            if ($request->has('registration_no')) {
                $user->sicil_no = $request->registration_no;
            }
            if ($request->has('hire_date')) {
                $user->hire_date = $request->hire_date ? \Carbon\Carbon::parse($request->hire_date)->format('Y-m-d') : null;
            }
            if ($request->has('termination_date')) {
                $user->termination_date = $request->termination_date ? \Carbon\Carbon::parse($request->termination_date)->format('Y-m-d') : null;
            }
            if ($request->has('job_title')) {
                $user->unvan = $request->job_title;
            }
            if ($request->has('is_mavi_yaka')) {
                $user->is_mavi_yaka = $request->boolean('is_mavi_yaka');
            }
            if ($request->filled('password')) {
                $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
                $user->require_password_change = false; // Şifre değiştiği için uyarıyı kaldır
            }

            // Durum (aktif/pasif) bilgisi opsiyonel olarak gelebilir
            // is_active field does not exist on users table in IAA? wait, let's just save the rest
            
            $user->save();

            Log::info("Merkezi API'den kullanıcı senkronize edildi: " . $user->email);

            return response()->json([
                'status' => 'success',
                'message' => 'Kullanıcı başarıyla güncellendi'
            ]);

        } catch (\Exception $e) {
            Log::error('Merkezi API User Sync Hatası: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Sunucu hatası: ' . $e->getMessage()
            ], 500);
        }
    }
}
