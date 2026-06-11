<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h2 class="text-xl font-bold">⚙️ AI Provider Settings</h2>
        <p class="text-sm text-gray-500">Configure how JobiBot connects to AI models. Use your own API key or a local model.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6 space-y-5">
        {{-- Provider Selector --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">AI Provider</label>
            <div class="grid grid-cols-3 gap-3">
                <label wire:click="$set('provider', 'openai')"
                       class="flex flex-col items-center gap-1 p-3 border rounded-lg cursor-pointer transition
                              {{ $provider === 'openai' ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200' : 'border-gray-200 hover:border-gray-300' }}">
                    <span class="text-2xl">🧠</span>
                    <span class="text-sm font-medium">OpenAI</span>
                    <span class="text-xs text-gray-400">Cloud</span>
                </label>
                <label wire:click="$set('provider', 'ollama')"
                       class="flex flex-col items-center gap-1 p-3 border rounded-lg cursor-pointer transition
                              {{ $provider === 'ollama' ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200' : 'border-gray-200 hover:border-gray-300' }}">
                    <span class="text-2xl">🦙</span>
                    <span class="text-sm font-medium">Ollama</span>
                    <span class="text-xs text-gray-400">Local</span>
                </label>
                <label wire:click="$set('provider', 'privateai')"
                       class="flex flex-col items-center gap-1 p-3 border rounded-lg cursor-pointer transition
                              {{ $provider === 'privateai' ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200' : 'border-gray-200 hover:border-gray-300' }}">
                    <span class="text-2xl">🔒</span>
                    <span class="text-sm font-medium">Private AI</span>
                    <span class="text-xs text-gray-400">Local vLLM</span>
                </label>
            </div>
        </div>

        {{-- API Key (OpenAI only) --}}
        @if($provider === 'openai')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                <input type="password" wire:model="apiKey"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                       placeholder="sk-...">
                <p class="text-xs text-gray-400 mt-1">Your key is stored locally and never sent to our servers.</p>
            </div>
        @endif

        {{-- Base URL --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ $provider === 'openai' ? 'API Base URL' : 'Server URL' }}
            </label>
            <input type="text" wire:model="baseUrl"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                   placeholder="http://localhost:11434">
        </div>

        {{-- Model --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
            <input type="text" wire:model="model"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                   placeholder="gpt-4o">
            <p class="text-xs text-gray-400 mt-1">
                @if($provider === 'openai')
                    e.g. gpt-4o, gpt-4o-mini, gpt-3.5-turbo
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
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition">
                🔌 Test Connection
            </button>
            <button wire:click="save"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                💾 Save Settings
            </button>
        </div>

        {{-- Health Check Result --}}
        @if($healthMessage)
            <div class="p-3 rounded-lg text-sm {{ $providerHealthy ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                {{ $healthMessage }}
            </div>
        @endif
    </div>
</div>