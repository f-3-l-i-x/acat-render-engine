<?php

namespace ACAT\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;

/**
 *
 */
class WordTextPlaceholder extends ACatPlaceholder
{

    /**
     * @var string
     */
    protected string $namespace = 'https://schemas.openxmlformats.org/wordprocessingml/2006/main';

    /**
     * @var string|null
     */
    private ?string $text;

    /**
     * WordTextNode constructor.
     * @param string|null $text
     */
    public function __construct(?string $text)
    {
        parent::__construct();
        $this->text = $text;
    }

    /**
     * @return string|null
     */
    public function getText() : ?string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getXMLTagAsString() : string
    {
        return '<w:t xml:space="preserve">' . $this->text . '</w:t>';
    }

    /**
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
    public function getDOMNode(DOMDocument $domDocument) : DOMNode
    {
        if ($this->text == null) {
            $this->text = "";
        }

        $elementNode = $domDocument->createElement('w:t');
        $textNode = $domDocument->createTextNode($this->text);

        $preserverAttribute = $domDocument->createAttribute('xml:space');
        $preserverAttribute->value = "preserve";

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
        return $this->text;
    }
}