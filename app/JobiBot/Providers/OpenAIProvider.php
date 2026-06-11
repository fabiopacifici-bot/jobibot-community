<?php

namespace App\JobiBot\Providers;

use App\JobiBot\Exceptions\LaiException;
use App\JobiBot\LaiProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIProvider implements LaiProviderInterface
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl = 'https://api.openai.com/v1',
        private readonly string $defaultModel = 'gpt-4o',
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

        $response = Http::withToken($this->apiKey)
            ->timeout(120)
            ->post("{$this->baseUrl}/chat/completions", $payload);

        if ($response->failed()) {
            Log::error('OpenAIProvider error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new LaiException(
                $response->json('error.message') ?? 'OpenAI request failed',
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
            $response = Http::withToken($this->apiKey)
                ->timeout(5)
                ->get("{$this->baseUrl}/models");

            return $response->successful();
        } catch (\Throwable) {
            return false;
        }
    }
}