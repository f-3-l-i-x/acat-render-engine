<?php

namespace ACAT\Render\Condition\Action;

use DOMElement;
use ACAT\Utils\DOMUtils;
use ACAT\Parser\ParserConstants;
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

    /**
     * Returns the field, text and view elements behind the condition element in document order. It does not matter
     * whether an element is in the run of the condition element or in one of the following runs. Only the paragraph
     * of the condition element is searched, without a paragraph its run.
     *
     * @return DOMElement[]
     */
    protected function getFollowingElements(): array
    {
        $element = $this->conditionElement->getElement();

        $contextNode = DOMUtils::getParentNode($element, 'w:p')
            ?? DOMUtils::getParentNode($element, 'w:r')
            ?? $element->parentNode;

        if (!$contextNode) {
            return [];
        }

        // a single location path, so the nodes are in document order
        $nodes = $this->conditionElement->getXPath()->query(
            './/*[self::acat:condition or self::acat:field or self::acat:text or self::acat:view]',
            $contextNode
        );

        $found = false;
        $elements = [];

        foreach ($nodes as $node) {
            if ($node->isSameNode($element)) {
                $found = true;
            } elseif ($found && $node->nodeName !== ParserConstants::ACAT_CONDITION_NODE) {
                $elements[] = $node;
            }
        }

        return $elements;
    }
}
