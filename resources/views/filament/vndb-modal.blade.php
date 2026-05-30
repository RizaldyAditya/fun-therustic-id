@php
    $devStatusLabels = [0 => 'Finished', 1 => 'In Development', 2 => 'Cancelled'];
    $devStatusColors = [0 => 'success', 1 => 'warning', 2 => 'danger'];
    $platformLabels = [
        'win' => 'Windows', 'lin' => 'Linux', 'mac' => 'Mac',
        'web' => 'Web', 'ios' => 'iOS', 'and' => 'Android',
        'dos' => 'DOS', 'vnds' => 'VNDS',
    ];
    $genderLabels = ['f' => 'Female', 'm' => 'Male', 'b' => 'Both', 'n' => 'Non-binary'];
    $genderColors = ['f' => 'info', 'm' => 'primary', 'b' => 'warning', 'n' => 'gray'];
@endphp

<div class="space-y-8 py-6">
    @if (! $vndata)
        <div class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
            <div class="p-4 rounded-full bg-gray-50 dark:bg-gray-800/50 mb-4">
                <x-heroicon-o-exclamation-triangle class="w-16 h-16 opacity-20" />
            </div>
            <h3 class="text-lg font-medium">Could not load VNDB data</h3>
            <p class="text-sm">The VNDB API did not return data for ID <code class="text-primary-500">{{ $vndb_id }}</code>.</p>
        </div>
    @else
        {{-- Top Section: Info left, Cover right --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-4">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ $vndata['title'] ?? $title }}
                </h2>

                @if ($vndata['developers'] ?? null)
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Developer:</span>
                        {{ collect($vndata['developers'])->pluck('name')->implode(', ') }}
                    </p>
                @endif

                <div class="flex flex-wrap items-center gap-2">
                    @if (isset($vndata['devstatus']))
                        <x-filament::badge :color="$devStatusColors[$vndata['devstatus']] ?? 'gray'">
                            {{ $devStatusLabels[$vndata['devstatus']] ?? 'Unknown' }}
                        </x-filament::badge>
                    @endif

                    @if ($vndata['platforms'] ?? null)
                        @foreach ($vndata['platforms'] as $platform)
                            <x-filament::badge color="gray">
                                {{ $platformLabels[$platform] ?? strtoupper($platform) }}
                            </x-filament::badge>
                        @endforeach
                    @endif

                    @if ($vndata['released'] ?? null)
                        <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">
                            Released: {{ $vndata['released'] }}
                        </span>
                    @endif
                </div>

                @if ($vndata['description'] ?? null)
                    <div class="text-sm leading-relaxed text-gray-700 dark:text-gray-300 whitespace-pre-line
                                bg-gray-50 dark:bg-gray-800/30 rounded-xl p-5 border dark:border-white/5">
                        {!! nl2br(e(preg_replace('/\n{2,}/', "\n", preg_replace('/\[url=(.*?)\](.*?)\[\/url\]/', '$2 ($1)', strip_tags($vndata['description']))))) !!}
                    </div>
                @endif
            </div>

            @if ($vndata['image']['url'] ?? null)
                <div class="lg:col-span-1">
                    <img src="{{ $vndata['image']['url'] }}" alt="{{ $vndata['title'] ?? $title }}"
                         class="w-full h-auto object-cover rounded-xl shadow-md border dark:border-white/10">
                </div>
            @endif
        </div>

        {{-- Characters Section --}}
        @if (count($chardata) > 0)
            <section>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-heroicon-o-users class="w-5 h-5 text-primary-500" />
                    Characters
                    <span class="text-sm font-normal text-gray-400 dark:text-gray-500">({{ count($chardata) }})</span>
                </h3>

                <div x-data="{
                    search: '',
                    noMatch: false,
                    checkNoMatch() {
                        $nextTick(() => {
                            let cards = this.$el.querySelectorAll('.character-card');
                            this.noMatch = cards.length > 0 && Array.from(cards).every(c => c.offsetParent === null);
                        });
                    }
                }" x-init="$watch('search', () => checkNoMatch())" class="space-y-4">
                    <div class="relative max-w-sm">
                        <x-heroicon-m-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                        <input type="text" x-model="search" @input="checkNoMatch()" placeholder="Search by name..."
                               class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                      placeholder-gray-400 dark:placeholder-gray-500
                                      focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                                      text-sm transition-shadow duration-200">
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                        @foreach ($chardata as $i => $char)
                                 <div x-show="search === '' || {{ json_encode($char['name']) }}.toLowerCase().includes(search.toLowerCase()) || {{ json_encode($char['original'] ?? '') }}.toLowerCase().includes(search.toLowerCase())"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="character-card overflow-hidden rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-200 dark:border-white/5
                                        hover:border-primary-300 dark:hover:border-primary-700
                                        hover:shadow-sm transition-all duration-200">
                                @if ($char['image']['url'] ?? null)
                                    <div class="aspect-square overflow-hidden">
                                        <img src="{{ $char['image']['url'] }}" alt="{{ $char['name'] }}"
                                             class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="aspect-square bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <x-heroicon-o-user class="w-12 h-12 text-gray-400" />
                                    </div>
                                @endif

                                <div class="p-3 space-y-1.5">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white leading-tight line-clamp-2">
                                        {{ $char['name'] }}
                                    </h4>

                                    @if ($char['original'] ?? null)
                                        <p class="text-xs text-gray-400 dark:text-gray-500 line-clamp-1">
                                            {{ $char['original'] }}
                                        </p>
                                    @endif

                                    @if ($char['description'] ?? null)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-3">
                                            {{ strip_tags($char['description']) }}
                                        </p>
                                    @endif

                                    @if ($char['gender'][0] ?? null)
                                        <x-filament::badge :color="$genderColors[$char['gender'][0]] ?? 'gray'" class="!mt-2 text-xs">
                                            {{ $genderLabels[$char['gender'][0]] ?? $char['gender'][0] }}
                                        </x-filament::badge>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div x-show="search !== '' && noMatch"
                         x-transition:enter="transition ease-out duration-200"
                         class="text-center py-8 text-gray-400 dark:text-gray-500">
                        <x-heroicon-o-user-minus class="w-10 h-10 mx-auto mb-2 opacity-40" />
                        <p class="text-sm">No characters match "<span x-text="search" class="font-medium text-gray-500 dark:text-gray-400"></span>"</p>
                    </div>
                </div>
            </section>
        @endif
    @endif
</div>
