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
     * @todo
     */
    public function getContextNode() : DOMNode
    {
        return new DOMNode();
    }

}