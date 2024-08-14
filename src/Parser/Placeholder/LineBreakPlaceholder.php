<?php

namespace ACAT\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;

/**
 *
 */
class LineBreakPlaceholder extends ACatPlaceholder
{

    /**
     * @return string
     */
    public function getXMLTagAsString() : string
    {
        return "<w:br/>";
    }

    /**
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
    public function getDOMNode(DOMDocument $domDocument) : DOMNode
    {
        return $domDocument->createElement('w:br');
    }

    /**
     * @return int
     */
    public function length() : int
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getNodeAsString() : string
    {
        return "";
    }

}