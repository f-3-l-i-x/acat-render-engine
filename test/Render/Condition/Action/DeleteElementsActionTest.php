<?php

namespace Tests\Render\Condition\Action;

use PHPUnit\Framework\TestCase;
use ACAT\Parser\Tag\TagGenerator;
use ACAT\Document\Word\ContentPart;
use ACAT\Exception\RenderException;
use ACAT\Exception\ElementException;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Parser\Normalizer\Normalizer;
use ACAT\Parser\Element\ElementGenerator;
use ACAT\Render\Condition\ConditionRender;
use ACAT\Exception\ConditionParserException;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * tests of the actions which delete the next element (2) or the remaining elements (1) of a paragraph
 */
class DeleteElementsActionTest extends TestCase
{

    /**
     * every paragraph is a list of runs, the result contains the remaining text and elements of every paragraph
     *
     * @return array
     */
    public static function conditions() : array
    {
        return [
            'remaining: elements in the run of the condition'    => [
                [['A ${C:1:=:1}${F:5} B ${V:V_X} C']],
                ['A  B  C'],
            ],
            'remaining: elements in the following runs'          => [
                [['A ${C:1:=:1}', '${F:5} B', '${T:Text} C ${V:V_X}']],
                ['A  B C '],
            ],
            'remaining: elements before the condition are kept'  => [
                [['${F:4} A ${C:1:=:1} B ${F:5}']],
                ['[F:4] A  B '],
            ],
            'remaining: other paragraphs are kept'               => [
                [['${F:4}'], ['A ${C:1:=:1}${F:5}'], ['${F:6}']],
                ['[F:4]', 'A ', '[F:6]'],
            ],
            'remaining: following conditions are kept'           => [
                [['${C:1:=:1}${F:5}${C:2:<>:0} B'], ['C']],
                [' B', 'C'],
            ],
            'remaining: the condition is false'                  => [
                [['A ${C:1:<>:1}${F:5} B ${F:6}']],
                ['A [F:5] B [F:6]'],
            ],
            'next: element in the run of the condition'          => [
                [['A ${C:1:=:2}${F:5} B ${F:6}', '${F:7} C']],
                ['A  B [F:6][F:7] C'],
            ],
            'next: element in a following run'                   => [
                [['A ${C:1:=:2} B', 'C', '${V:V_X} D ${F:6}']],
                ['A  BC D [F:6]'],
            ],
            'next: elements before the condition are kept'       => [
                [['${F:4}', 'A ${C:1:=:2}', '${T:Text}${F:5}']],
                ['[F:4]A [F:5]'],
            ],
            'next: elements of the next paragraph are kept'      => [
                [['A ${C:1:=:2} B'], ['${F:5}']],
                ['A  B', '[F:5]'],
            ],
            'next: the condition is false'                       => [
                [['A ${C:1:<>:2}${F:5} B']],
                ['A [F:5] B'],
            ],
            'next: one element for every condition'              => [
                [['${C:1:=:2}${F:5}${C:1:=:2}${F:6}${F:7}']],
                ['[F:7]'],
            ],
        ];
    }

    /**
     * @param array $paragraphs
     * @param array $expected
     * @return void
     * @throws ConditionParserException
     * @throws ElementException
     * @throws RenderException
     */
    #[Test]
    #[DataProvider('conditions')]
    public function renderCondition(array $paragraphs, array $expected) : void
    {
        $contentPart = $this->getContentPart($paragraphs);
        $elementGenerator = new ElementGenerator($contentPart);

        // field 1 has no value: "=" is true, "<>" is false
        $conditionRender = new ConditionRender();
        $conditionRender->render($elementGenerator->getConditionElements());

        $this->assertSame($expected, $this->getParagraphs($contentPart));
        $this->assertCount(0, $contentPart->getXPath()->query('//acat:condition'));
    }

    /**
     * @param array $paragraphs
     * @return ContentPart
     */
    private function getContentPart(array $paragraphs) : ContentPart
    {
        $content = '';

        foreach ($paragraphs as $runs) {
            $content .= '<w:p>';
            foreach ($runs as $text) {
                $content .= '<w:r><w:t xml:space="preserve">' . htmlspecialchars($text, ENT_XML1) . '</w:t></w:r>';
            }
            $content .= '</w:p>';
        }

        $contentPart = new ContentPart(
            '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body>'
            . $content
            . '</w:body></w:document>',
            'word/document.xml'
        );

        new Normalizer()->normalize($contentPart);
        new TagGenerator($contentPart)->generateTags();

        return $contentPart;
    }

    /**
     * text of every paragraph, the remaining elements are shown as [F:field], [V:view] or [T:text]
     *
     * @param ContentPart $contentPart
     * @return array
     */
    private function getParagraphs(ContentPart $contentPart) : array
    {
        $paragraphs = [];
        $xPath = $contentPart->getXPath();

        foreach ($xPath->query('//w:p') as $paragraph) {
            $text = '';
            // text nodes created by the tag generator do not have a namespace, so they are selected by their name
            $nodes = $xPath->query(
                './/*[name() = "w:t" or self::acat:field or self::acat:view or self::acat:text]',
                $paragraph
            );

            foreach ($nodes as $node) {
                $text .= match ($node->nodeName) {
                    'acat:field' => '[F:' . $node->getAttribute('field') . ']',
                    'acat:view'  => '[V:' . $node->getAttribute('view') . ']',
                    'acat:text'  => '[T:' . $node->nodeValue . ']',
                    default      => $node->nodeValue,
                };
            }
            $paragraphs[] = $text;
        }

        return $paragraphs;
    }

}
