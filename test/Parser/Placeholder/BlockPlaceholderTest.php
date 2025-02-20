<?php

namespace Tests\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Exception\PlaceholderException;
use ACAT\Parser\Placeholder\EndBlockPlaceholder;
use ACAT\Parser\Placeholder\StartBlockPlaceholder;

class BlockPlaceholderTest extends TestCase
{

    /**
     * @throws DOMException
     * @throws PlaceholderException
     * @return void
     */
    #[Test]
    public function aStartBlockNodeCanBeCreated() : void
    {
        $startBlock = new StartBlockPlaceholder();
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
     * @return void
     * @throws DOMException
     * @throws PlaceholderException
     */
    public function aEndBlockNodeCanBeCreated() : void
    {
        $endBlock = new EndBlockPlaceholder();
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

}