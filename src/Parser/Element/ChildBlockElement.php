<?php

namespace ACAT\Parser\Element;

use DOMNode;
use DOMNodeList;
use ACAT\Utils\DOMUtils;
use ACAT\Parser\ParserConstants;
use ACAT\Exception\ElementException;

/**
 *
 */
class ChildBlockElement extends Element
{

    /**
     * @return string
     * @throws ElementException
     */
    public function getFieldId() : string
    {
        throw new ElementException('not implemented');
    }

    /**
     * @param string $attribute
     * @return string|null
     * @throws ElementException
     */
    public function getAttributeValue(string $attribute) : ?string
    {
        throw new ElementException('not implemented');
    }

    /**
     * @return array
     * @throws ElementException
     */
    public function getViewElements() : array
    {
        $viewElements = [];
        $viewNodes = $this->getElements(ParserConstants::ACAT_VIEW_NODE);

        foreach ($viewNodes as $viewNode) {
            $viewElements[] = new ViewElement($viewNode, true);
        }

        return $viewElements;
    }

    /**
     * @param string $elementType
     * @return DOMNodeList
     */
    private function getElements(string $elementType) : DOMNodeList
    {
        if (DOMUtils::isRemoved($this->element)) {
            return new DOMNodeList();
        }

        if ($this->element->nodeName == $elementType) {
            $contextNode = $this->element->parentNode;
        } else {
            $contextNode = $this->element;
        }

        return $this->getXpath()->query('.//' . $elementType, $contextNode);
    }

    /**
     * @return array
     * @throws ElementException
     */
    public function getTextElements() : array
    {
        $textElements = [];
        $textNodes = $this->getElements(ParserConstants::ACAT_TEXT_NODES);

        foreach ($textNodes as $textNode) {
            $textElements[] = new TextElement($textNode, true);
        }

        return $textElements;
    }

    /**
     * @return array
     * @throws ElementException
     */
    public function getFieldElements() : array
    {
        $fieldElements = [];
        $fieldNodes = $this->getElements(ParserConstants::ACAT_FIELD_NODE);

        foreach ($fieldNodes as $fieldNode) {
            $fieldElements[] = new FieldElement($fieldNode, true);
        }

        return $fieldElements;
    }

    /**
     * @return array
     * @throws ElementException
     */
    public function getConditionElements() : array
    {
        $conditionElements = [];
        $conditionNodes = $this->getElements(ParserConstants::ACAT_CONDITION_NODE);

        foreach ($conditionNodes as $conditionNode) {
            $conditionElements[] = new ConditionElement($conditionNode, true);
        }

        return $conditionElements;
    }

    /**
     * @param DOMNode|null $contextNode
     * @return ChildBlockElement
     * @throws ElementException
     */
    public function getClonedChildBlockElement(?DOMNode $contextNode = null) : ChildBlockElement
    {
        $clonedElement = $this->element->cloneNode(true);
        if (!DOMUtils::isRemoved($this->element) && $contextNode) {
            $clonedElement = $contextNode->parentNode->insertBefore($clonedElement, $contextNode);
        }
        return new ChildBlockElement($clonedElement);
    }

}