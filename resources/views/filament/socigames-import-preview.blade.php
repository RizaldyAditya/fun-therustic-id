<div class="space-y-4">
    @if($cover_image)
        <img src="{{ str_starts_with($cover_image, 'http') ? $cover_image : asset('storage/' . $cover_image) }}" class="w-full rounded-lg" />
    @endif
    <div class="rounded-lg border p-4 space-y-2">
        <p><strong>Title:</strong> {{ $title }}</p>
        <p><strong>Version:</strong> {{ $version ?? 'N/A' }}</p>
        <p><strong>Developer:</strong> {{ $developer ?? 'N/A' }}</p>
        <p><strong>Genres:</strong> {{ $genres ?? 'N/A' }}</p>
        <p><strong>Engine:</strong> {{ $engine ?? 'N/A' }}</p>
        <p><strong>Description:</strong> {{ $description ?? 'N/A' }}</p>
        <p><strong>Release Date:</strong> {{ $release_date?->format('M d, Y') ?? 'N/A' }}</p>
    </div>
</div>
