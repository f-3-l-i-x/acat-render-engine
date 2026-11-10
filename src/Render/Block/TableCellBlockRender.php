<?php

namespace ACAT\Render\Block;

use ACAT\Parser\Element\BlockElement;

/**
 *
 */
class TableCellBlockRender extends BlockRender
{
    /**
     * @param BlockElement $blockElement
     * @param array $values
     */
    public function __construct(protected BlockElement $blockElement, protected array $values)
    {
        parent::__construct();
    }

    /**
     *
     */
    #[\Override]
    public function cleanUpBlock(): void
    {
        // TODO: Implement cleanUpBlock() method.
    }
}
