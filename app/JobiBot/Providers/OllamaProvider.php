<?php

namespace App\JobiBot\Providers;

use App\JobiBot\Exceptions\LaiException;
use App\JobiBot\LaiProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaProvider implements LaiProviderInterface
{
    public function __construct(
        private readonly string $baseUrl = 'http://localhost:11434',
        private readonly string $defaultModel = 'gemma3',
    ) {}

    public function chat(array $messages, array $options = []): array
    {
        $payload = [
            'model'    => $options['model'] ?? $this->defaultModel,
            'messages' => $messages,
            'stream'   => false,
        ];

        if (isset($options['temperature'])) {
            $payload['options']['temperature'] = $options['temperature'];
        }

        $response = Http::timeout(120)
            ->post("{$this->baseUrl}/api/chat", $payload);

        if ($response->failed()) {
            Log::error('OllamaProvider error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new LaiException(
                'Ollama request failed: ' . $response->body(),
                $response->status()
            );
        }

        $data = $response->json();

        return [
            'content' => $data['message']['content'],
            'usage'   => [
                'total_tokens'  => $data['eval_count'] ?? 0,
                'prompt_tokens' => $data['prompt_eval_count'] ?? 0,
            ],
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
            $response = Http::timeout(5)->get("{$this->baseUrl}/api/tags");

            return $response->successful();
        } catch (\Throwable) {
            return false;
        }
    }
}