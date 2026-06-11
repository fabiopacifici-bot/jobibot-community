<?php

use App\JobiBot\Lai;
use App\JobiBot\LaiProviderInterface;
use App\JobiBot\Providers\OllamaProvider;
use App\JobiBot\Providers\OpenAIProvider;
use App\JobiBot\Providers\PrivateAIProvider;

test('provider interface can be resolved', function () {
    expect(Lai::provider())->toBeInstanceOf(LaiProviderInterface::class);
});

test('provider returns model from config', function () {
    expect(Lai::model())->toBeString();
    expect(Lai::model())->not->toBeEmpty();
});

test('provider returns temperature from config', function () {
    expect(Lai::temperature())->toBeFloat();
    expect(Lai::temperature())->toBeGreaterThanOrEqual(0);
    expect(Lai::temperature())->toBeLessThanOrEqual(2);
});

test('recruiter system messages are valid', function () {
    $messages = Lai::getRecruiterSysMessages();

    expect($messages)->toBeArray();
    expect($messages)->toHaveCount(2);
    expect($messages[0]['role'])->toBe('system');
    expect($messages[0]['content'])->not->toBeEmpty();
});

test('ollama provider can be instantiated', function () {
    $provider = new OllamaProvider(
        baseUrl: 'http://localhost:11434',
        defaultModel: 'gemma3',
    );

    expect($provider)->toBeInstanceOf(LaiProviderInterface::class);
});

test('openai provider can be instantiated', function () {
    $provider = new OpenAIProvider(
        apiKey: 'test-key',
        baseUrl: 'https://api.openai.com/v1',
        defaultModel: 'gpt-4o',
    );

    expect($provider)->toBeInstanceOf(LaiProviderInterface::class);
});

test('privateai provider can be instantiated', function () {
    $provider = new PrivateAIProvider(
        baseUrl: 'http://localhost:8005',
        defaultModel: 'qwen3-7b',
        apiKey: null,
    );

    expect($provider)->toBeInstanceOf(LaiProviderInterface::class);
});

test('moderate returns false for non-openai provider', function () {
    config(['jobibot.provider' => 'ollama']);
    expect(Lai::moderate('test content'))->toBeFalse();
});
