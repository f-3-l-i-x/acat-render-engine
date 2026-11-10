<?php

namespace Tests\Render\Element;

use DOMException;
use PHPUnit\Framework\TestCase;
use ACAT\Parser\Tag\TagGenerator;
use ACAT\Document\Word\ContentPart;
use ACAT\Exception\ElementException;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Parser\Normalizer\Normalizer;
use ACAT\Parser\Element\ElementGenerator;
use ACAT\Render\Element\ViewElementRender;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 *
 */
class ViewElementRenderTest extends TestCase
{

    /**
     * @return array
     */
    public static function viewValues() : array
    {
        $text = fn(string $text) : string => '<w:t xml:space="preserve">' . $text . '</w:t>';

        return [
            'text'                    => ['Zeile', $text('Zeile')],
            'line break'              => ['a<w:br/>b', $text('a') . '<w:br/>' . $text('b')],
            'consecutive line breaks' => ['a<w:br/><w:br/>b', $text('a') . '<w:br/><w:br/>' . $text('b')],
            'trailing line break'     => ['a<w:br/>', $text('a') . '<w:br/>'],
            'leading line break'      => ['<w:br/>a', '<w:br/>' . $text('a')],
            'zero'                    => ['0', $text('0')],
            'zero after line break'   => ['a<w:br/>0', $text('a') . '<w:br/>' . $text('0')],
            'number'                  => [4, $text('4')],
            'empty'                   => ['', ''],
            'null'                    => [null, ''],
        ];
    }

    /**
     * @param mixed $value
     * @param string $expected
     * @return void
     * @throws DOMException
     * @throws ElementException
     */
    #[Test]
    #[DataProvider('viewValues')]
    public function aViewCanBeRendered(mixed $value, string $expected) : void
    {
        $contentPart = $this->getContentPart();
        $elementGenerator = new ElementGenerator($contentPart);

        $viewElementRender = new ViewElementRender();
        $viewElementRender->render($elementGenerator->getViewElements(), ['V_TEST' => $value]);

        $this->assertSame('<w:r>' . $expected . '</w:r>', $this->getRunContent($contentPart));
    }

    /**
     * @return void
     * @throws DOMException
     * @throws ElementException
     */
    #[Test]
    public function aViewWithoutValueIsRemoved() : void
    {
        $contentPart = $this->getContentPart();
        $elementGenerator = new ElementGenerator($contentPart);

        $viewElementRender = new ViewElementRender();
        $viewElementRender->render($elementGenerator->getViewElements());

        $this->assertSame('<w:r></w:r>', $this->getRunContent($contentPart));
    }

    /**
     * @return ContentPart
     */
    private function getContentPart() : ContentPart
    {
        $contentPart = new ContentPart(
            '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body>'
            . '<w:p><w:r><w:t>${V:V_TEST}</w:t></w:r></w:p>'
            . '</w:body></w:document>',
            'word/document.xml'
        );

        new Normalizer()->normalize($contentPart);
        new TagGenerator($contentPart)->generateTags();

        return $contentPart;
    }

    /**
     * @param ContentPart $contentPart
     * @return string
     */
    private function getRunContent(ContentPart $contentPart) : string
    {
        $run = $contentPart->getXPath()->query('//w:r')->item(0);

        return str_replace('<w:r/>', '<w:r></w:r>', $contentPart->getDomDocument()->saveXML($run));
    }

}
