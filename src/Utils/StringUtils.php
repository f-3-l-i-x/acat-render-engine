<?php

namespace ACAT\Utils;

/**
 *
 */
class StringUtils
{

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function startsWith(string $haystack, string $needle) : bool
    {
        return $needle === '' || strripos($haystack, $needle, -strlen($haystack)) !== false;
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function endsWith(string $haystack, string $needle) : bool
    {
        if ($needle === '') {
            return true;
        }
        $diff = strlen($haystack) - strlen($needle);
        return $diff >= 0 && stripos($haystack, $needle, $diff) !== false;
    }

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     * @deprecated
     *
     */
    public static function contains(string $haystack, string $needle) : bool
    {
        return str_contains($haystack, $needle);
    }

    /**
     * @param string|null $value
     * @return bool
     */
    public static function isEmpty(?string $value) : bool
    {
        return $value == null || strlen($value) == 0;
    }

}