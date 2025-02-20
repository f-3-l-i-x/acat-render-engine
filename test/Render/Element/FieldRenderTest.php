<?php

namespace Tests\Render\Element;

use DOMNodeList;
use DOMException;
use ACAT\Parser\ParserConstants;
use ACAT\Exception\RenderException;
use ACAT\Exception\ElementException;
use ACAT\Render\Element\FieldRender;
use Tests\Render\AbstractRenderTest;
use ACAT\Parser\Element\FieldElement;
use PHPUnit\Framework\Attributes\Test;

/**
 *
 */
class FieldRenderTest extends AbstractRenderTest
{

    /**
     *
     * @return void
     */
    #[Test]
    public function aFieldRenderCanBeCreated() : void
    {
        $fieldRender = new FieldRender();
        $this->assertInstanceOf(FieldRender::class, $fieldRender);
    }

    /**
     *
     * @throws DOMException
     * @throws ElementException
     * @throws RenderException
     *@return void
     */
    #[Test]
    public function fieldsCanBeRendered() : void
    {
        $values = $this->getValues();
        $elementGenerator = $this->getWordElementGenerator();

        $fieldRender = new FieldRender();
        $this->assertInstanceOf(FieldRender::class, $fieldRender);

        $fieldRender->render($elementGenerator->getFieldElements(), $values);
        $acatFieldNodes = $elementGenerator->getContentPart()->getXPath()->query(
            '//' . ParserConstants::ACAT_FIELD_NODE
        );

        $this->assertInstanceOf(DOMNodeList::class, $acatFieldNodes);
        $this->assertEquals(9, $acatFieldNodes->length);
    }

    /**
     *
     * @throws ElementException
     * @throws RenderException
     * @throws DOMException
     *@return void
     */
    #[Test]
    public function aFieldCanBeRendered() : void
    {
        $values = $this->getValues();
        $elementGenerator = $this->getWordElementGenerator();

        $fieldRender = new FieldRender();
        $this->assertInstanceOf(FieldRender::class, $fieldRender);

        $fieldNodes = $elementGenerator->getContentPart()->getXPath()->query('//' . ParserConstants::ACAT_FIELD_NODE);
        $this->assertInstanceOf(DOMNodeList::class, $fieldNodes);
        $this->assertEquals(29, $fieldNodes->length);

        foreach ($fieldNodes as $fieldNode) {
            $fieldRender->renderFieldElement(new FieldElement($fieldNode), $values);
        }

        $fieldNodes = $elementGenerator->getContentPart()->getXPath()->query('//' . ParserConstants::ACAT_FIELD_NODE);
        $this->assertInstanceOf(DOMNodeList::class, $fieldNodes);
        $this->assertEquals(0, $fieldNodes->length);
    }


}