<?php

namespace ACAT\Parser\Element;

use ACAT\Exception\ElementException;

/**
 *
 */
class ConditionElement extends Element
{

    /**
     * @return string
     * @throws ElementException
     */
    public function getAction() : string
    {
        $action = $this->getAttributeValue('action');
        if ($action == null || strlen($action) == 0) {
            throw new ElementException($this->getId() . ' does not contains an action');
        }
        return $action;
    }

    /**
     * @return string
     * @throws ElementException
     */
    public function getExpression() : string
    {
        foreach ($this->element->childNodes as $childNode) {
            if ($childNode->nodeType == XML_CDATA_SECTION_NODE) {
                return $childNode->data;
            }
        }
        throw new ElementException($this->getId() . ' does not contains an expression');
    }

}