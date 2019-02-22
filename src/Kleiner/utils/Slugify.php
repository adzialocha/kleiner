<?php

namespace Kleiner\Utils;

class Slugify
{
    public static function convert ($str)
    {
        $result = preg_replace('~[^\\pL\d]+~u', '-', $str);
        $result = trim($result, '-');
        $result = iconv('utf-8', 'us-ascii//TRANSLIT', $result);
        $result = strtolower($result);
        $result = preg_replace('~[^-\w]+~', '', $result);

        return $result;
    }
}
