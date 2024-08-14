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
    public function __construct(BlockElement $blockElement, array $values)
    {
        parent::__construct();
        $this->values = $values;
        $this->blockElement = $blockElement;
    }

    /**
     *
     */
    public function cleanUpBlock() : void
    {
        // TODO: Implement cleanUpBlock() method.
    }
}