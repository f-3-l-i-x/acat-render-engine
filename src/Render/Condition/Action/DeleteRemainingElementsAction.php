<?php

namespace ACAT\Render\Condition\Action;

/**
 * deletes all field, text and view elements behind the condition element in its paragraph
 */
class DeleteRemainingElementsAction extends ConditionAction
{
    /**
     * @return void
     */
    #[\Override]
    public function execute(): void
    {
        foreach ($this->getFollowingElements() as $node) {
            $node->parentNode?->removeChild($node);
        }
    }
}
