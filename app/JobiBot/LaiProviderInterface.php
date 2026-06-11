<?php

namespace App\JobiBot;

interface LaiProviderInterface
{
    /**
     * Send a chat completion request.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array{model?: string, temperature?: float, max_tokens?: int}  $options
     * @return array{content: string, usage?: array{total_tokens: int}}
     *
     * @throws \App\JobiBot\Exceptions\LaiException
     */
    public function chat(array $messages, array $options = []): array;

    /**
     * Send a text completion request (simpler interface).
     *
     * @param  array{model?: string, temperature?: float}  $options
     * @return string
     */
    public function complete(string $prompt, array $options = []): string;

    /**
     * Check if the provider is healthy/reachable.
     */
    public function health(): bool;
}