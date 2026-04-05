<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DiditService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.didit.base_url');
        $this->apiKey  = config('services.didit.api_key');
    }

   public function createSession($user)
{
    $response = Http::withBasicAuth($this->apiKey, '') // 👈 IMPORTANT
        ->acceptJson()
        ->post($this->baseUrl . '/v1/session', [
            'external_id' => (string) $user->id,
            'callback_url' => route('didit.webhook'),
        ]);

    \Log::info('DIDIT DEBUG', [
        'status' => $response->status(),
        'body' => $response->body(),
    ]);

    return $response->json();
}
    // public function createSession($user)
    // {
    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer ' . $this->apiKey
    //     ])->post($this->baseUrl . '/v1/session', [
    //         'external_id' => (string) $user->id,
    //         'callback_url' => route('didit.webhook'),
    //     ]);

    //     return $response->json();
    // }
}