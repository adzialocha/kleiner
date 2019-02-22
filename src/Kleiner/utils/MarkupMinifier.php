<?php

namespace Kleiner\Utils;

class MarkupMinifier
{
    const REGEX = '%(?>[^\S ]\s*| \s{2,})(?=[^<]*+(?:<(?!/?(?:textarea|pre|script)\b)[^<]*+)*+(?:<(?>textarea|pre|script)\b| \z))%Six';

    public static function convert ($str)
    {
        return preg_replace(static::REGEX, '', $str);
    }
}
