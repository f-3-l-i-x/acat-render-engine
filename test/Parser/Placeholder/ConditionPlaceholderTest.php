<?php

namespace Tests\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Exception\PlaceholderException;
use ACAT\Parser\Placeholder\ConditionPlaceholder;

class ConditionPlaceholderTest extends TestCase
{

    /**
     * @throws DOMException
     * @throws PlaceholderException
     * @return void
     */
    #[Test]
    public function aConditionNodeCanBeCreated() : void
    {
        $conditionNode = new ConditionPlaceholder(1, 2, '<>2');
        $xmlOutput = '<acat:condition xmlns:acat="https://schemas.acat.akademie.uni-bremen.de" id="' . $conditionNode->getId(
            ) . '" field="' . $conditionNode->getFieldId() . '" action="2"><![CDATA[<>2]]></acat:condition>';

        $this->assertInstanceOf(ConditionPlaceholder::class, $conditionNode);

        $this->assertNotNull($conditionNode->getId());
        $this->assertNotNull($conditionNode->getAction());
        $this->assertNotNull($conditionNode->getExpression());
        $this->assertNotNull($conditionNode->getXMLTagAsString());

        $this->assertIsString($conditionNode->getId());
        $this->assertIsString($conditionNode->getAction());
        $this->assertIsString($conditionNode->getExpression());
        $this->assertIsString($conditionNode->getXMLTagAsString());

        $domDocument = new DOMDocument('1.0', "UTF-8");

        $node = $conditionNode->getDOMNode($domDocument);

        $this->assertInstanceOf(DOMNode::class, $node);
        $this->assertStringContainsString('acat', $node->prefix);

        $this->assertEquals($xmlOutput, $domDocument->saveXML($node));
    }

    /**
     * @throws PlaceholderException
     * @return void
     */
    #[Test]
    public function aConditionNodeCanBeCreateWithWrongAction() : void
    {
        $this->expectException(PlaceholderException::class);
        new ConditionPlaceholder(1, 5, '<>2');
    }

    /**
     * @throws PlaceholderException
     * @return void
     */
    #[Test]
    public function aConditionNodeCanNotBeCreateWithWrongExpression() : void
    {
        $this->expectException(PlaceholderException::class);
        new ConditionPlaceholder(1, 1, '!2');
    }

}