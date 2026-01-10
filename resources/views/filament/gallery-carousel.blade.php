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
                        'z-30 scale-110 border border-white dark:border-gray-800 rounded-xl': active === {{ $index }},
                        'z-20 opacity-60 translate-x-[-70%] scale-90 blur-[1px]': active === {{ $index }} + 1,
                        'z-20 opacity-60 translate-x-[70%] scale-90 blur-[1px]': active === {{ $index }} - 1,
                        'z-10 opacity-30 translate-x-[-120%] scale-75 blur-[2px]': active === {{ $index }} + 2,
                        'z-10 opacity-30 translate-x-[120%] scale-75 blur-[2px]': active === {{ $index }} - 2,
                        'hidden': Math.abs(active - {{ $index }}) > 2
                    }"
                    @click="active = {{ $index }}"
                >
                    <div class="relative group overflow-hidden rounded-xl">
                        <img src="{{ Storage::disk('public')->url($image->image_url) }}" 
                             class="w-[700px] md:w-[850px] h-[400px] object-cover border-2 border-white/10">
                        
                        @if($image->description)
                            <div 
                                x-show="active === {{ $index }}"
                                x-transition:enter="transition ease-out delay-300 duration-500"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/80 via-black/40 to-transparent text-white"
                            >
                                <p class="text-sm md:text-base font-medium leading-relaxed drop-shadow-md line-clamp-2 max-w-[90%]">
                                    {{ $image->description }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-center items-center gap-4 mt-2 relative z-40">
            <button type="button" 
                @click.stop="active = active > 0 ? active - 1 : images.length - 1" 
                class="p-2 rounded-full bg-gray-200/50 hover:bg-gray-300/50 dark:bg-white/5 dark:hover:bg-white/10 transition-colors"
            >
                <x-heroicon-m-chevron-left class="w-6 h-6 text-gray-700 dark:text-gray-300"/>
            </button>

            <div class="flex gap-2 px-3 py-2 bg-gray-200/30 dark:bg-white/5 rounded-full">
                @foreach($images as $index => $image)
                    <button type="button" @click.stop="active = {{ $index }}" 
                        :class="active === {{ $index }} ? 'bg-primary-500 w-4' : 'bg-gray-400 dark:bg-gray-600 w-2'"
                        class="h-2 rounded-full transition-all duration-300"></button>
                @endforeach
            </div>

            <button type="button" 
                @click.stop="active = active < images.length - 1 ? active + 1 : 0" 
                class="p-2 rounded-full bg-gray-200/50 hover:bg-gray-300/50 dark:bg-white/5 dark:hover:bg-white/10 transition-colors"
            >
                <x-heroicon-m-chevron-right class="w-6 h-6 text-gray-700 dark:text-gray-300"/>
            </button>
        </div>
    </div>
@endif
