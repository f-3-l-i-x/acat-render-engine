<?php

namespace ACAT\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;

/**
 *
 */
class TextPlaceholder extends ACatPlaceholder
{
    /**
     * Creates a text placeholder with the given static text.
     *
     * @param string|null $text
     */
    public function __construct(private ?string $text)
    {
        parent::__construct();
    }

    /**
     * Returns the placeholder's static text.
     *
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * Sets the placeholder's static text.
     *
     * @param string|null $text
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    /**
     * Returns a human-readable representation of the text marker, used for
     * logging.
     *
     * @return string
     */
    public function getXMLTagAsString(): string
    {
        return '<acat:text space="preserve">' . $this->text . '</acat:text>';
    }

    /**
     * Builds the "acat:text" DOM element carrying the placeholder's id and
     * text.
     *
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
    public function getDOMNode(DOMDocument $domDocument): DOMNode
    {
        $elementNode = $domDocument->createElementNS($this->namespace, 'acat:text');
        $textNode = $domDocument->createTextNode($this->text);

        $idAttribute = $domDocument->createAttribute('id');
        $idAttribute->nodeValue = $this->id;

        $preserverAttribute = $domDocument->createAttribute('space');
        $preserverAttribute->value = "preserve";

        $elementNode->appendChild($idAttribute);
        $elementNode->appendChild($preserverAttribute);
        $elementNode->appendChild($textNode);

        return $elementNode;
    }

    /**
     * Returns the length of the original "${T:...}" marker text.
     *
     * @return int
     */
    public function length(): int
    {
        return strlen($this->getNodeAsString());
    }

    /**
     * Returns the original "${T:...}" marker text.
     *
     * @return string
     */
    public function getNodeAsString(): string
    {
        return '${T:' . $this->text . '}';
    }
}
