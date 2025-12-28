<div 
    x-data="{ 
        currentUrl: '{{ $episodes->first()?->video_source_url }}', 
        playVideo(url) {
            // Update our state
            this.currentUrl = url;
            
            // Prepare the URL for Dailymotion
            const separator = url.includes('?') ? '&' : '?';
            const cleanUrl = `${url}${separator}autoplay=0&queue-autoplay-next=0`;

            // Set a default URL for black content
            let iframeUrl = cleanUrl;
            if (!url) {
                iframeUrl = 'data:text/html;charset=utf-8,%3Chtml%3E%3Chead%3E%3Cstyle%3Ebody%20%7B%0A%20%20background-color:%23000000;%0A%20%20color:%23ffffff;%0A%7D%0A%3C%2Fstyle%3E%3C%2Fhead%3E%3Cbody%3E%3C%2Fbody%3E';
            }

            // Force the iframe to change
            if (this.$refs.videoPlayer) {
                this.$refs.videoPlayer.src = iframeUrl;
            }
        }
    }"
    style="display: flex; flex-direction: row; height: 600px; width: 100%; overflow: hidden; background: #111827; border-radius: 12px;"
>
    
    <div style="width: 280px; flex-shrink: 0; display: flex; flex-direction: column; border-right: 1px solid #374151;">
        <div style="padding: 16px; border-bottom: 1px solid #374151; background: #1f2937;">
            <h3 style="color: #9ca3af; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">Episodes</h3>
        </div>

        <div style="flex-grow: 1; overflow-y: auto; padding: 8px; display: flex; flex-direction: column; gap: 4px;">
            @foreach($episodes as $episode)
                <button 
                    type="button"
                    {{-- This triggers the function inside x-data --}}
                    x-on:click="playVideo('{{ $episode->video_source_url }}')"
                    style="width: 100%; text-align: left; padding: 16px 20px; border-radius: 6px; border: none; cursor: pointer; display: block;"
                    {{-- This changes the button color based on currentUrl --}}
                    :style="currentUrl === '{{ $episode->video_source_url }}' ? 'background: #fbbf24; color: #000;' : 'background: transparent; color: #d1d5db;'"
                >
                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; padding: 10px;">
                        <span>Episode {{ $episode->episode_number }}</span>
                        <template x-if="currentUrl === '{{ $episode->video_source_url }}'">
                            <span style="font-size: 10px; background: #000; color: #fbbf24; padding: 2px 6px; border-radius: 4px; text-transform: uppercase;">Playing</span>
                        </template>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    <div style="flex-grow: 1; background: #000; position: relative;">
        <iframe 
            x-ref="videoPlayer" 
            :src="currentUrl ? currentUrl : 'data:text/html;charset=utf-8,%3Chtml%3E%3Chead%3E%3Cstyle%3Ebody%20%7B%0A%20%20background-color:%23000000;%0A%20%20color:%23ffffff;%0A%7D%0A%3C%2Fstyle%3E%3C%2Fhead%3E%3Cbody%3E%3C%2Fbody%3E'" 
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;" 
            allowfullscreen>
        </iframe>
    </div>
</div>
