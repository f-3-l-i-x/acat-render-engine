<?php

namespace ACAT\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;

/**
 *
 */
class ViewPlaceholder extends FieldPlaceholder
{
    /**
     * Builds the "acat:view" DOM element carrying the placeholder's id and
     * view id.
     *
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
    #[\Override]
    public function getDOMNode(DOMDocument $domDocument): DOMNode
    {
        $elementNode = $domDocument->createElementNS($this->namespace, 'acat:view');

        $idAttribute = $domDocument->createAttribute('id');
        $idAttribute->value = $this->getId();

        $fieldAttribute = $domDocument->createAttribute('view');
        $fieldAttribute->value = $this->fieldId;

        $elementNode->appendChild($idAttribute);
        $elementNode->appendChild($fieldAttribute);

        return $elementNode;
    }

    /**
     * Returns a human-readable representation of the view marker, used for
     * logging.
     *
     * @return string
     */
    #[\Override]
    public function getXMLTagAsString(): string
    {
        return '<acat:view id="' . $this->getId() . '" field="' . $this->fieldId . '"/>';
    }

    /**
     * Returns the original "${V:...}" marker text.
     *
     * @return string
     */
    #[\Override]
    public function getNodeAsString(): string
    {
        return '${V:' . $this->fieldId . '}';
    }
}
