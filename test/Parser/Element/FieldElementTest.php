<?php

namespace Tests\Parser\Element;

use DOMNode;
use DOMNodeList;
use ACAT\Parser\ParserConstants;
use ACAT\Exception\ElementException;
use ACAT\Parser\Element\FieldElement;
use PHPUnit\Framework\Attributes\Test;

/**
 *
 */
class FieldElementTest extends AbstractElementTest
{

    /**
     * @throws ElementException
     * @return void
     */
    #[Test]
    public function aFieldElementCanBeCreated() : void
    {
        $wordContentPart = $this->getWordContentPart();
        $fieldNodes = $wordContentPart->getXPath()->query('//' . ParserConstants::ACAT_FIELD_NODE);

        $this->assertInstanceOf(DOMNodeList::class, $fieldNodes);
        $this->assertEquals(29, $fieldNodes->length);

        $fieldElement = new FieldElement($fieldNodes->item(0));
        $fieldId = $fieldNodes->item(0)->getAttribute('field');

        $this->assertInstanceOf(FieldElement::class, $fieldElement);
        $this->assertEquals($fieldId, $fieldElement->getFieldId());
    }

    /**
     *
     * @throws ElementException
     *@return void
     */
    #[Test]
    public function getElementFromFieldElement() : void
    {
        $wordContentPart = $this->getWordContentPart();
        $fieldNodes = $wordContentPart->getXPath()->query('//' . ParserConstants::ACAT_FIELD_NODE);

        $this->assertInstanceOf(DOMNodeList::class, $fieldNodes);
        $this->assertEquals(29, $fieldNodes->length);

        $fieldElement = new FieldElement($fieldNodes->item(0));
        $this->assertInstanceOf(DOMNode::class, $fieldElement->getElement());
    }

    /**
     * @throws ElementException
     * @return void
     */
    #[Test]
    public function aFieldElementWithoutFieldIdThrowsAnException() : void
    {
        $this->expectException(ElementException::class);

        $wordContentPart = $this->getWordContentPart();
        $fieldNodes = $wordContentPart->getXPath()->query('//' . ParserConstants::ACAT_FIELD_NODE);

        $this->assertInstanceOf(DOMNodeList::class, $fieldNodes);
        $this->assertEquals(29, $fieldNodes->length);

        $fieldElement = new FieldElement($fieldNodes->item(0));
        $fieldNodes->item(0)->removeAttribute('field');

        $fieldElement->getFieldId();
    }

}