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
    public function getContextNode() : DOMNode
    {
        return $this->getEnd();
    }

}