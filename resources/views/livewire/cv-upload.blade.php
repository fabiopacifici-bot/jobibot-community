<div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-200 dark:border-gray-800 p-6 max-w-2xl mx-auto shadow-sm">
    <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">📄 Upload Your CV</h2>

    @if(!$authenticated)
        <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <p class="text-sm text-blue-700 dark:text-blue-300">
                👋 <strong>Guest mode.</strong> Upload your CV for instant AI review — no account needed.
                <a href="{{ route('register') }}" class="underline font-medium">Register</a> to save your CV and access all features.
            </p>
        </div>
    @endif

    @if($candidate && $candidate->bio)
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="text-sm font-medium text-green-800 dark:text-green-300 mb-1">✅ CV Processed</div>
            <p class="text-sm text-green-700 dark:text-green-400">{{ $candidate->bio }}</p>
            <button wire:click="removeCv" class="mt-2 text-xs text-red-600 dark:text-red-400 hover:underline">Remove CV</button>
        </div>
    @else
        <div class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-8 text-center"
             x-data="{ dragging: false }"
             x-on:dragover.prevent="dragging = true"
             x-on:dragleave.prevent="dragging = false"
             x-on:drop.prevent="dragging = false; $refs.input.files = $event.dataTransfer.files; $refs.input.dispatchEvent(new Event('change'))"
             :class="{ 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20': dragging }">
            <div class="text-4xl mb-2">📁</div>
            <p class="text-gray-600 dark:text-gray-400 mb-2">Drag & drop your CV or click to browse</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">PDF, DOC, DOCX, TXT — max {{ config('jobibot.cv_max_size_kb', 5120) / 1024 }}MB</p>
            <input type="file" wire:model="cv" x-ref="input" id="cv" class="hidden" accept=".pdf,.doc,.docx,.txt">
            <label for="cv" class="mt-3 inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm cursor-pointer hover:bg-indigo-700 transition-colors">
                Browse Files
            </label>
        </div>
    @endif

    @error('cv')
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
    @enderror

    <div wire:loading wire:target="cv" class="mt-4 text-center">
        <div class="animate-spin inline-block w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full"></div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Analyzing your CV with AI...</p>
    </div>
</div>