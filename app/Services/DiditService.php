<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DiditService
{
    protected $baseUrl;
    protected $apiKey;
    protected $workflow_id;

    public function __construct()
    {
        $this->baseUrl = config('services.didit.base_url');
        $this->apiKey  = config('services.didit.api_key');
        $this->workflow_id  = config('services.didit.workflow_id');
    }

    // 7-4-26

  public function createSession($user)
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
        ])
        ->acceptJson()
        ->post($this->baseUrl . '/v3/session/', [
            'workflow_id' => $this->workflow_id,
            'callback' => route('didit.webhook'),
            'vendor_data' => (string) $user->id,
        ]);

        \Log::info('DIDIT DEBUG', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return $response->json();
    }
   
}