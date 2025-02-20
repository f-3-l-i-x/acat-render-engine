<?php

namespace Tests\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Parser\Placeholder\FieldPlaceholder;

class FieldPlaceholderTest extends TestCase
{

    /**
     * @throws DOMException
     * @return void
     */
    #[Test]
    public function aFieldNodeCanBeCreated() : void
    {
        $fieldNode = new FieldPlaceholder('12');
        $xmlOutput = '<acat:field xmlns:acat="https://schemas.acat.akademie.uni-bremen.de" id="' . $fieldNode->getId(
            ) . '" field="12"/>';

        $this->assertInstanceOf(FieldPlaceholder::class, $fieldNode);
        $this->assertNotNull($fieldNode->getId());
        $this->assertNotNull($fieldNode->getXMLTagAsString());
        $this->assertIsString($fieldNode->getXMLTagAsString());

        $this->assertEquals('${F:12}', $fieldNode->getNodeAsString());

        $domDocument = new DOMDocument('1.0', "UTF-8");

        $node = $fieldNode->getDOMNode($domDocument);

        $this->assertInstanceOf(DOMNode::class, $node);
        $this->assertStringContainsString('acat', $node->prefix);

        $this->assertEquals($xmlOutput, $domDocument->saveXML($node));
    }

}