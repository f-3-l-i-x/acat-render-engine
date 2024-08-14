<?php

namespace ACAT\Render\Block;

use DOMNode;
use ACAT\Utils\DOMUtils;
use ACAT\Parser\Element\BlockElement;

/**
 *
 */
class ParagraphBlockRender extends BlockRender
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
     * @return void
     */
    public function cleanUpBlock() : void
    {
        parent::cleanUpBlock();

        $this->deleteParentParagraph($this->blockElement->getEnd());
        $this->deleteParentParagraph($this->blockElement->getStart());
    }

    /**
     * @param DOMNode $node
     * @return void
     */
    private function deleteParentParagraph(DOMNode $node) : void
    {
        if (!DOMUtils::isRemoved($node)) {
            $paragraph = DOMUtils::getParentNode($node, 'w:p');
            if ($paragraph && !DOMUtils::isRemoved($paragraph)) {
                $paragraph->parentNode->removeChild($paragraph);
            }
        }
    }

}
