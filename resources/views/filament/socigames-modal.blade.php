<div class="p-4">
    @if(empty($items))
        <div class="text-center py-8 text-gray-500">
            <p>No items found or unable to fetch data.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($items as $item)
                <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow flex flex-col">
                    <a href="{{ $item['url'] }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="block">
                        @if($item['image'])
                            <img src="{{ $item['image'] }}"
                                 alt="{{ $item['title'] }}"
                                 class="w-full h-40 object-cover"
                                 loading="lazy">
                        @endif
                    </a>
                    <div class="p-3 flex flex-col flex-1">
                        <h3 class="font-semibold text-sm line-clamp-2 mb-2">{{ $item['title'] }}</h3>
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                            @if($item['category'])
                                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded">{{ $item['category'] }}</span>
                            @endif
                            @if($item['date'])
                                <span>{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</span>
                            @endif
                        </div>
                        <div class="mt-auto">
                            <button
                                type="button"
                                x-on:click="$wire.importSocigamesItem({{ Illuminate\Support\Js::from($item) }})"
                                class="w-full inline-flex items-center justify-center gap-1 rounded-lg bg-primary-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
                            >
                                <x-heroicon-s-arrow-down-tray class="h-4 w-4" />
                                Import
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
