<?php

namespace ACAT\Parser\Element;

use ACAT\Exception\ElementException;

/**
 *
 */
class ViewElement extends Element
{
    /**
     * @return string
     * @throws ElementException
     */
    #[\Override]
    public function getFieldId(): string
    {
        $id = $this->getAttributeValue('view');
        if (empty($id)) {
            throw new ElementException($this->element->nodeName . ' does not contains a view id');
        }
        return $id;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getType(): string
    {
        return "VIEW";
    }
}
