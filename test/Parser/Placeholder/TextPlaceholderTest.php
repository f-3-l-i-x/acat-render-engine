<?php

namespace Tests\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;
use PHPUnit\Framework\TestCase;
use ACAT\Parser\Placeholder\TextPlaceholder;

class TextPlaceholderTest extends TestCase
{

    /**
     * @test
     * @throws DOMException
     */
    public function aTextNodeCanBeCreated() : void
    {
        $text = 'Ich bin der krasse Text';
        $textNode = new TextPlaceholder($text);

        $expectedXmlString = '<acat:text xmlns:acat="https://schemas.acat.akademie.uni-bremen.de" id="' . $textNode->getId(
            ) . '" space="preserve">' . $text . '</acat:text>';

        $this->assertInstanceOf(TextPlaceholder::class, $textNode);
        $this->assertNotNull($textNode->getText());
        $this->assertNotNull($textNode->getXMLTagAsString());
        $this->assertIsString($textNode->getXMLTagAsString());
        $this->assertStringContainsString($text, $textNode->getXMLTagAsString());

        $domDocument = new DOMDocument('1.0', "UTF-8");

        $node = $textNode->getDOMNode($domDocument);

        $this->assertInstanceOf(DOMNode::class, $node);
        $this->assertStringContainsString('acat', $node->prefix);

        $this->assertEquals($expectedXmlString, $domDocument->saveXML($node));
    }
}