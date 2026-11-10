<?php

namespace ACAT\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;
use JetBrains\PhpStorm\Pure;
use ACAT\Exception\PlaceholderException;

/**
 *
 */
class ConditionPlaceholder extends ACatPlaceholder
{
    /**
     * @var string
     */
    private string $fieldId;

    /**
     * @var string
     */
    private string $action;

    /**
     * @var string
     */
    private string $expression;

    /**
     * @var array|string[]
     */
    private array $availableExpressions = ['>', '<', '=', '<>'];

    /**
     * @var array|int[]
     */
    private array $availableActions = [0, 1, 2, 3, 4];

    /**
     * Creates a condition placeholder for the given field, action and
     * expression.
     *
     * @param string $fieldId
     * @param string $action
     * @param string $expression
     * @throws PlaceholderException
     */
    public function __construct(string $fieldId, string $action, string $expression)
    {
        if (empty($fieldId) || empty($expression)) {
            throw new PlaceholderException("invalid condition node $fieldId $action $expression");
        }

        $this->fieldId = $fieldId;
        $this->setAction($action);
        $this->setExpression($expression);

        parent::__construct();
    }

    /**
     * Returns the condition's expression (e.g. "<>0").
     *
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * Validates and sets the condition's expression, rejecting unsupported
     * operators.
     *
     * @param string $expression
     * @return void
     * @throws PlaceholderException
     */
    public function setExpression(string $expression): void
    {
        $isAvailable = array_any(
            $this->availableExpressions,
            fn(string $availableExpression): bool => str_contains($expression, $availableExpression)
        );

        if (!$isAvailable) {
            throw new PlaceholderException('expression ' . $expression . ' is not implemented');
        }

        $this->expression = $expression;
    }

    /**
     * Builds the "acat:condition" DOM element carrying the field id, action
     * and expression.
     *
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
    #[\Override]
    public function getDOMNode(DOMDocument $domDocument): DOMNode
    {
        $elementNode = $domDocument->createElementNS($this->namespace, 'acat:condition');
        $expressionNode = $domDocument->createCDATASection($this->expression);

        $idAttribute = $domDocument->createAttribute('id');
        $idAttribute->value = $this->getId();

        $fieldAttribute = $domDocument->createAttribute('field');
        $fieldAttribute->value = $this->getFieldId();

        $actionAttribute = $domDocument->createAttribute('action');
        $actionAttribute->value = $this->getAction();

        $elementNode->appendChild($idAttribute);
        $elementNode->appendChild($fieldAttribute);
        $elementNode->appendChild($actionAttribute);
        $elementNode->appendChild($expressionNode);

        return $elementNode;
    }

    /**
     * Returns the id of the field this condition evaluates.
     *
     * @return string
     */
    public function getFieldId(): string
    {
        return $this->fieldId;
    }

    /**
     * Returns the action to perform when the condition evaluates to true.
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Validates and sets the action to perform when the condition evaluates
     * to true.
     *
     * @param string $action
     * @return void
     * @throws PlaceholderException
     */
    public function setAction(string $action): void
    {
        if (!in_array($action, $this->availableActions)) {
            throw new PlaceholderException("action $action is not implemented");
        }

        $this->action = $action;
    }

    /**
     * Returns a human-readable representation of the condition marker, used
     * for logging.
     *
     * @param string $prefix
     * @return string
     */
    #[Pure]
    #[\Override]
    public function getXMLTagAsString(string $prefix = 'acat'): string
    {
        return '<' . $prefix . ':condition field="' . $this->getFieldId() . '" id="' . $this->getId()
            . '" action="' . $this->action . '">' . $this->expression . '</' . $prefix . ':condition>';
    }

    /**
     * Returns the length of the original "${C:...}" marker text.
     *
     * @return int
     */
    #[\Override]
    public function length(): int
    {
        return strlen($this->getNodeAsString());
    }

    /**
     * Returns the original "${C:...}" marker text.
     *
     * @return string
     */
    #[\Override]
    public function getNodeAsString(): string
    {
        return '${C:' . $this->fieldId . ':' . $this->expression . ':' . $this->action . '}';
    }
}
