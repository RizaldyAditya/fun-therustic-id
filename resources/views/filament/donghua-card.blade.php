<div class="relative group rounded-xl bg-gray-900 overflow-hidden shadow-lg transition-all hover:-translate-y-1 border border-white/5">
    <div class="relative aspect-[3/4] w-full overflow-hidden">
        <a href="{{ $getRecord()->stream_url }}" class="absolute inset-0 z-11" target="_blank"></a>
            <img src="{{ asset('storage/' . $getRecord()->donghua->image_cover) }}" 
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent opacity-80"></div>
            <div class="absolute top-0 left-0 z-10">
                <div class="bg-yellow-400 text-black text-[14px] font-black px-3 py-1 rounded-br-lg shadow-lg flex items-center gap-1 uppercase italic">
                    <span>EP {{ $getRecord()->episode_number }}</span>
                </div>
            </div>
            
            <div class="absolute top-2 right-2 z-20">
                <span class="bg-black/60 backdrop-blur-md text-white text-[12px] px-2 py-0.5 rounded border border-white/10 font-bold uppercase">
                    S{{ $getRecord()->donghua->season }}
                </span>
            </div>

            <div class="absolute bottom-2 right-2 z-20">
                <span class="text-[10px] text-primary-400 font-bold drop-shadow-lg uppercase tracking-wider">
                    {{ $getRecord()->stream->name }}
                </span>
            </div>
        </div>

        <div class="p-3">
            <h3 class="text-xs font-bold text-gray-100 line-clamp-2 group-hover:text-primary-400 transition-colors uppercase leading-tight">
                {{ $getRecord()->donghua->title_en }}
            </h3>
            <p class="text-[12px] text-gray-500 mt-1 truncate font-medium">
                {{ $getRecord()->created_at ? \Carbon\Carbon::parse($getRecord()->created_at)->format('F d, Y') : 'TBA' }}
            </p>
        </div>
    </a>
</div>
