<?php

namespace Tests\Parser\Element;

use DOMNodeList;
use ACAT\Parser\ParserConstants;
use ACAT\Exception\ElementException;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Exception\PlaceholderException;
use ACAT\Parser\Element\ConditionElement;

/**
 *
 */
class ConditionElementTest extends AbstractElementTest
{

    /**
     *
     * @throws ElementException
     *@return void
     */
    #[Test]
    public function aConditionElementCanBeCreated() : void
    {
        $wordContentPart = $this->getWordContentPart();
        $conditionNodes = $wordContentPart->getXPath()->query('//' . ParserConstants::ACAT_CONDITION_NODE);

        $this->assertInstanceOf(DOMNodeList::class, $conditionNodes);
        $this->assertEquals(14, $conditionNodes->length);

        $conditionElement = new ConditionElement($conditionNodes->item(0));
        $fieldId = $conditionNodes->item(0)->getAttribute('field');

        $this->assertInstanceOf(ConditionElement::class, $conditionElement);
        $this->assertEquals($fieldId, $conditionElement->getFieldId());
    }

    /**
     *
     * @throws ElementException
     *@return void
     */
    #[Test]
    public function aConditionElementHasAnAction() : void
    {
        $wordContentPart = $this->getWordContentPart();
        $conditionNodes = $wordContentPart->getXPath()->query('//' . ParserConstants::ACAT_CONDITION_NODE);

        $this->assertInstanceOf(DOMNodeList::class, $conditionNodes);
        $this->assertEquals(14, $conditionNodes->length);

        $conditionElement = new ConditionElement($conditionNodes->item(0));
        $action = $conditionNodes->item(0)->getAttribute('action');

        $this->assertInstanceOf(ConditionElement::class, $conditionElement);
        $this->assertEquals($action, $conditionElement->getAction());
    }

    /**
     *
     * @throws ElementException
     *@return void
     */
    #[Test]
    public function aConditionWithActionThrowsException() : void
    {
        $this->expectException(ElementException::class);

        $wordContentPart = $this->getWordContentPart();
        $conditionNodes = $wordContentPart->getXPath()->query('//' . ParserConstants::ACAT_CONDITION_NODE);

        $this->assertInstanceOf(DOMNodeList::class, $conditionNodes);
        $this->assertEquals(14, $conditionNodes->length);

        $conditionNodes->item(0)->removeAttribute('action');
        $conditionElement = new ConditionElement($conditionNodes->item(0));
        $this->assertInstanceOf(ConditionElement::class, $conditionElement);

        $conditionElement->getAction();
    }

    /**
     *
     * @throws ElementException
     * @throws PlaceholderException
     *@return void
     */
    #[Test]
    public function aConditionWithExpressionThrowsException() : void
    {
        $this->expectException(ElementException::class);

        $wordContentPart = $this->getWordContentPart();
        $conditionNodes = $wordContentPart->getXPath()->query('//' . ParserConstants::ACAT_CONDITION_NODE);

        $this->assertInstanceOf(DOMNodeList::class, $conditionNodes);
        $this->assertEquals(14, $conditionNodes->length);

        $conditionElement = new ConditionElement($conditionNodes->item(0));
        $this->assertInstanceOf(ConditionElement::class, $conditionElement);

        foreach ($conditionNodes->item(0)->childNodes as $node) {
            if ($node->nodeType == XML_CDATA_SECTION_NODE) {
                $node->parentNode->removeChild($node);
            }
        }

        $conditionElement->getExpression();
    }

    /**
     * @throws ElementException
     * @return void
     */
    #[Test] public function aConditionElementHasAnExpression() : void
    {
        $expression = null;

        $wordContentPart = $this->getWordContentPart();
        $conditionNodes = $wordContentPart->getXPath()->query('//' . ParserConstants::ACAT_CONDITION_NODE);

        $this->assertInstanceOf(DOMNodeList::class, $conditionNodes);
        $this->assertEquals(14, $conditionNodes->length);

        $conditionElement = new ConditionElement($conditionNodes->item(0));
        $this->assertInstanceOf(ConditionElement::class, $conditionElement);

        foreach ($conditionNodes->item(0)->childNodes as $node) {
            if ($node->nodeType == XML_CDATA_SECTION_NODE) {
                $expression = $node->data;
            }
        }

        $this->assertNotNull($expression);
        $this->assertEquals($expression, $conditionElement->getExpression());
    }


}