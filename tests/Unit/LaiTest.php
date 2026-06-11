<?php

namespace Tests\Unit;

use App\JobiBot\Lai;
use App\JobiBot\LaiProviderInterface;
use App\JobiBot\Providers\OpenAIProvider;
use App\JobiBot\Providers\OllamaProvider;
use App\JobiBot\Providers\PrivateAIProvider;
use Tests\TestCase;

class LaiTest extends TestCase
{
    public function test_provider_interface_can_be_resolved(): void
    {
        $this->assertInstanceOf(LaiProviderInterface::class, Lai::provider());
    }

    public function test_provider_returns_model_from_config(): void
    {
        $this->assertIsString(Lai::model());
        $this->assertNotEmpty(Lai::model());
    }

    public function test_provider_returns_temperature_from_config(): void
    {
        $this->assertIsFloat(Lai::temperature());
        $this->assertGreaterThanOrEqual(0, Lai::temperature());
        $this->assertLessThanOrEqual(2, Lai::temperature());
    }

    public function test_recruiter_system_messages_are_valid(): void
    {
        $messages = Lai::getRecruiterSysMessages();

        $this->assertIsArray($messages);
        $this->assertCount(2, $messages);
        $this->assertEquals('system', $messages[0]['role']);
        $this->assertNotEmpty($messages[0]['content']);
    }

    public function test_ollama_provider_can_be_instantiated(): void
    {
        $provider = new OllamaProvider(
            baseUrl: 'http://localhost:11434',
            defaultModel: 'gemma3',
        );

        $this->assertInstanceOf(LaiProviderInterface::class, $provider);
    }

    public function test_openai_provider_can_be_instantiated(): void
    {
        $provider = new OpenAIProvider(
            apiKey: 'test-key',
            baseUrl: 'https://api.openai.com/v1',
            defaultModel: 'gpt-4o',
        );

        $this->assertInstanceOf(LaiProviderInterface::class, $provider);
    }

    public function test_privateai_provider_can_be_instantiated(): void
    {
        $provider = new PrivateAIProvider(
            baseUrl: 'http://localhost:8005',
            defaultModel: 'qwen3-7b',
            apiKey: null,
        );

        $this->assertInstanceOf(LaiProviderInterface::class, $provider);
    }

    public function test_moderate_returns_false_for_non_openai_provider(): void
    {
        config(['jobibot.provider' => 'ollama']);
        $this->assertFalse(Lai::moderate('test content'));
    }
}