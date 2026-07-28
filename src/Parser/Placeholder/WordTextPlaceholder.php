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
    protected string $namespace = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    /**
     * Creates a placeholder wrapping a plain "w:t" text run.
     *
     * @param string|null $text
     */
    public function __construct(private ?string $text)
    {
        parent::__construct();
    }

    /**
     * Returns the placeholder's text.
     *
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * Returns a human-readable representation of the text run, used for
     * logging.
     *
     * @return string
     */
    public function getXMLTagAsString(): string
    {
        return '<w:t xml:space="preserve">' . $this->text . '</w:t>';
    }

    /**
     * Builds a "w:t" DOM element containing the placeholder's text.
     *
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
    public function getDOMNode(DOMDocument $domDocument): DOMNode
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
     * Returns the length of the placeholder's text.
     *
     * @return int
     */
    public function length(): int
    {
        return strlen($this->getNodeAsString());
    }

    /**
     * Returns the placeholder's text, or an empty string if none was set.
     *
     * @return string
     */
    public function getNodeAsString(): string
    {
        return $this->text ?? '';
    }
}
