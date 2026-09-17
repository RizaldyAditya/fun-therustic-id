@php
function injectAlpineDirectives(string $html): string
{
    if (!str_contains($html, 'tab_widget')) {
        return $html;
    }

    $html = preg_replace_callback(
        '/<div class="tab_widget wp_shortcodes_tabs"><ul class="wps_tabs"><li><a href="#" data-tab="(tab-[^"]+)">/',
        function ($m) {
            $firstId = $m[1];

            return '<div class="tab_widget wp_shortcodes_tabs" x-data="{ activeTab: \''.$firstId.'\' }"><ul class="wps_tabs"><li><a href="#" data-tab="'.$firstId.'">';
        },
        $html
    );

    $html = preg_replace(
        '/<a href="#" data-tab="(tab-[^"]+)">/',
        '<a href="#" data-tab="$1" @click.prevent="activeTab = \'$1\'" :class="{ active: activeTab === \'$1\' }">',
        $html
    );

    $html = preg_replace(
        '/<div id="(tab-[^"]+)" class="tab_content/',
        '<div id="$1" class="tab_content" x-show="activeTab === \'$1\'"',
        $html
    );

    return $html;
}

$parsedContent = injectAlpineDirectives($content);
@endphp

<div class="anime7-content" style="background: #ffffff; color: #333333; padding: 1.5rem; border-radius: 0.5rem; font-family: 'Signika Negative', 'Segoe UI', Arial, sans-serif; font-size: 18px; line-height: 1.7;">
    {!! $parsedContent !!}
</div>

<style>
    .anime7-content {
        background: #ffffff !important;
        color: #333333;
    }

    .anime7-content * {
        color: #333333;
    }

    .anime7-content p {
        margin-bottom: 1rem;
        line-height: 1.7;
    }

    .anime7-content a {
        color: #16a500;
        text-decoration: none;
        transition: color 0.2s;
    }

    .anime7-content a:hover {
        color: #128c00;
        text-decoration: underline;
    }

    .anime7-content img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 1rem auto;
        border-radius: 4px;
    }

    .anime7-content iframe {
        width: 100%;
        aspect-ratio: 16 / 9;
        border: none;
        border-radius: 4px;
        margin: 1rem 0;
    }

    .anime7-content strong,
    .anime7-content b {
        font-weight: 700;
    }

    .anime7-content [x-cloak] {
        display: none !important;
    }

    /* Tab Widget */
    .anime7-content .tab_widget {
        margin: 1.5rem 0;
    }

    .anime7-content .wps_tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0;
        list-style: none;
        padding: 0;
        margin: 0;
        border-bottom: 2px solid #e0e0e0;
    }

    .anime7-content .wps_tabs li {
        margin: 0;
    }

    .anime7-content .wps_tabs li a {
        display: inline-block;
        padding: 0.6rem 1.2rem;
        text-decoration: none;
        color: #666666;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s;
        cursor: pointer;
    }

    .anime7-content .wps_tabs li a:hover {
        color: #16a500;
        background: rgba(22, 165, 0, 0.05);
    }

    .anime7-content .wps_tabs li a.active {
        color: #16a500;
        border-bottom-color: #16a500;
    }

    .anime7-content .tab_container {
        padding: 1rem 0;
    }

    .anime7-content .tab_content {
        display: none;
    }

    .anime7-content .tab_content:first-child {
        display: block;
    }

    .anime7-content .tab_content img {
        max-width: 100%;
        height: auto;
        margin: 0 auto;
    }

    .anime7-content .tab_content iframe {
        width: 100%;
    }

    /* Download links styling */
    .anime7-content p:has(a[href*="dl.anime7.download"]) {
        line-height: 2;
    }

    .anime7-content a[href*="dl.anime7.download"] {
        font-weight: 600;
    }
</style>
