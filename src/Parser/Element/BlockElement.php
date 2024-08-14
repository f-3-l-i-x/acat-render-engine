<?php

namespace ACAT\Parser\Element;

use DOMNode;
use Exception;
use DOMElement;
use ACAT\Exception\ElementException;

/**
 *
 */
abstract class BlockElement
{

    /**
     * @var DOMElement|DOMNode
     */
    protected DOMElement | DOMNode $end;

    /**
     * @var DOMElement|DOMNode
     */
    protected DOMElement | DOMNode $start;

    /**
     * @var array
     */
    protected array $ids = [];

    /**
     * @var array
     */
    protected array $fieldIds = [];

    /**
     * @var array
     */
    protected array $children = [];

    /**
     * @var array
     */
    protected array $textElements = [];

    /**
     * @var array
     */
    protected array $viewElements = [];

    /**
     * @var array
     */
    protected array $fieldElements = [];

    /**
     * @var array
     */
    protected array $conditionElements = [];

    /**
     * @param DOMNode $start
     * @param DOMNode $end
     */
    public function __construct(DOMNode $start, DOMNode $end)
    {
        $this->end = $end;
        $this->start = $start;
    }

    /**
     * @return DOMNode
     */
    public function getEnd() : DOMNode
    {
        return $this->end;
    }

    /**
     * @param DOMNode $end
     */
    public function setEnd(DOMNode $end) : void
    {
        $this->end = $end;
    }

    /**
     * @return DOMNode
     */
    public function getStart() : DOMNode
    {
        return $this->start;
    }

    /**
     * @param DOMNode $start
     */
    public function setStart(DOMNode $start) : void
    {
        $this->start = $start;
    }

    /**
     * @return array
     */
    public function getChildren() : array
    {
        return $this->children;
    }

    /**
     * @param array $children
     * @throws Exception
     */
    public function setChildren(array $children) : void
    {
        $this->children = $children;
    }

    /**
     * @return array
     */
    public function getFieldIds() : array
    {
        if (empty($this->fieldIds)) {
            try {
                foreach ($this->getFieldElements() as $fieldElement) {
                    $this->fieldIds['fields'][] = $fieldElement->getFieldId();
                }
                foreach ($this->getConditionElements() as $conditionElement) {
                    $this->fieldIds['fields'][] = $conditionElement->getFieldId();
                }
                foreach ($this->getViewElements() as $viewElement) {
                    $this->fieldIds['views'][] = $viewElement->getFieldId();
                }
            } //@todo logging or throwing?
            catch (ElementException $e) {
                //Logging::getFormLogger()->warn($e);
            }
            foreach ($this->fieldIds as $key => $value) {
                $this->fieldIds[$key] = array_unique($value, SORT_NUMERIC);
            }
        }

        return $this->fieldIds;
    }

    /**
     * @return array
     */
    public function getFieldElements() : array
    {
        return $this->fieldElements;
    }

    /**
     * @param array $fieldElements
     */
    public function setFieldElements(array $fieldElements) : void
    {
        $this->fieldElements = $fieldElements;
    }

    /**
     * @return array
     */
    public function getConditionElements() : array
    {
        return $this->conditionElements;
    }

    /**
     * @param array $conditionElements
     */
    public function setConditionElements(array $conditionElements) : void
    {
        $this->conditionElements = $conditionElements;
    }

    /**
     * @return array
     */
    public function getViewElements() : array
    {
        return $this->viewElements;
    }

    /**
     * @param array $viewElements
     */
    public function setViewElements(array $viewElements) : void
    {
        $this->viewElements = $viewElements;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getElementIds() : array
    {
        if (empty($this->ids)) {
            try {
                foreach ($this->getFieldElements() as $fieldElement) {
                    $this->ids[] = $fieldElement->getId();
                }
                foreach ($this->getConditionElements() as $conditionElement) {
                    $this->ids[] = $conditionElement->getId();
                }
                foreach ($this->getViewElements() as $viewElement) {
                    $this->ids[] = $viewElement->getId();
                }
            } catch (ElementException $e) {
                //@todo handle excpetion
                //Logging::getFormLogger()->warn($e);
            }
        }

        return $this->ids;
    }

    /**
     * @return array
     */
    public function getTextElements() : array
    {
        return $this->textElements;
    }

    /**
     * @param array $textElements
     */
    public function setTextElements(array $textElements) : void
    {
        $this->textElements = $textElements;
    }

    /**
     * @return string
     */
    public function getStartId() : string
    {
        return $this->start->getAttribute('id');
    }

    /**
     * @return string
     */
    public function getEndId() : string
    {
        return $this->end->getAttribute('id');
    }

    /**
     * @return DOMNode
     */
    abstract public function getContextNode() : DOMNode;

}