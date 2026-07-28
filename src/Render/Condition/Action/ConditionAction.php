<?php

namespace ACAT\Render\Condition\Action;

use ACAT\Exception\RenderException;
use ACAT\Exception\ElementException;
use ACAT\Parser\Element\ConditionElement;

/**
 *
 */
abstract class ConditionAction
{
    /**
     * ConditionAction constructor.
     * @param ConditionElement $conditionElement
     */
    public function __construct(protected ConditionElement $conditionElement)
    {
    }

    /**
     * @var array|string[]
     */
    private static array $actions = [
        '0' => DeleteParagraphAction::class,
        '1' => DeleteRemainingElementsAction::class,
        '2' => DeleteNextElementAction::class,
        '3' => DeleteRestAction::class,
        '4' => DeleteUntilNextElementAction::class,
    ];

    /**
     * @param ConditionElement $conditionElement
     * @return ConditionAction
     * @throws RenderException
     * @throws ElementException
     */
    public static function getAction(ConditionElement $conditionElement): ConditionAction
    {
        $actionClass = self::$actions[$conditionElement->getAction()] ?? null;

        if (!$actionClass) {
            throw new RenderException('unsupported action');
        }

        return new $actionClass($conditionElement);
    }

    /**
     *
     */
    abstract public function execute(): void;
}
