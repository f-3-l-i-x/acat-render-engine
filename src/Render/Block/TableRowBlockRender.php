<?php

namespace ACAT\Render\Block;

use ACAT\Utils\DOMUtils;
use ACAT\Parser\Element\BlockElement;

/**
 *
 */
class TableRowBlockRender extends BlockRender
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
        parent::cleanUpBlock();

        $parentEndRow = DOMUtils::getParentNode($this->blockElement->getEnd(), 'w:tr');
        $parentStartRow = DOMUtils::getParentNode($this->blockElement->getStart(), 'w:tr');

        // the table row with the marker is removed, a marker outside a table row is removed itself
        $nodes = [
            $parentEndRow ?? $this->blockElement->getEnd(),
            $parentStartRow ?? $this->blockElement->getStart(),
        ];

        foreach ($nodes as $node) {
            $node->parentNode?->removeChild($node);
        }
    }
}
