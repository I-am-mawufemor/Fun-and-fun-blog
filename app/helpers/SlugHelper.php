<?php

namespace Mawufemor\Techandfun\Helpers;

class SlugHelper
{
    public static function generate(string $name): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    }
}