<?php

namespace ACAT\Render\Condition\Action;

use ACAT\Utils\DOMUtils;
use ACAT\Exception\RenderException;

/**
 *
 */
class DeleteRestAction extends ConditionAction
{

    /**
     * @return void
     * @throws RenderException
     */
    public function execute() : void
    {
        $parentRunNode = DOMUtils::getParentNode($this->conditionElement->getElement(), 'w:r');

        if ($parentRunNode) {
            $elements = $this->conditionElement->getXPath()->query("following-sibling::*", $parentRunNode);
            foreach ($elements as $element) {
                $removedNode = $element->parentNode->removeChild($element);
                if ($removedNode != $element) {
                    throw new RenderException(
                        'error while executing delete rest action -> could not delete node ' . $element->nodeName
                    );
                }
            }
        }
    }
}