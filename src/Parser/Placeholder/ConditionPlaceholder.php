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
     * ConditionNode constructor.
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
     * @return string
     */
    public function getExpression() : string
    {
        return $this->expression;
    }

    /**
     * @param string $expression
     * @return void
     * @throws PlaceholderException
     */
    public function setExpression(string $expression) : void
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
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
    public function getDOMNode(DOMDocument $domDocument) : DOMNode
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
     * @return string
     */
    public function getFieldId() : string
    {
        return $this->fieldId;
    }

    /**
     * @return string
     */
    public function getAction() : string
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return void
     * @throws PlaceholderException
     */
    public function setAction(string $action) : void
    {
        if (!in_array($action, $this->availableActions)) {
            throw new PlaceholderException("action $action is not implemented");
        }

        $this->action = $action;
    }

    /**
     * @param string $prefix
     * @return string
     */
    #[Pure]
    public function getXMLTagAsString(string $prefix = 'acat') : string
    {
        $tag = "<acat:condition field=" . $this->getFieldId() . " id=" . $this->getId(
            ) . " action=" . $this->action . ">";
        $tag .= $this->expression;
        $tag .= "/>";

        return $tag;
    }

    /**
     * @return int
     */
    public function length() : int
    {
        return strlen($this->getNodeAsString());
    }

    /**
     * @return string
     */
    public function getNodeAsString() : string
    {
        return '${C:' . $this->fieldId . ':' . $this->expression . ':' . $this->action . '}';
    }

}