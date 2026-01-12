<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArabuluculukRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Yetki kontrolünü Policy veya Controller'da yapacağız, burası true kalsın.
        return true; 
    }

    public function rules(): array
    {
        return [
            'calisan_user_id' => 'required|exists:users,id',
            'type' => 'required|in:ihtiyari,zorunlu',
            'dosya_no' => 'nullable|string|max:50',
            
            // Eğer İhtiyari ise personel seçimi zorunlu, Zorunlu ise Hukuk süreci
            'arabulucu_id' => 'nullable|exists:arabulucular,id',
            
            // Parasal
            'talep_tutari' => 'nullable|numeric|min:0',
            'anlasilan_tutar' => 'nullable|numeric|min:0',
            
            // Avukat Atamaları (Opsiyonel başta)
            'internal_lawyer_id' => 'nullable|exists:users,id',
            'external_lawyer_id' => 'nullable|exists:users,id',
            
            // Dosyalar (Opsiyonel, sonradan da yüklenebilir)
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,udf,jpg,png|max:10240', // UDF izni burada
        ];
    }
}