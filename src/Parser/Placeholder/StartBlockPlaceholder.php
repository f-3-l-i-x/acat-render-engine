<?php

namespace ACAT\Parser\Placeholder;

use ACAT\Exception\PlaceholderException;

/**
 *
 */
class StartBlockPlaceholder extends BlockPlaceholder
{

    /**
     * @throws PlaceholderException
     */
    public function __construct()
    {
        parent::__construct(0);
    }

}