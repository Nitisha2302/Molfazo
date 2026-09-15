<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * PHASE 2 — Stability AI image generation.
 *
 * Included now so the credit flow has something to call, but the
 * purchase flow (phase 1) works completely without this.
 */
class StabilityAiService
{
    protected string $apiKey;
    protected string $host;
    protected string $engine;

    public function __construct()
    {
        $this->apiKey = (string) config('services.stability.api_key');
        $this->host   = rtrim((string) config('services.stability.host'), '/');
        $this->engine = (string) config('services.stability.engine');

        if ($this->apiKey === '') {
            throw new RuntimeException('STABILITY_API_KEY is not configured.');
        }
    }

    /**
     * Text → image.
     * Returns the stored public path of the generated file.
     */
    public function generate(string $prompt, ?string $negativePrompt = null, string $aspectRatio = '1:1'): string
    {
        $form = [
            ['name' => 'prompt',       'contents' => $prompt],
            ['name' => 'output_format','contents' => 'png'],
            ['name' => 'aspect_ratio', 'contents' => $aspectRatio],
            ['name' => 'model',        'contents' => $this->engine],
        ];

        if ($negativePrompt) {
            $form[] = ['name' => 'negative_prompt', 'contents' => $negativePrompt];
        }

        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept'        => 'image/*',
            ])
            ->timeout(120)
            ->asMultipart()
            ->post($this->host . '/v2beta/stable-image/generate/sd3', $form);

        return $this->storeResponse($response, 'generate');
    }

    /**
     * Image → image (the "edit" option: keep the product, change the scene).
     */
    public function editImage(string $sourceImageAbsolutePath, string $prompt, float $strength = 0.55): string
    {
        if (! file_exists($sourceImageAbsolutePath)) {
            throw new RuntimeException('Source image not found.');
        }

        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept'        => 'image/*',
            ])
            ->timeout(120)
            ->attach('image', file_get_contents($sourceImageAbsolutePath), basename($sourceImageAbsolutePath))
            ->asMultipart()
            ->post($this->host . '/v2beta/stable-image/generate/sd3', [
                ['name' => 'prompt',        'contents' => $prompt],
                ['name' => 'mode',          'contents' => 'image-to-image'],
                ['name' => 'strength',      'contents' => (string) $strength],
                ['name' => 'output_format', 'contents' => 'png'],
                ['name' => 'model',         'contents' => $this->engine],
            ]);

        return $this->storeResponse($response, 'edit');
    }

    protected function storeResponse($response, string $kind): string
    {
        if ($response->failed()) {
            $body = $response->body();

            Log::error('Stability AI request failed', [
                'kind'   => $kind,
                'status' => $response->status(),
                'body'   => Str::limit($body, 500),
            ]);

            throw new RuntimeException('Image generation failed: ' . Str::limit($body, 200));
        }

        $filename = 'ai_photos/' . date('Y/m') . '/' . Str::uuid() . '.png';

        Storage::disk('public')->put($filename, $response->body());

        return $filename;
    }

    /**
     * The default prompt the app pre-fills in "Edit" mode.
     */
    public static function defaultProductPrompt(?string $productName = null): string
    {
        $name = $productName ?: 'the product';

        return "Professional e-commerce product photograph of {$name}, "
             . "clean white studio background, soft even lighting, sharp focus, "
             . "high resolution, centered composition, no text, no watermark";
    }
}
