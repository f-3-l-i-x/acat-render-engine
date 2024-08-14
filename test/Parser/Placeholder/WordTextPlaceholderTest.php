<?php

namespace Tests\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;
use PHPUnit\Framework\TestCase;
use ACAT\Parser\Placeholder\WordTextPlaceholder;

class WordTextPlaceholderTest extends TestCase
{

    /**
     * @test
     * @throws DOMException
     */
    public function aNodeCanBeCreated() : void
    {
        $xmlOutput = '<w:t xml:space="preserve">ff</w:t>';

        $wordNode = new WordTextPlaceholder('ff');

        $this->assertInstanceOf(WordTextPlaceholder::class, $wordNode);
        $this->assertNotNull($wordNode->getText());
        $this->assertIsString($wordNode->getText());
        $this->assertNotNull($wordNode->getXMLTagAsString());
        $this->assertStringContainsString($wordNode->getText(), $wordNode->getXMLTagAsString());

        $domDocument = new DOMDocument('1.0', "UTF-8");

        $node = $wordNode->getDOMNode($domDocument);

        $this->assertInstanceOf(DOMNode::class, $node);
        $this->assertEquals($xmlOutput, $domDocument->saveXML($node));
    }

}