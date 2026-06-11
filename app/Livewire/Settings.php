<?php

namespace App\Livewire;

use App\JobiBot\Providers\OllamaProvider;
use App\JobiBot\Providers\OpenAIProvider;
use App\JobiBot\Providers\PrivateAIProvider;
use Livewire\Component;

class Settings extends Component
{
    public string $provider = 'openai';

    public string $model = 'gpt-4o';

    public string $apiKey = '';

    public string $baseUrl = '';

    public bool $providerHealthy = false;

    public string $healthMessage = '';

    public function mount(): void
    {
        $this->provider = config('jobibot.provider', 'openai');
        $this->model = config('jobibot.model', 'gpt-4o');

        if ($this->provider === 'openai') {
            $this->apiKey = config('jobibot.providers.openai.api_key', '');
            $this->baseUrl = config('jobibot.providers.openai.base_url', 'https://api.openai.com/v1');
        } elseif ($this->provider === 'ollama') {
            $this->baseUrl = config('jobibot.providers.ollama.base_url', 'http://localhost:11434');
            $this->model = config('jobibot.providers.ollama.model', 'gemma3');
        } elseif ($this->provider === 'privateai') {
            $this->baseUrl = config('jobibot.providers.privateai.base_url', 'http://localhost:8005');
            $this->model = config('jobibot.providers.privateai.model', 'qwen3-7b');
        }
    }

    public function updatedProvider(string $value): void
    {
        // Set sensible defaults when switching providers
        if ($value === 'ollama') {
            $this->baseUrl = 'http://localhost:11434';
            $this->model = 'gemma3';
        } elseif ($value === 'privateai') {
            $this->baseUrl = 'http://localhost:8005';
            $this->model = 'qwen3-7b';
        } else {
            $this->baseUrl = 'https://api.openai.com/v1';
            $this->model = 'gpt-4o';
        }
    }

    public function save(): void
    {
        $this->updateEnvFile('JOBIBOT_AI_PROVIDER', $this->provider);
        $this->updateEnvFile('JOBIBOT_AI_MODEL', $this->model);

        if ($this->provider === 'openai') {
            $this->updateEnvFile('OPENAI_API_KEY', $this->apiKey);
            $this->updateEnvFile('OPENAI_BASE_URL', $this->baseUrl);
        } elseif ($this->provider === 'ollama') {
            $this->updateEnvFile('OLLAMA_BASE_URL', $this->baseUrl);
        } elseif ($this->provider === 'privateai') {
            $this->updateEnvFile('PRIVATEAI_BASE_URL', $this->baseUrl);
        }

        // Refresh config
        config([
            'jobibot.provider' => $this->provider,
            'jobibot.model' => $this->model,
            'jobibot.providers.openai.api_key' => $this->apiKey,
            'jobibot.providers.openai.base_url' => $this->baseUrl,
            'jobibot.providers.ollama.base_url' => $this->baseUrl,
            'jobibot.providers.privateai.base_url' => $this->baseUrl,
        ]);

        session()->flash('message', '✅ Settings saved!');
    }

    public function testConnection(): void
    {
        $provider = match ($this->provider) {
            'ollama' => new OllamaProvider(baseUrl: $this->baseUrl, defaultModel: $this->model),
            'privateai' => new PrivateAIProvider(baseUrl: $this->baseUrl, defaultModel: $this->model),
            default => new OpenAIProvider(apiKey: $this->apiKey, baseUrl: $this->baseUrl, defaultModel: $this->model),
        };

        $this->providerHealthy = $provider->health();

        $this->healthMessage = $this->providerHealthy
            ? '✅ Connected! Provider is healthy.'
            : '❌ Could not reach the provider. Check your URL and credentials.';
    }

    protected function updateEnvFile(string $key, string $value): void
    {
        $envPath = base_path('.env');
        $content = file_exists($envPath) ? file_get_contents($envPath) : '';

        $pattern = "/^{$key}=.*$/m";

        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, "{$key}={$value}", $content);
        } else {
            $content .= "\n{$key}={$value}";
        }

        file_put_contents($envPath, $content);
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
