<?php

namespace ACAT\Parser\Placeholder;

use ACAT\Exception\PlaceholderException;

/**
 *
 */
class EndBlockPlaceholder extends BlockPlaceholder
{

    /**
     * @throws PlaceholderException
     */
    public function __construct()
    {
        parent::__construct(1);
    }

}