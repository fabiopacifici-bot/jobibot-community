<div class="bg-white rounded-xl shadow-sm border p-6 max-w-2xl mx-auto">
    <h2 class="text-xl font-bold mb-4">📄 Upload Your CV</h2>

    @if($candidate && $candidate->bio)
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="text-sm font-medium text-green-800 mb-1">✅ CV Processed</div>
            <p class="text-sm text-green-700">{{ $candidate->bio }}</p>
            <button wire:click="removeCv" class="mt-2 text-xs text-red-600 hover:underline">Remove CV</button>
        </div>
    @else
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center"
             x-data="{ dragging: false }"
             x-on:dragover.prevent="dragging = true"
             x-on:dragleave.prevent="dragging = false"
             x-on:drop.prevent="dragging = false; $refs.input.files = $event.dataTransfer.files; $refs.input.dispatchEvent(new Event('change'))"
             :class="{ 'border-indigo-400 bg-indigo-50': dragging }">
            <div class="text-4xl mb-2">📁</div>
            <p class="text-gray-600 mb-2">Drag & drop your CV or click to browse</p>
            <p class="text-xs text-gray-400">PDF, DOC, DOCX, TXT — max {{ config('jobibot.cv_max_size_kb', 5120) / 1024 }}MB</p>
            <input type="file" wire:model="cv" x-ref="input" id="cv" class="hidden" accept=".pdf,.doc,.docx,.txt">
            <label for="cv" class="mt-3 inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm cursor-pointer hover:bg-indigo-700">
                Browse Files
            </label>
        </div>
    @endif

    @error('cv')
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
    @enderror

    <div wire:loading wire:target="cv" class="mt-4 text-center">
        <div class="animate-spin inline-block w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full"></div>
        <p class="text-sm text-gray-500 mt-2">Analyzing your CV with AI...</p>
    </div>
</div>