<?php

namespace ACAT\Parser\Placeholder;

use ACAT\Exception\PlaceholderException;

/**
 *
 */
class EndBlockPlaceholder extends BlockPlaceholder
{
    /**
     * Creates a block-end placeholder.
     *
     * @throws PlaceholderException
     */
    public function __construct()
    {
        parent::__construct(1);
    }
}
