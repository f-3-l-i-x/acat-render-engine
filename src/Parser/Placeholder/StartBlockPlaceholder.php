<?php

namespace ACAT\Parser\Placeholder;

use ACAT\Exception\PlaceholderException;

/**
 *
 */
class StartBlockPlaceholder extends BlockPlaceholder
{
    /**
     * Creates a block-start placeholder.
     *
     * @throws PlaceholderException
     */
    public function __construct()
    {
        parent::__construct(0);
    }
}
