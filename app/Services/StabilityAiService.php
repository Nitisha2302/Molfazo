<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use RuntimeException;

class StabilityAiService
{
    protected string $apiKey;
    protected string $host;

    public function __construct()
    {
        $this->apiKey = (string) config('services.stability.api_key');
        $this->host   = rtrim((string) config('services.stability.host', 'https://api.stability.ai'), '/');

        if ($this->apiKey === '') {
            throw new RuntimeException('STABILITY_API_KEY is not configured.');
        }
    }

    /**
     * MAIN FLOW: remove background -> place on white -> save.
     * Returns ONLY the file name.
     */
    public function enhanceProductPhoto(string $sourceAbsolutePath): string
    {
        $cutout = $this->removeBackground($sourceAbsolutePath);

        return $this->placeOnWhite($cutout, true, 8);
    }

    public function removeBackground(string $sourceAbsolutePath): string
    {
        if (! file_exists($sourceAbsolutePath)) {
            throw new RuntimeException('Source image not found.');
        }

        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept'        => 'image/*',
            ])
            ->timeout(120)
            ->attach('image', file_get_contents($sourceAbsolutePath), basename($sourceAbsolutePath))
            ->post($this->host . '/v2beta/stable-image/edit/remove-background', [
                ['name' => 'output_format', 'contents' => 'png'],
            ]);

        if ($response->failed()) {
            $this->throwApiError($response, 'remove-background');
        }

        return $response->body();
    }

    public function placeOnWhite(string $pngBytes, bool $square = true, int $padding = 8): string
    {
        $manager = new ImageManager(new Driver());
        $cutout  = $manager->read($pngBytes);

        $w = $cutout->width();
        $h = $cutout->height();

        $padding = max(0, min(25, $padding));

        if ($square) {
            $side = (int) round(max($w, $h) * (1 + $padding / 100));
            $canvasW = $canvasH = $side;
        } else {
            $canvasW = (int) round($w * (1 + $padding / 100));
            $canvasH = (int) round($h * (1 + $padding / 100));
        }

        $canvas = $manager->create($canvasW, $canvasH)->fill('ffffff');
        $canvas->place($cutout, 'center');

        return $this->saveToProductImages((string) $canvas->toPng());
    }

    /**
     * Text to image. Returns ONLY the file name.
     */
    public function generate(string $prompt): string
    {
        $form = [
            ['name' => 'prompt',          'contents' => $this->asProductCardPrompt($prompt)],
            ['name' => 'negative_prompt', 'contents' => self::NEGATIVE_PROMPT],
            ['name' => 'output_format',   'contents' => 'png'],
            ['name' => 'aspect_ratio',    'contents' => '1:1'],
        ];

        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept'        => 'image/*',
            ])
            ->timeout(120)
            ->asMultipart()
            ->post($this->host . '/v2beta/stable-image/generate/core', $form);

        if ($response->failed()) {
            $this->throwApiError($response, 'generate');
        }

        return $this->saveToProductImages($response->body());
    }

    public const NEGATIVE_PROMPT = 'blurry, low quality, distorted, text, watermark, logo, '
        . 'multiple products, cluttered background, people, hands';

    protected function asProductCardPrompt(string $prompt): string
    {
        $p = strtolower($prompt);

        $mentionsSetting = str_contains($p, 'background')
            || str_contains($p, 'studio')
            || str_contains($p, 'table')
            || str_contains($p, 'surface')
            || str_contains($p, 'backdrop');

        if ($mentionsSetting) {
            return $prompt . ', professional product photography, sharp focus, high resolution';
        }

        return $prompt . ', on a clean white studio background, professional e-commerce '
             . 'product photography, soft even lighting, sharp focus, centered, high resolution';
    }

    public static function defaultProductPrompt(?string $productName = null): string
    {
        $name = $productName ?: 'the product';

        return "Clean white studio background, professional e-commerce product "
             . "photograph of {$name}, soft even lighting, sharp focus, "
             . "centered composition, no text, no watermark";
    }

    /**
     * Save PNG bytes into public/assets/product_images
     * Returns ONLY the file name, e.g. "1758012345_ai_9f2c4a1b.png"
     */
    protected function saveToProductImages(string $bytes): string
    {
        $dir = public_path('assets/product_images');

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = time() . '_ai_' . Str::random(12) . '.png';

        file_put_contents($dir . DIRECTORY_SEPARATOR . $filename, $bytes);

        return $filename;
    }

    protected function throwApiError($response, string $operation): void
    {
        $body = $response->body();

        Log::error('Stability AI request failed', [
            'operation' => $operation,
            'status'    => $response->status(),
            'body'      => Str::limit($body, 500),
        ]);

        $message = null;

        if (Str::startsWith(ltrim($body), '{')) {
            $json = json_decode($body, true);
            $message = $json['errors'][0] ?? $json['message'] ?? $json['name'] ?? null;
        }

        if ($response->status() === 401) {
            $message = 'Stability API key is invalid.';
        } elseif ($response->status() === 402) {
            $message = 'Stability account has no credits left.';
        } elseif ($response->status() === 413) {
            $message = 'Image is too large. Use a smaller photo.';
        } elseif ($response->status() === 429) {
            $message = 'Too many requests. Please wait a moment.';
        }

        throw new RuntimeException($message ?? ('Image processing failed (' . $response->status() . ')'));
    }
}