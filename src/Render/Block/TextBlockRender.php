<?php

namespace ACAT\Render\Block;

use ACAT\Parser\Element\BlockElement;

/**
 *
 */
class TextBlockRender extends BlockRender
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
        $parentEndNode = $this->blockElement->getEnd()->parentNode;
        $parentStartNode = $this->blockElement->getStart()->parentNode;

        $parentStartNode?->removeChild($this->blockElement->getStart());
        $parentEndNode?->removeChild($this->blockElement->getEnd());
    }

}