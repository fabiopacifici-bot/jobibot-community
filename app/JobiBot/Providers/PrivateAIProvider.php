<?php

namespace App\JobiBot\Providers;

use App\JobiBot\Exceptions\LaiException;
use App\JobiBot\LaiProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Private AI Server provider — connects to a local OpenAI-compatible endpoint.
 * Defaults to localhost:8005 (private-ai-server, vLLM-based).
 */
class PrivateAIProvider implements LaiProviderInterface
{
    public function __construct(
        private readonly string $baseUrl = 'http://localhost:8005',
        private readonly string $defaultModel = 'qwen3-7b',
        private readonly ?string $apiKey = null,
    ) {}

    public function chat(array $messages, array $options = []): array
    {
        $payload = [
            'model'       => $options['model'] ?? $this->defaultModel,
            'temperature' => $options['temperature'] ?? 0.6,
            'messages'    => $messages,
        ];

        if (isset($options['max_tokens'])) {
            $payload['max_tokens'] = $options['max_tokens'];
        }

        $request = Http::timeout(120);

        if ($this->apiKey) {
            $request->withToken($this->apiKey);
        }

        $response = $request->post("{$this->baseUrl}/v1/chat/completions", $payload);

        if ($response->failed()) {
            Log::error('PrivateAIProvider error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new LaiException(
                'Private AI server request failed: ' . $response->body(),
                $response->status()
            );
        }

        $data = $response->json();

        return [
            'content' => $data['choices'][0]['message']['content'],
            'usage'   => $data['usage'] ?? null,
        ];
    }

    public function complete(string $prompt, array $options = []): string
    {
        $result = $this->chat(
            [['role' => 'user', 'content' => $prompt]],
            $options
        );

        return $result['content'];
    }

    public function health(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/health");

            return $response->successful();
        } catch (\Throwable) {
            return false;
        }
    }
}