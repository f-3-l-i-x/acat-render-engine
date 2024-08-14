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
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
    public function getDOMNode(DOMDocument $domDocument) : DOMNode
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
     * @return string
     */
    public function getXMLTagAsString() : string
    {
        return '<acat:view id="' . $this->getId() . '" field="' . $this->fieldId . '"/>';
    }

    /**
     * @return string
     */
    public function getNodeAsString() : string
    {
        return '${V:' . $this->fieldId . '}';
    }
}