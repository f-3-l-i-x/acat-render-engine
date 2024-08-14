<?php

namespace ACAT\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;

/**
 *
 */
class FieldPlaceholder extends ACatPlaceholder
{

    /**
     * @var string
     */
    protected string $fieldId;

    /**
     * @param string $fieldId
     */
    public function __construct(string $fieldId)
    {
        parent::__construct();
        $this->fieldId = $fieldId;
    }

    /**
     * @return string
     */
    public function getFieldId() : string
    {
        return $this->fieldId;
    }

    /**
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
    public function getDOMNode(DOMDocument $domDocument) : DOMNode
    {
        $elementNode = $domDocument->createElementNS($this->namespace, 'acat:field');

        $idAttribute = $domDocument->createAttribute('id');
        $idAttribute->value = $this->getId();

        $fieldAttribute = $domDocument->createAttribute('field');
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
        return "<acat:field id" . $this->getId() . " field=" . $this->fieldId . "/>";
    }

    /**
     * @return int
     */
    public function length() : int
    {
        return strlen($this->getNodeAsString());
    }

    /**
     * @return string
     */
    public function getNodeAsString() : string
    {
        return '${F:' . $this->fieldId . '}';
    }

}