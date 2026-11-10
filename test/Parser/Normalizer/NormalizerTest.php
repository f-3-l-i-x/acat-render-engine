<?php

namespace Tests\Parser\Normalizer;

use DOMNodeList;
use PHPUnit\Framework\TestCase;
use ACAT\Parser\ParserConstants;
use ACAT\Document\Word\ContentPart;
use ACAT\Document\Word\WordDocument;
use ACAT\Exception\DocumentException;
use ACAT\Parser\Normalizer\Normalizer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use ACAT\Exception\PlaceholderException;
use ACAT\Parser\Placeholder\ACatPlaceholder;
use ACAT\Parser\Placeholder\TextPlaceholder;
use ACAT\Parser\Placeholder\FieldPlaceholder;
use ACAT\Parser\Placeholder\EndBlockPlaceholder;
use ACAT\Parser\Placeholder\ConditionPlaceholder;
use ACAT\Parser\Placeholder\StartBlockPlaceholder;

/**
 *
 */
class NormalizerTest extends TestCase
{

    /**
     *
     * @throws DocumentException
     *@return void
     */
    #[Test]
    public function normalizeWordDocument() : void
    {
        $currentDocument = __DIR__ . '/../../Resources/Parser/Normalizer/normalizer_test_document_lck.docx';

        //create a copy from original file
        copy(__DIR__ . '/../../Resources/Parser/Normalizer/normalizer_test_document.docx', $currentDocument);

        //create new instance
        $wordDocument = new WordDocument($currentDocument);

        //normalizer
        $normalizer = new Normalizer();

        //open document
        $wordDocument->open();

        //get content parts
        foreach ($wordDocument->getContentParts() as $contentPart) {

            //check content part
            $this->assertInstanceOf(ContentPart::class, $contentPart);

            //normalize the content part
            $normalizer->normalize($contentPart);
        }

        //save document
        $wordDocument->save();

        //get 'word/document.xml' ContentPart
        $contentPart = $wordDocument->getContentParts()['word/document.xml'];
        $this->assertInstanceOf(ContentPart::class, $contentPart);

        //get all text nodes
        $textNodes = $contentPart->getXPath()->query('//w:t');
        $this->assertInstanceOf(DOMNodeList::class, $textNodes);
        $this->assertGreaterThan(0, count($textNodes));

        //get placeholders
        $aCatNodes = [];
        $aCatTextNodes = [];
        $acatFieldNodes = [];
        $acatConditionNodes = [];
        $acatStartBlockNodes = [];
        $acatEndBlockNodes = [];

        foreach ($textNodes as $textNode) {
            preg_match_all(ParserConstants::MARKER_REG_EX, $textNode->nodeValue, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                try {
                    $aCatNodes[] = ACatPlaceholder::getPlaceholder($match[0]);
                } catch (PlaceholderException $e) {
                    $this->assertStringContainsString('C:12', $e->getMessage());
                }
            }
        }

        //we need exactly 57 Nodes
        $this->assertCount(57, $aCatNodes);

        foreach ($aCatNodes as $aCatNode) {
            if ($aCatNode instanceof FieldPlaceholder) {
                $acatFieldNodes[] = $aCatNode;
            } elseif ($aCatNode instanceof ConditionPlaceholder) {
                $acatConditionNodes[] = $aCatNode;
            } elseif ($aCatNode instanceof TextPlaceholder) {
                $aCatTextNodes[] = $aCatNode;
            } elseif ($aCatNode instanceof StartBlockPlaceholder) {
                $acatStartBlockNodes[] = $aCatNode;
            } elseif ($aCatNode instanceof EndBlockPlaceholder) {
                $acatEndBlockNodes[] = $aCatNode;
            }
        }

        //29 field nodes
        $this->assertCount(29, $acatFieldNodes);

        //14 condition nodes
        $this->assertCount(14, $acatConditionNodes);

        //2 text nodes
        $this->assertCount(2, $aCatTextNodes);

        //6 start block nodes
        $this->assertCount(6, $acatStartBlockNodes);

        //6 end block nodes
        $this->assertCount(6, $acatEndBlockNodes);
    }

    /**
     * An unrelated "$" (e.g. a currency amount) that is never followed by its own "}"
     * must not be merged with whatever "}" happens to appear later in the document -
     * doing so would delete every node in between and glue their text together.
     *
     * @throws DocumentException
     */
    #[Test]
    public function anUnrelatedDollarSignDoesNotCorruptLaterParagraphs() : void
    {
        $xml = '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
            <w:body>
                <w:p><w:r><w:t>Kosten: </w:t></w:r><w:r><w:t>$50</w:t></w:r></w:p>
                <w:p><w:r><w:t>Ein normaler Absatz ohne Platzhalter.</w:t></w:r></w:p>
                <w:p><w:r><w:t>Noch ein Absatz mit array(1,2,3}</w:t></w:r></w:p>
                <w:p><w:r><w:t>Und hier geht es normal weiter.</w:t></w:r></w:p>
            </w:body>
        </w:document>';

        $contentPart = new ContentPart($xml);
        (new Normalizer())->normalize($contentPart);

        $paragraphs = $contentPart->getXPath()->query('//w:p');
        $this->assertCount(4, $paragraphs);

        $textNodes = $contentPart->getXPath()->query('//w:t');
        $values = array_map(fn($node) => $node->nodeValue, iterator_to_array($textNodes));

        $this->assertContains('Ein normaler Absatz ohne Platzhalter.', $values);
        $this->assertContains('Und hier geht es normal weiter.', $values);
    }

    /**
     * A placeholder split across several differently-formatted runs (as Word does
     * whenever the marker text has mixed formatting) must still be reassembled into
     * a single text node.
     *
     * @throws DocumentException
     */
    #[Test]
    public function aMarkerSplitAcrossMultipleRunsIsReassembled() : void
    {
        $xml = '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
            <w:body>
                <w:p>
                    <w:r><w:t>${</w:t></w:r>
                    <w:r><w:t>F</w:t></w:r>
                    <w:r><w:t>:</w:t></w:r>
                    <w:r><w:t>11</w:t></w:r>
                    <w:r><w:t>2</w:t></w:r>
                    <w:r><w:t>1}</w:t></w:r>
                </w:p>
            </w:body>
        </w:document>';

        $contentPart = new ContentPart($xml);
        (new Normalizer())->normalize($contentPart);

        $textNodes = $contentPart->getXPath()->query('//w:t');
        $this->assertCount(1, $textNodes);
        $this->assertSame('${F:1121}', $textNodes->item(0)->nodeValue);
    }

    /**
     * @return array
     */
    public static function textNodes() : array
    {
        return [
            'complete placeholder'                   => [
                [['${F:1}'], ['Text']],
                ['${F:1}', 'Text'],
            ],
            'placeholder in two runs'                => [
                [['${F:', '12}']],
                ['${F:12}'],
            ],
            'placeholder in three runs'              => [
                [['Feld ${', 'F:12', '} Ende']],
                ['Feld ${F:12} Ende'],
            ],
            'end and start of placeholders in a run' => [
                [['${F:1', '2} und ${F:4', '5}']],
                ['${F:12}', ' und ${F:45}'],
            ],
            'complete placeholder between two parts' => [
                [['${F:1', '2} und ${F:3} und ${F:4', '5}']],
                ['${F:12}', ' und ${F:3} und ${F:45}'],
            ],
            'restart of an unfinished placeholder'   => [
                [['Preis 5$ ', '${F:', '1}']],
                ['Preis 5$ ', '${F:1}'],
            ],
            'dollar and brace without a placeholder' => [
                [['Preis 5$'], ['Absatz zwei'], ['Menge {a}']],
                ['Preis 5$', 'Absatz zwei', 'Menge {a}'],
            ],
            'unfinished placeholder in a paragraph'  => [
                [['Preis 5$'], ['${F:1}'], ['Menge {a}']],
                ['Preis 5$', '${F:1}', 'Menge {a}'],
            ],
        ];
    }

    /**
     * @param array $paragraphs text of the runs of every paragraph
     * @param array $expected text nodes after the normalizer was running
     * @return void
     */
    #[Test]
    #[DataProvider('textNodes')]
    public function normalizeTextNodes(array $paragraphs, array $expected) : void
    {
        $contentPart = $this->getContentPart($paragraphs);

        $normalizer = new Normalizer();
        $normalizer->normalize($contentPart);

        $this->assertSame($expected, $this->getTextNodeValues($contentPart));
        $this->assertCount(count($paragraphs), $contentPart->getXPath()->query('//w:p'));
    }

    /**
     * text nodes of a content part must not be merged into the next content part
     *
     * @return void
     */
    #[Test]
    public function normalizeSeveralContentParts() : void
    {
        $firstContentPart = $this->getContentPart([['Preis 5$']]);
        $secondContentPart = $this->getContentPart([['${F:1}'], ['Menge {a}']]);

        $normalizer = new Normalizer();
        $normalizer->normalize($firstContentPart);
        $normalizer->normalize($secondContentPart);

        $this->assertSame(['Preis 5$'], $this->getTextNodeValues($firstContentPart));
        $this->assertSame(['${F:1}', 'Menge {a}'], $this->getTextNodeValues($secondContentPart));
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
                $content .= '<w:r><w:t xml:space="preserve">' . $text . '</w:t></w:r>';
            }
            $content .= '</w:p>';
        }

        return new ContentPart(
            '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body>'
            . $content
            . '</w:body></w:document>',
            'word/document.xml'
        );
    }

    /**
     * @param ContentPart $contentPart
     * @return array
     */
    private function getTextNodeValues(ContentPart $contentPart) : array
    {
        $values = [];

        foreach ($contentPart->getXPath()->query('//w:t') as $textNode) {
            $values[] = $textNode->nodeValue;
        }

        return $values;
    }

}
