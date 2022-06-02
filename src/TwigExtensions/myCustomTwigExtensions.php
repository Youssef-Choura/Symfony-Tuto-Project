<?php

namespace App\TwigExtensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class myCustomTwigExtensions extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('defaultImage',[$this, 'defaultImage'])
        ];
    }
    public function defaultImage(string $path):string
    {
        if (trim($path) === ''){
            return 'download.png';
        }
        return $path;
    }
}