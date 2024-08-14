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
     * @var string|null
     */
    private ?string $text;

    /**
     * TextNode constructor.
     * @param string|null $text
     */
    public function __construct(?string $text)
    {
        $this->text = $text;
        parent::__construct();
    }

    /**
     * @return string|null
     */
    public function getText() : ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     */
    public function setText(?string $text) : void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getXMLTagAsString() : string
    {
        return '<acat:text space="preserve">' . $this->text . '</w:t>';
    }

    /**
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
    public function getDOMNode(DOMDocument $domDocument) : DOMNode
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
        return '${T:' . $this->text . '}';
    }

}