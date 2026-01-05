@if($images->isEmpty())
    <div class="flex flex-col items-center justify-center h-[400px] text-gray-400 dark:text-gray-500">
        <div class="p-4 rounded-full bg-gray-50 dark:bg-gray-800/50 mb-4">
            <x-heroicon-o-photo class="w-16 h-16 opacity-20" />
        </div>
        <h3 class="text-lg font-medium">No images found</h3>
        <p class="text-sm">This AVN doesn't have any gallery images uploaded yet.</p>
    </div>
@else
    <div x-data="{ 
        active: 0, 
        images: {{ $images->map(fn($img) => Storage::disk('public')->url($img->image_url))->toJson() }} 
    }" class="relative py-8">
        <div class="flex items-center justify-center overflow-hidden relative h-[450px]">
            @foreach($images as $index => $image)
                <div 
                    x-show="Math.abs(active - {{ $index }}) <= 2"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    class="absolute transition-all duration-500 ease-in-out cursor-pointer"
                    :class="{
                        'z-30 scale-110 shadow-2xl border-4 border-white dark:border-gray-800 rounded-xl': active === {{ $index }},
                        'z-20 opacity-60 translate-x-[-70%] scale-90 blur-[1px]': active === {{ $index }} + 1,
                        'z-20 opacity-60 translate-x-[70%] scale-90 blur-[1px]': active === {{ $index }} - 1,
                        'z-10 opacity-30 translate-x-[-120%] scale-75 blur-[2px]': active === {{ $index }} + 2,
                        'z-10 opacity-30 translate-x-[120%] scale-75 blur-[2px]': active === {{ $index }} - 2,
                        'hidden': Math.abs(active - {{ $index }}) > 2
                    }"
                    @click="active = {{ $index }}"
                >
                    <img src="{{ Storage::disk('public')->url($image->image_url) }}" class="w-[700px] md:w-[850px] h-[400px] object-cover rounded-xl shadow-lg border border-white/10">
                </div>
            @endforeach
        </div>

        <div class="flex justify-center items-center gap-4 mt-8">
            <button type="button" @click="active = active > 0 ? active - 1 : images.length - 1" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
                <x-heroicon-m-chevron-left class="w-6 h-6"/>
            </button>

            <div class="flex gap-2">
                @foreach($images as $index => $image)
                    <button @click="active = {{ $index }}" 
                        :class="active === {{ $index }} ? 'bg-primary-500 w-4' : 'bg-gray-300 dark:bg-gray-600 w-2'"
                        class="h-2 rounded-full transition-all duration-300"></button>
                @endforeach
            </div>

            <button type="button" @click="active = active < images.length - 1 ? active + 1 : 0" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
                <x-heroicon-m-chevron-right class="w-6 h-6"/>
            </button>
        </div>
    </div>
@endif
