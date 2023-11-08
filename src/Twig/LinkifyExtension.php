<?php

namespace App\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

/*
    This is a custom extension for twig to replace link to clickable html link (use with |linkify) 
*/

class LinkifyExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('linkify', [$this, 'linkifyText'], ['is_safe' => ['html']]),
        ];
    }

    public function linkifyText(string $text): ?string
    {
        // replace link
        $text = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank">$1</a>',
            $text
        );

        return $text;
    }
}
