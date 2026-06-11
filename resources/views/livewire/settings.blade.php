<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">⚙️ AI Provider Settings</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Configure how JobiBot connects to AI models. Choose a provider, enter your credentials, and test the connection.</p>
    </div>

    <div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-200 dark:border-gray-800 p-6 space-y-5 shadow-sm">
        {{-- Provider Selector — improved grid: 2 cols on mobile, 4 on desktop --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">AI Provider</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                {{-- OpenAI --}}
                <label wire:click="$set('provider', 'openai')"
                       class="flex flex-col items-center gap-1.5 p-3 border rounded-xl cursor-pointer transition-all
                              {{ $provider === 'openai' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 ring-2 ring-indigo-200 dark:ring-indigo-800 shadow-sm' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-sm' }}">
                    <span class="text-2xl">🧠</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">OpenAI</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">Cloud</span>
                </label>

                {{-- OpenRouter — NEW --}}
                <label wire:click="$set('provider', 'openrouter')"
                       class="flex flex-col items-center gap-1.5 p-3 border rounded-xl cursor-pointer transition-all
                              {{ $provider === 'openrouter' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 ring-2 ring-indigo-200 dark:ring-indigo-800 shadow-sm' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-sm' }}">
                    <span class="text-2xl">🌐</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">OpenRouter</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">Cloud · Multi-model</span>
                </label>

                {{-- Ollama --}}
                <label wire:click="$set('provider', 'ollama')"
                       class="flex flex-col items-center gap-1.5 p-3 border rounded-xl cursor-pointer transition-all
                              {{ $provider === 'ollama' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 ring-2 ring-indigo-200 dark:ring-indigo-800 shadow-sm' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-sm' }}">
                    <span class="text-2xl">🦙</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Ollama</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">Local</span>
                </label>

                {{-- Private AI --}}
                <label wire:click="$set('provider', 'privateai')"
                       class="flex flex-col items-center gap-1.5 p-3 border rounded-xl cursor-pointer transition-all
                              {{ $provider === 'privateai' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 ring-2 ring-indigo-200 dark:ring-indigo-800 shadow-sm' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-sm' }}">
                    <span class="text-2xl">🔒</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Private AI</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">Local vLLM</span>
                </label>
            </div>

            {{-- Provider description --}}
            <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                @if($provider === 'openai')
                    Direct connection to OpenAI's API. Best performance, requires API key.
                @elseif($provider === 'openrouter')
                    Access 200+ models from OpenAI, Anthropic, Google, Meta & more through a single API.
                @elseif($provider === 'ollama')
                    Run models locally with Ollama. Free, private, no API key needed.
                @elseif($provider === 'privateai')
                    Self-hosted vLLM-compatible server. Full control, zero external dependencies.
                @endif
            </p>
        </div>

        {{-- API Key (OpenAI & OpenRouter) --}}
        @if(in_array($provider, ['openai', 'openrouter']))
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    API Key
                    @if($provider === 'openrouter')
                        <span class="text-xs text-gray-400 dark:text-gray-500 font-normal">— Get yours at <a href="https://openrouter.ai/keys" target="_blank" class="underline">openrouter.ai/keys</a></span>
                    @endif
                </label>
                <input type="password" wire:model="apiKey"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500"
                       placeholder="{{ $provider === 'openrouter' ? 'sk-or-v1-...' : 'sk-...' }}">
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Your key is stored locally and never sent to our servers.</p>
            </div>
        @endif

        {{-- Base URL --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                @if(in_array($provider, ['openai', 'openrouter']))
                    API Base URL
                @else
                    Server URL
                @endif
            </label>
            <input type="text" wire:model="baseUrl"
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500"
                   placeholder="http://localhost:11434">
        </div>

        {{-- Model --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Model</label>
            <input type="text" wire:model="model"
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500"
                   placeholder="gpt-4o">
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                @if($provider === 'openai')
                    e.g. gpt-4o, gpt-4o-mini, gpt-3.5-turbo
                @elseif($provider === 'openrouter')
                    e.g. openai/gpt-4o, anthropic/claude-3.5-sonnet, google/gemini-2.5-pro, meta-llama/llama-4-maverick
                @elseif($provider === 'ollama')
                    e.g. gemma3, qwen3, llama3, mistral
                @else
                    e.g. qwen3-7b, gemma-3-12b
                @endif
            </p>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 pt-2">
            <button wire:click="testConnection"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                🔌 Test Connection
            </button>
            <button wire:click="save"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                💾 Save Settings
            </button>
        </div>

        {{-- Health Check Result --}}
        @if($healthMessage)
            <div class="p-3 rounded-lg text-sm {{ $providerHealthy ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800' }}">
                {{ $healthMessage }}
            </div>
        @endif
    </div>
</div>