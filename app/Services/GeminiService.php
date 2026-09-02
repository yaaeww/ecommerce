<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected ?string $apiKey; // Boleh null
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->model  = env('GEMINI_MODEL_NAME', 'gemini-pro');

        if (empty($this->apiKey)) {
            Log::error('❌ GEMINI_API_KEY tidak di-set di .env atau cache belum di-clear.');
            throw new \Exception('Kunci API Gemini tidak dikonfigurasi. Harap periksa file .env.');
        }

        // ✅ Semua model >= 1.5 (termasuk 2.0, 2.5, dst) pakai endpoint v1beta
        if (preg_match('/(1\.5|2\.[0-9]+)/', $this->model)) {
            $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
        } else {
            $this->baseUrl = "https://generativelanguage.googleapis.com/v1/models/{$this->model}:generateContent";
        }
    }

    /**
     * 🔹 Kirim prompt ke Gemini dan ambil respons AI
     */
    public function ask(string $prompt): string
    {
        try {
            $url = "{$this->baseUrl}?key={$this->apiKey}";

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->failed()) {
                Log::error('🚨 Gemini API Error: ' . $response->body());
                return '⚠️ AI gagal merespons. (Info: ' . $response->json('error.message', 'Request Gagal') . ')';
            }

            $data = $response->json();

            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                Log::error('Gemini API Error: No candidates found. ' . json_encode($data));
                return '⚠️ AI tidak dapat merespons (kemungkinan karena filter keamanan).';
            }

            return trim($data['candidates'][0]['content']['parts'][0]['text']);
        } catch (\Exception $e) {
            Log::error('💥 GeminiService Exception: ' . $e->getMessage());
            return '⚠️ Terjadi kesalahan internal: ' . $e->getMessage();
        }
    }

    /**
     * 🔸 Static helper — bisa dipanggil cepat dari controller
     */
    public static function askOnce(string $prompt): string
    {
        return (new static())->ask($prompt);
    }
}
