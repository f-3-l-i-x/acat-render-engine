<?php

namespace ACAT\Parser\Element;

use DOMNode;
use ACAT\Utils\DOMUtils;

/**
 *
 */
class TableRowBlock extends BlockElement
{
    /**
     * @return DOMNode
     */
    #[\Override]
    public function getContextNode(): DOMNode
    {
        return DOMUtils::getParentNode($this->getEnd(), 'w:tr');
    }
}
