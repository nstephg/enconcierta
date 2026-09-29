<?php

if (!function_exists('format_mentions')) {
    /**
     * Convierte menciones @handle en enlaces morados clickeables hacia el perfil del usuario.
     */
    function format_mentions(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        $escaped = e($text);

        return preg_replace(
            '/@([a-zA-Z0-9_]+)/',
            '<a href="/perfil/$1" class="text-[#7C5CFF] font-semibold hover:text-[#2FE6D0] hover:underline transition-colors cursor-pointer">@$1</a>',
            $escaped
        );
    }
}