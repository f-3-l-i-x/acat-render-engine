<?php

namespace ACAT\Render\Condition\Action;

use ACAT\Exception\RenderException;

/**
 *
 */
class DeleteRemainingElementsAction extends ConditionAction
{

    /**
     * @return void
     * @throws RenderException
     */
    public function execute() : void
    {
        $nodes = $this->conditionElement->getXpath()
                                        ->query(
                                            './/acat:field|.//acat:text|.//acat:view',
                                            $this->conditionElement->getElement()
                                        );
        foreach ($nodes as $node) {
            $deletedNode = $node->parentNode->removeChild($node);
            if (!$deletedNode->isSameNode($node)) {
                throw new RenderException($node->nodeName . ' could not removed');
            }
        }
    }
}