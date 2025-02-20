<?php

namespace Tests\Render\Element;

use DOMNodeList;
use DOMException;
use ACAT\Parser\ParserConstants;
use ACAT\Exception\RenderException;
use ACAT\Render\Element\TextRender;
use ACAT\Exception\ElementException;
use ACAT\Parser\Element\TextElement;
use Tests\Render\AbstractRenderTest;
use PHPUnit\Framework\Attributes\Test;

/**
 *
 */
class TextRenderTest extends AbstractRenderTest
{

    /**
     * @return void
     */
    #[Test]
    public function aTextRenderCanBeCreated() : void
    {
        $textRender = new TextRender();
        $this->assertInstanceOf(TextRender::class, $textRender);
    }

    /**
     * @throws ElementException
     * @throws RenderException
     * @return void
     */
    #[Test]
    public function textFieldsCanBeRendered() : void
    {
        $wordElementGenerator = $this->getWordElementGenerator();

        $textRender = new TextRender();
        $this->assertInstanceOf(TextRender::class, $textRender);

        $textRender->render($wordElementGenerator->getTextElements());

        $acatTextNodes = $wordElementGenerator->getContentPart()->getXPath()->query(
            '//' . ParserConstants::ACAT_TEXT_NODES
        );
        $this->assertInstanceOf(DOMNodeList::class, $acatTextNodes);
        $this->assertEquals(0, $acatTextNodes->length);
    }

    /**
     * @throws DOMException
     * @throws ElementException
     * @return void
     */
    #[Test]
    public function aFieldCanBeRendered() : void
    {
        $wordElementGenerator = $this->getWordElementGenerator();

        $textRender = new TextRender();
        $this->assertInstanceOf(TextRender::class, $textRender);

        $textNodes = $wordElementGenerator->getContentPart()->getXPath()->query(
            '//' . ParserConstants::ACAT_TEXT_NODES
        );
        $this->assertInstanceOf(DOMNodeList::class, $textNodes);
        $this->assertEquals(2, $textNodes->length);

        foreach ($textNodes as $textNode) {
            $textRender->renderTextElement(new TextElement($textNode));
        }

        $textNodes = $wordElementGenerator->getContentPart()->getXPath()->query(
            '//' . ParserConstants::ACAT_TEXT_NODES
        );
        $this->assertInstanceOf(DOMNodeList::class, $textNodes);
        $this->assertEquals(0, $textNodes->length);
    }

}