<?php

namespace ACAT\Render\Condition\Action;

use DOMNode;
use ACAT\Utils\DOMUtils;
use ACAT\Exception\RenderException;

/**
 *
 */
class DeleteParagraphAction extends ConditionAction
{

    /**
     * @return void
     * @throws RenderException
     */
    public function execute() : void
    {
        $paragraph = DOMUtils::getParentNode($this->conditionElement->getElement(), 'w:p');

        if ($paragraph && !DOMUtils::isRemoved($paragraph)) {
            if ((int)$this->conditionElement->getXPath()
                                            ->evaluate(
                                                'count(.//w:p)',
                                                $paragraph->parentNode
                                            ) == 1 && $paragraph->parentNode->nodeName == 'w:tc') {
                $this->deleteParagraphInaCell($paragraph);
            } else {
                $deletedNode = $paragraph->parentNode->removeChild($paragraph);
                if (!$paragraph->isSameNode($deletedNode)) {
                    throw new RenderException($paragraph->nodeName . ' could not removed');
                }
            }
        }
    }

    /**
     * @param DOMNode $paragraphNode
     * @see http://officeopenxml.com/WPtableCell.php
     */
    public function deleteParagraphInaCell(DOMNode $paragraphNode) : void
    {
        if ($paragraphNode->nodeName == 'w:p') {
            while ($paragraphNode->hasChildNodes()) {
                $paragraphNode->removeChild($paragraphNode->firstChild);
            }
        }
    }

}