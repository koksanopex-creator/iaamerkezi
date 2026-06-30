<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function chat(array $history, array $tools = [])
    {
        if (!$this->apiKey) {
            return [
                'error' => 'API Key tanımlanmamış. Lütfen yönetici ile iletişime geçin.'
            ];
        }

        // 1. Dinamik Model Seçimi
        $activeModel = $this->discoverModel();

        // 2. Tool Tanımlamalarını Formatla
        $formattedTools = [];
        if (!empty($tools)) {
            $formattedTools = [
                ['function_declarations' => $this->formatToolsForGemini($tools)]
            ];
        }

        // 3. Mesajları Formatla
        $contents = array_map(function ($msg) {
            return [
                'role' => $msg['role'] === 'ai' ? 'model' : 'user',
                'parts' => [['text' => $msg['content']]]
            ];
        }, $history);

        $payload = [
            'contents' => $contents,
            'tools' => $formattedTools
        ];

        // API Versiyonu: V1beta genellikle tool kullanımı için daha kararlıdır.
        $baseUrl = "https://generativelanguage.googleapis.com/v1beta/{$activeModel}:generateContent";

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$baseUrl}?key={$this->apiKey}", $payload);

            if ($response->failed()) {
                Log::error('Gemini API Error (' . $activeModel . ')', $response->json());

                // Eğer seçilen model hata verirse (örn: Overloaded), Fallback olarak flash-latest dene
                if ($activeModel !== 'models/gemini-1.5-flash') {
                    Log::warning('Ana model başarısız, fallback model deneniyor...');
                    $fallbackUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent";
                    $response = Http::withHeaders(['Content-Type' => 'application/json'])
                        ->post("{$fallbackUrl}?key={$this->apiKey}", $payload);

                    if ($response->failed()) {
                        return ['error' => 'Yapay zeka servisi şu an yoğun, lütfen birazdan tekrar deneyin.'];
                    }
                } else {
                    return ['error' => 'Yapay zeka servisine ulaşılamadı. (Quota/Error)'];
                }
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return ['error' => 'Bir hata oluştu: ' . $e->getMessage()];
        }
    }

    /**
     * Google API'den kullanılabilir modelleri sorgular veya cache'den getirir.
     */
    private function discoverModel()
    {
        // Her istekte sormamak için basit bir Cache mekanizması (Session veya File Cache kullanılabilir)
        // Şimdilik performansı etkilememesi için config veya default ile başlayalım, 
        // ama kullanıcı dinamik istediği için sorgu atabiliriz.

        try {
            $listUrl = "https://generativelanguage.googleapis.com/v1beta/models?key={$this->apiKey}";
            // Timeout'u kısa tutalım ki sistemi yavaşlatmasın
            $modelsResponse = Http::timeout(2)
                ->withoutVerifying() // Geliştirme ortamı için
                ->get($listUrl);

            if ($modelsResponse->successful()) {
                $availableModels = $modelsResponse->json('models');

                // Öncelik Sırası: 1.5 Flash -> 1.5 Pro -> Flash Latest
                $preferredOrder = [
                    'gemini-1.5-flash',
                    'gemini-1.5-pro',
                    'gemini-1.0-pro',
                    'gemini-pro'
                ];

                foreach ($preferredOrder as $pref) {
                    foreach ($availableModels as $m) {
                        // Model ismi 'models/gemini-1.5-flash-001' gibi gelebilir, contains ile bakalım
                        if (str_contains($m['name'], $pref)) {
                            // generateContent metodunu destekliyor mu?
                            if (in_array('generateContent', $m['supportedGenerationMethods'] ?? [])) {
                                return $m['name'];
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Gemini Model Discovery Failed: ' . $e->getMessage());
        }

        // Fallback Model
        return 'models/gemini-1.5-flash';
    }

    private function formatToolsForGemini($tools)
    {
        // AiTools sınıfındaki tanımları Gemini formatına çevirir
        $declarations = [];
        foreach ($tools as $name => $tool) {
            $declarations[] = [
                'name' => $name,
                'description' => $tool['description'],
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => !empty($tool['parameters']) ? $tool['parameters'] : (object) [],
                    'required' => $tool['required'] ?? []
                ]
            ];
        }
        return $declarations;
    }
}
