<div>
    <div class="mb-4">
        <h2 class="text-xl font-bold">🎯 Interview Simulation</h2>
        <p class="text-sm text-gray-500">Practice job interviews with AI. Your responses are analyzed in real-time.</p>
    </div>

    {{-- Start Screen --}}
    @if(!$simulationStarted)
        <div class="bg-white rounded-xl shadow-sm border p-6 max-w-2xl mx-auto">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                <input type="text" wire:model="fullname"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                       placeholder="Enter your full name">
                @error('fullname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Job Description</label>
                <textarea wire:model="job" rows="5"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                          placeholder="Paste a job description here to simulate an interview for that role..."></textarea>
                @error('job') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            @if($cvMatchScore)
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm">
                    📊 CV Match Score: <strong>{{ $cvMatchScore }}%</strong>
                </div>
            @endif

            <button wire:click="startSimulation"
                    class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium hover:bg-indigo-700 transition">
                🚀 Start Interview Simulation
            </button>
        </div>
    @endif

    {{-- Simulation Active --}}
    @if($simulationStarted)
        <div class="max-w-3xl mx-auto">
            {{-- Score Results --}}
            @if($simulationScore)
                <div class="mb-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white text-center">
                    <div class="text-4xl font-bold mb-2">{{ $simulationScore }}%</div>
                    <div class="text-sm opacity-90">{{ $considerations }}</div>
                </div>
                <button wire:click="newSimulation" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium hover:bg-indigo-700 mb-6">
                    🔄 Start New Simulation
                </button>
            @endif

            {{-- Conversation --}}
            <div x-data x-init="$watch('$wire.conversation.length', () => { setTimeout(() => { const el = $el.querySelector('#conversation-end'); el?.scrollIntoView({ behavior: 'smooth' }); }, 50) })"
                 class="bg-white rounded-xl shadow-sm border">
                <div class="p-4 border-b bg-gray-50 rounded-t-xl flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Interview in progress...</span>
                    @if(!$simulationScore)
                        <button wire:click="endSimulation" class="text-xs text-red-600 hover:underline">
                            ⏹ End Simulation
                        </button>
                    @endif
                </div>
                <div class="p-4 space-y-4 max-h-[500px] overflow-y-auto" id="conversation-container">
                    @foreach($conversation as $i => $msg)
                        <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] rounded-xl px-4 py-2 text-sm
                                {{ $msg['role'] === 'user'
                                    ? 'bg-indigo-600 text-white rounded-br-sm'
                                    : 'bg-gray-100 text-gray-800 rounded-bl-sm' }}"
                                 id="simulation-message-{{ $i }}">
                                {!! nl2br(e($msg['content'])) !!}
                            </div>
                        </div>
                    @endforeach
                    <div id="conversation-end"></div>

                    @if($loading)
                        <div class="flex justify-start">
                            <div class="bg-gray-100 rounded-xl px-4 py-3 text-sm text-gray-500">
                                <div class="flex gap-1">
                                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0s"></span>
                                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.2s"></span>
                                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.4s"></span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Input Area --}}
                @if(!$simulationScore && $simulationStarted)
                    <div class="p-4 border-t">
                        <div class="flex gap-2">
                            <textarea wire:model="prompt" rows="2"
                                      wire:keydown.enter.prevent="submitAnswer"
                                      class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 resize-none text-sm"
                                      placeholder="Type your answer... (Enter to send)"></textarea>
                            <button wire:click="submitAnswer"
                                    wire:loading.attr="disabled"
                                    class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50 self-end">
                                Send
                            </button>
                        </div>
                        @error('prompt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>