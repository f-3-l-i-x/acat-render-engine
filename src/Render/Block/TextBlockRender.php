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
    public function __construct(protected BlockElement $blockElement, protected array $values)
    {
        parent::__construct();
    }

    /**
     *
     */
    public function cleanUpBlock(): void
    {
        parent::cleanUpBlock();

        $parentEndNode = $this->blockElement->getEnd()->parentNode;
        $parentStartNode = $this->blockElement->getStart()->parentNode;

        $parentStartNode?->removeChild($this->blockElement->getStart());
        $parentEndNode?->removeChild($this->blockElement->getEnd());
    }
}
