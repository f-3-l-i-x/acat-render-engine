<?php

namespace ACAT\Parser\Element;


use DOMNode;
use DOMNodeList;

/**
 *
 */
class TableCellBlock extends BlockElement
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