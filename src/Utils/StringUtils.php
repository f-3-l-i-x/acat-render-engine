<?php

namespace ACAT\Utils;

/**
 *
 */
class StringUtils
{

    /**
     * @param string|null $value
     * @return bool
     */
    public static function isEmpty(?string $value) : bool
    {
        return $value == null || strlen($value) == 0;
    }

}