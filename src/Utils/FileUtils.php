<?php

namespace ACAT\Utils;

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
        if ($file === '' || !str_starts_with($file, '/')) {
            return $file;
        }
        return substr($file, 1);
    }

}