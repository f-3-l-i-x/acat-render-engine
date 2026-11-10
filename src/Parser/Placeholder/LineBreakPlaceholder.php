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
     * Returns a human-readable representation of the line break, used for
     * logging.
     *
     * @return string
     */
    #[\Override]
    public function getXMLTagAsString(): string
    {
        return "<w:br/>";
    }

    /**
     * Builds a "w:br" DOM element representing a line break.
     *
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
    #[\Override]
    public function getDOMNode(DOMDocument $domDocument): DOMNode
    {
        return $domDocument->createElement('w:br');
    }

    /**
     * Returns 0, since a line break has no marker text of its own.
     *
     * @return int
     */
    #[\Override]
    public function length(): int
    {
        return 0;
    }

    /**
     * Returns an empty string, since a line break has no marker text of its
     * own.
     *
     * @return string
     */
    #[\Override]
    public function getNodeAsString(): string
    {
        return "";
    }
}
