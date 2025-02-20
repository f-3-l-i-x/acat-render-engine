<?php

namespace Tests\Parser\Element;

use DOMNodeList;
use ACAT\Parser\ParserConstants;
use ACAT\Exception\ElementException;
use ACAT\Parser\Element\TextElement;
use PHPUnit\Framework\Attributes\Test;

/**
 *
 */
class TextElementTest extends AbstractElementTest
{

    /**
     * @throws ElementException
     * @return void
     */
    #[Test]
    public function aTextElementCanBeCreated() : void
    {
        $wordContentPart = $this->getWordContentPart();
        $fieldNodes = $wordContentPart->getXPath()->query('//' . ParserConstants::ACAT_TEXT_NODES);

        $this->assertInstanceOf(DOMNodeList::class, $fieldNodes);
        $this->assertEquals(3, $fieldNodes->length);

        $fieldElement = new TextElement($fieldNodes->item(0));
        $nodeValue = $fieldNodes->item(0)->nodeValue;

        $this->assertInstanceOf(TextElement::class, $fieldElement);
        $this->assertEquals($nodeValue, $fieldElement->getText());
    }

    /**
     *
     * @throws ElementException
     *@return void
     */
    #[Test]
    public function aTextElementFieldIdThrowsAppException() : void
    {
        $this->expectException(ElementException::class);

        $wordContentPart = $this->getWordContentPart();
        $fieldNodes = $wordContentPart->getXPath()->query('//' . ParserConstants::ACAT_TEXT_NODES);

        $this->assertInstanceOf(DOMNodeList::class, $fieldNodes);
        $this->assertEquals(3, $fieldNodes->length);

        $fieldElement = new TextElement($fieldNodes->item(0));
        $this->assertInstanceOf(TextElement::class, $fieldElement);

        $fieldElement->getFieldId();
    }

}