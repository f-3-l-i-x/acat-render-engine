<?php

namespace ACAT\Parser\Element;

use DOMNode;

/**
 *
 */
class TextBlock extends BlockElement
{
    /**
     * @return DOMNode
     */
    #[\Override]
    public function getContextNode(): DOMNode
    {
        return $this->getEnd();
    }
}
