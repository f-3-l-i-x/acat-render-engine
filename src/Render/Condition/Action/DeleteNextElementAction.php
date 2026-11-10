<?php

namespace ACAT\Render\Condition\Action;

/**
 * deletes the next field, text or view element behind the condition element in its paragraph
 */
class DeleteNextElementAction extends ConditionAction
{
    /**
     * @return void
     */
    #[\Override]
    public function execute(): void
    {
        $nodeToDelete = array_first($this->getFollowingElements());

        $nodeToDelete?->parentNode?->removeChild($nodeToDelete);
    }
}
