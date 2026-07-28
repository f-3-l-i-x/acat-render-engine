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
     * Creates a field placeholder for the given field id.
     *
     * @param string $fieldId
     */
    public function __construct(protected string $fieldId)
    {
        parent::__construct();
    }

    /**
     * Returns the id of the field this placeholder renders.
     *
     * @return string
     */
    public function getFieldId(): string
    {
        return $this->fieldId;
    }

    /**
     * Builds the "acat:field" DOM element carrying the placeholder's id and
     * field id.
     *
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
    public function getDOMNode(DOMDocument $domDocument): DOMNode
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
     * Returns a human-readable representation of the field marker, used for
     * logging.
     *
     * @return string
     */
    public function getXMLTagAsString(): string
    {
        return '<acat:field id="' . $this->getId() . '" field="' . $this->fieldId . '"/>';
    }

    /**
     * Returns the length of the original "${F:...}" marker text.
     *
     * @return int
     */
    public function length(): int
    {
        return strlen($this->getNodeAsString());
    }

    /**
     * Returns the original "${F:...}" marker text.
     *
     * @return string
     */
    public function getNodeAsString(): string
    {
        return '${F:' . $this->fieldId . '}';
    }
}
