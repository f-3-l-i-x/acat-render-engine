<?php

namespace ACAT\Parser\Element;

use DOMXPath;
use DOMElement;
use DOMDocument;
use ACAT\Utils\DOMUtils;
use ACAT\Parser\ParserConstants;
use ACAT\Exception\ElementException;

/**
 *
 */
abstract class Element
{

    /**
     * @var bool
     */
    protected bool $blockElement;

    /**
     * @var DOMDocument
     */
    protected DOMDocument $domDocument;

    /**
     * @var DOMXPath|null
     */
    protected ?DOMXPath $domXPath = null;

    /**
     * @var DOMElement
     */
    protected DOMElement $element;

    /**
     * @param DOMElement $element
     * @param bool $blockElement
     * @throws ElementException
     */
    public function __construct(DOMElement $element, bool $blockElement = false)
    {
        $this->element = $element;
        $this->blockElement = $blockElement;

        if (!$element->ownerDocument) {
            throw new ElementException("node doesn't have a owner document");
        }

        $this->domDocument = $element->ownerDocument;
    }

    /**
     * @return DOMElement
     */
    public function getElement() : DOMElement
    {
        return $this->element;
    }

    /**
     * @return DOMDocument|null
     */
    public function getDomDocument() : ?DOMDocument
    {
        return $this->domDocument;
    }

    /**
     * @return DOMXPath
     */
    public function getXpath() : DOMXPath
    {
        if (!$this->domXPath) {
            $this->domXPath = new DOMXPath($this->domDocument);
            foreach (ParserConstants::$wordNamespaces as $prefix => $url) {
                $this->getXpath()->registerNamespace($prefix, $url);
            }
        }
        return $this->domXPath;
    }

    /**
     * @return string
     * @throws ElementException
     */
    public function getFieldId() : string
    {
        $fieldId = $this->getAttributeValue('field');
        if (empty($fieldId)) {
            throw new ElementException($this->element->nodeName . ' does not contains a field id');
        }
        return $fieldId;
    }

    /**
     * @param string $attribute
     * @return string|null
     */
    protected function getAttributeValue(string $attribute) : ?string
    {
        if ($this->element->hasAttribute($attribute)) {
            return $this->element->getAttribute($attribute);
        }
        return null;
    }

    /**
     * @return string
     * @throws ElementException
     */
    public function getId() : string
    {
        $id = $this->getAttributeValue('id');
        if (empty($id)) {
            throw new ElementException($this->element->nodeName . ' does not contains a field id');
        }
        return $id;
    }

    /**
     *
     */
    public function delete() : void
    {
        if (!DOMUtils::isRemoved($this->element)) {
            $this->element->parentNode->removeChild($this->element);
        }
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return "";
    }

}