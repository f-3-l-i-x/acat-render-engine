<?php

namespace ACAT\Parser\Element;

use DOMNode;
use ACAT\Utils\DOMUtils;

/**
 *
 */
class ParagraphBlock extends BlockElement
{

    /**
     * @return DOMNode
     */
    public function getContextNode() : DOMNode
    {
        return DOMUtils::getParentNode($this->getEnd(), 'w:p');
    }
}