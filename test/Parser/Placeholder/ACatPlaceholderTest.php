<?php

namespace Tests\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Exception\PlaceholderException;
use ACAT\Parser\Placeholder\ACatPlaceholder;
use ACAT\Parser\Placeholder\TextPlaceholder;
use ACAT\Parser\Placeholder\FieldPlaceholder;
use ACAT\Parser\Placeholder\EndBlockPlaceholder;
use ACAT\Parser\Placeholder\ConditionPlaceholder;
use ACAT\Parser\Placeholder\StartBlockPlaceholder;

/**
 *
 */
class ACatPlaceholderTest extends TestCase
{

    /**
     *
     * @throws PlaceholderException
     *@return void
     */
    #[Test]
    public function getFieldPlaceholder() : void
    {
        $fieldNodeString = "\${F:1234}";
        $fieldNode = ACatPlaceholder::getPlaceholder($fieldNodeString);

        $this->assertInstanceOf(FieldPlaceholder::class, $fieldNode);

        $this->assertNotNull($fieldNode->getId());
        $this->assertEquals('1234', $fieldNode->getFieldId());
        $this->assertNotNull($fieldNode->getXMLTagAsString());
        $this->assertIsString($fieldNode->getXMLTagAsString());
        $this->assertStringContainsString('acat', $fieldNode->getXMLTagAsString());
    }

    /**
     * @throws PlaceholderException|DOMException
     */
    #[Test]
    public function getTextPlaceholder() : void
    {
        $text = 'Muh Mäh';
        $fieldNodeString = "\${T:" . $text . "}";

        $textNode = ACatPlaceholder::getPlaceholder($fieldNodeString);

        $this->assertInstanceOf(TextPlaceholder::class, $textNode);
        $this->assertNotNull($textNode->getText());
        $this->assertNotNull($textNode->getXMLTagAsString());
        $this->assertIsString($textNode->getXMLTagAsString());
        $this->assertStringContainsString($text, $textNode->getXMLTagAsString());

        $domDocument = new DOMDocument('1.0', "UTF-8");

        $node = $textNode->getDOMNode($domDocument);

        $this->assertInstanceOf(DOMNode::class, $node);
        $this->assertStringContainsString('acat', $node->prefix);

        $expectedXmlString = '<acat:text xmlns:acat="https://schemas.acat.akademie.uni-bremen.de" id="' . $textNode->getId(
            ) . '" space="preserve">' . $text . '</acat:text>';
        $this->assertEquals($expectedXmlString, $domDocument->saveXML($node));
    }

    /**
     *
     * @throws PlaceholderException
     */
    #[Test]
    public function getConditionPlaceholder() : void
    {
        $conditionNodeString = "\${C:567:<>:1}";
        $conditionNode = ACatPlaceholder::getPlaceholder($conditionNodeString);

        $this->assertInstanceOf(ConditionPlaceholder::class, $conditionNode);

        $this->assertNotNull($conditionNode->getId());
        $this->assertNotNull($conditionNode->getAction());
        $this->assertNotNull($conditionNode->getExpression());
        $this->assertNotNull($conditionNode->getXMLTagAsString());

        $this->assertIsString($conditionNode->getId());
        $this->assertIsString($conditionNode->getAction());
        $this->assertIsString($conditionNode->getExpression());
        $this->assertIsString($conditionNode->getXMLTagAsString());

        $this->assertEquals('567', $conditionNode->getFieldId());
        $this->assertEquals('1', $conditionNode->getAction());
        $this->assertEquals('<>', $conditionNode->getExpression());

        $this->assertStringContainsString('acat', $conditionNode->getXMLTagAsString('acat'));
    }

    /**
     * @throws DOMException
     * @throws PlaceholderException
     * @return void
     */
    #[Test]
    public function getStartBlockPlaceholder() : void
    {
        $startBlockNodeString = "\${B:0}";
        $startBlock = ACatPlaceholder::getPlaceholder($startBlockNodeString);

        $expectedXmlString = '<acat:block xmlns:acat="https://schemas.acat.akademie.uni-bremen.de" id="' . $startBlock->getId(
            ) . '" type="start"/>';

        $this->assertInstanceOf(StartBlockPlaceholder::class, $startBlock);
        $this->assertIsString($startBlock->getType());
        $this->assertEquals('start', $startBlock->getType());

        $domDocument = new DOMDocument('1.0', "UTF-8");

        $node = $startBlock->getDOMNode($domDocument);

        $this->assertInstanceOf(DOMNode::class, $node);
        $this->assertStringContainsString('acat', $node->prefix);

        $this->assertEquals($expectedXmlString, $domDocument->saveXML($node));
    }

    /**
     *
     * @throws PlaceholderException
     * @throws DOMException
     */
    #[Test]
    public function getEndBlockPlaceholder() : void
    {
        $endBlockNodeString = "\${B:1}";
        $endBlock = ACatPlaceholder::getPlaceholder($endBlockNodeString);

        $expectedXmlString = '<acat:block xmlns:acat="https://schemas.acat.akademie.uni-bremen.de" id="' . $endBlock->getId(
            ) . '" type="end"/>';

        $this->assertInstanceOf(EndBlockPlaceholder::class, $endBlock);
        $this->assertIsString($endBlock->getType());
        $this->assertEquals('end', $endBlock->getType());

        $domDocument = new DOMDocument('1.0', "UTF-8");

        $node = $endBlock->getDOMNode($domDocument);

        $this->assertInstanceOf(DOMNode::class, $node);
        $this->assertStringContainsString('acat', $node->prefix);

        $this->assertEquals($expectedXmlString, $domDocument->saveXML($node));
    }

    #[Test]
    public function aBlockPlaceholderWithAUnsupportedTypeCanNotBeCreated() : void
    {
        $this->expectException(PlaceholderException::class);

        $blockNodeString = "\${B:1234}";
        ACatPlaceholder::getPlaceholder($blockNodeString);
    }

    #[Test]
    public function aPlaceholderWithMalformedStringCanNotBeCreated() : void
    {
        $this->expectException(PlaceholderException::class);

        $fieldNodeString = "\${X:1234}";
        ACatPlaceholder::getPlaceholder($fieldNodeString);
    }

    #[Test]
    public function aPlaceholderWithUnsupportedTypeCanNoBeCreated() : void
    {
        $this->expectException(PlaceholderException::class);
        ACatPlaceholder::getPlaceholder('FOO');
    }

}