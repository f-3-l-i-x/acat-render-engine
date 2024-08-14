<?php

namespace ACAT\Render\Condition\Action;

use ACAT\Utils\DOMUtils;
use ACAT\Exception\RenderException;

/**
 *
 */
class DeleteNextElementAction extends ConditionAction
{

    /**
     * @var string
     */
    private string $query = './/acat:field|.//acat:text|.//acat:view';

    /**
     * @return void
     * @throws RenderException
     */
    public function execute() : void
    {
        $nodeToDelete = null;

        $element = $this->conditionElement->getElement();
        $elements = $this->conditionElement->getXPath()->query($this->query, $element);

        if ($elements->length > 0) {
            $nodeToDelete = $elements->item(0);
        } else {
            $parentNode = DOMUtils::getParentNode($element, 'w:r');
            if (!$parentNode) {
                throw new RenderException($element->nodeName . ' has no parent w:r');
            }
            $rNodes = $this->conditionElement->getXPath()->query('following-sibling::w:r', $parentNode);
            foreach ($rNodes as $rNode) {
                $elements = $this->conditionElement->getXPath()->query($this->query, $rNode);
                if ($elements->length > 0) {
                    $nodeToDelete = $elements->item(0);
                    break;
                }
            }
        }

        $nodeToDelete?->parentNode->removeChild($nodeToDelete);
    }
}