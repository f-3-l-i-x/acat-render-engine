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
     * @param $fieldId
     * @param $action
     * @param $expression
     * @throws PlaceholderException
     */
    public function __construct($fieldId, $action, $expression)
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
        foreach ($this->availableExpressions as $availableExpression) {
            if (str_contains($expression, $availableExpression)) {
                $this->expression = $expression;
                return;
            }
        }

        throw new PlaceholderException('expression ' . $expression . ' is not implemented');
    }

    /**
     * Builds the "acat:condition" DOM element carrying the field id, action
     * and expression.
     *
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
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
    public function getXMLTagAsString(string $prefix = 'acat'): string
    {
        $tag = "<{$prefix}:condition field=" . $this->getFieldId() . " id=" . $this->getId(
        ) . " action=" . $this->action . ">";
        $tag .= $this->expression;
        $tag .= "/>";

        return $tag;
    }

    /**
     * Returns the length of the original "${C:...}" marker text.
     *
     * @return int
     */
    public function length(): int
    {
        return strlen($this->getNodeAsString());
    }

    /**
     * Returns the original "${C:...}" marker text.
     *
     * @return string
     */
    public function getNodeAsString(): string
    {
        return '${C:' . $this->fieldId . ':' . $this->expression . ':' . $this->action . '}';
    }
}
