<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'jobibot:install';

    protected $description = 'Install JobiBot Community Edition — run migrations and seed defaults';

    public function handle(): int
    {
        $this->info('🚀 Installing JobiBot Community Edition...');

        $this->call('migrate', ['--force' => true]);

        $this->info('✅ JobiBot Community Edition installed successfully.');
        $this->info('');
        $this->info('Next steps:');
        $this->info('  1. Set your AI provider in .env: JOBIBOT_AI_PROVIDER=openai|ollama|privateai');
        $this->info('  2. Set your API key if using OpenAI: OPENAI_API_KEY=sk-...');
        $this->info('  3. Start Laravel: php artisan serve');
        $this->info('  4. Open the app and upload your CV!');

        return self::SUCCESS;
    }
}
