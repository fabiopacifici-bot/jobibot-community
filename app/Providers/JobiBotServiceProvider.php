<?php

namespace App\Providers;

use App\Console\Commands\InstallCommand;
use App\JobiBot\LaiProviderInterface;
use App\JobiBot\Providers\OllamaProvider;
use App\JobiBot\Providers\OpenAIProvider;
use App\JobiBot\Providers\OpenRouterProvider;
use App\JobiBot\Providers\PrivateAIProvider;
use Illuminate\Support\ServiceProvider;

class JobiBotServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/jobibot.php',
            'jobibot'
        );

        $this->app->singleton(LaiProviderInterface::class, function () {
            $provider = config('jobibot.provider', 'openai');

            return match ($provider) {
                'ollama' => new OllamaProvider(
                    baseUrl: config('jobibot.providers.ollama.base_url'),
                    defaultModel: config('jobibot.providers.ollama.model'),
                ),
                'privateai' => new PrivateAIProvider(
                    baseUrl: config('jobibot.providers.privateai.base_url'),
                    defaultModel: config('jobibot.providers.privateai.model'),
                    apiKey: config('jobibot.providers.privateai.api_key'),
                ),
                'openrouter' => new OpenRouterProvider(
                    apiKey: config('jobibot.providers.openrouter.api_key'),
                    baseUrl: config('jobibot.providers.openrouter.base_url'),
                    defaultModel: config('jobibot.providers.openrouter.model'),
                ),
                default => new OpenAIProvider(
                    apiKey: config('jobibot.providers.openai.api_key'),
                    baseUrl: config('jobibot.providers.openai.base_url'),
                    defaultModel: config('jobibot.providers.openai.model'),
                ),
            };
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}
