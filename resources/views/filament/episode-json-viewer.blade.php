<div x-data="{ 
    content: @js($json),
    copyToClipboard() {
        window.navigator.clipboard.writeText(this.content);
        new FilamentNotification()
            .title('JSON copied to clipboard')
            .success()
            .send();
    }
}" class="space-y-4">
    <div class="flex justify-end">
        <x-filament::button 
            color="gray" 
            icon="heroicon-m-clipboard" 
            size="sm"
            @click="copyToClipboard"
        >
            Copy JSON
        </x-filament::button>
    </div>

    <div class="relative group">
        <pre class="p-4 overflow-x-auto text-sm font-mono text-gray-200 bg-gray-950 rounded-xl border border-white/10 max-h-[600px]"><code x-text="content"></code></pre>
    </div>
</div>
