@if($image)
    <div class="flex justify-center items-center w-full py-4">
        <img 
            src="{{ asset('storage/' . $image) }}" 
            class="max-w-full h-auto rounded-xl shadow-md border dark:border-white/10"
            style="max-height: 60vh; margin: auto;"
        />
    </div>
@else
    <div class="p-8 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl w-full text-center">
        <x-heroicon-o-photo class="w-12 h-12 text-gray-400 mx-auto mb-2" />
        <p class="text-sm text-gray-500">No image found for this episode</p>
    </div>
@endif
