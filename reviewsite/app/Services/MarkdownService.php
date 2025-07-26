<?php

namespace App\Services;

use Illuminate\Support\Str;

class MarkdownService
{
    public static function parse(string $content): string
    {
        // First, process spoiler tags
        $content = self::processSpoilerTags($content);
        
        // Then process regular markdown
        return Str::markdown($content);
    }

    private static function processSpoilerTags(string $content): string
    {
        // Convert [spoiler]text[/spoiler] to HTML with JavaScript toggle
        $content = preg_replace_callback(
            '/\[spoiler\](.*?)\[\/spoiler\]/s',
            function ($matches) {
                $spoilerId = 'spoiler_' . uniqid();
                $spoilerText = htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8');
                
                return '<div class="spoiler-container mb-4">
                    <button onclick="toggleSpoiler(\'' . $spoilerId . '\')" 
                            class="spoiler-toggle bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-semibold transition-colors mb-2">
                        <span class="spoiler-text">Show Spoiler</span>
                        <span class="spoiler-text-hidden hidden">Hide Spoiler</span>
                    </button>
                    <div id="' . $spoilerId . '" class="spoiler-content hidden bg-gray-800 border border-red-600 rounded-lg p-4">
                        <div class="prose prose-invert max-w-none">' . $spoilerText . '</div>
                    </div>
                </div>';
            },
            $content
        );

        return $content;
    }
} 