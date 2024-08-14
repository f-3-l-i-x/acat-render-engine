<?php

namespace ACAT\Utils;

use JetBrains\PhpStorm\Pure;

/**
 *
 */
class FileUtils
{

    /**
     * @param string $file
     * @return string
     */
    public static function stripTrailingSlash(string $file) : string
    {
        if ($file === '' || !StringUtils::startsWith($file, '/')) {
            return $file;
        }
        return substr($file, 1);
    }

}