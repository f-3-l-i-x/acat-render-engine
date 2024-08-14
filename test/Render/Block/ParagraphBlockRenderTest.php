<?php

namespace Tests\Render\Block;

use DOMException;
use ACAT\Utils\StringUtils;
use JetBrains\PhpStorm\ArrayShape;
use ACAT\Exception\RenderException;
use ACAT\Exception\ElementException;
use Tests\Render\AbstractRenderTest;
use ACAT\Parser\Element\ParagraphBlock;
use ACAT\Render\Block\ParagraphBlockRender;
use ACAT\Exception\ConditionParserException;

/**
 *
 */
class ParagraphBlockRenderTest extends AbstractRenderTest
{

    /**
     * @test
     *
     * @return void
     * @throws ElementException
     */
    public function aParagraphBlockRenderCanBeCreated() : void
    {
        $wordElementPElementGenerator = $this->getWordParagraphElementGenerator();
        $blockElements = $wordElementPElementGenerator->getBlocks();

        $this->assertCount(1, $blockElements);
        $this->assertInstanceOf(ParagraphBlock::class, $blockElements[0]);

        $paragraphBlockRender = new ParagraphBlockRender($blockElements[0], []);
        $this->assertInstanceOf(ParagraphBlockRender::class, $paragraphBlockRender);
    }

    /**
     * @test
     *
     * @return void
     * @throws ElementException
     * @throws RenderException
     * @throws ConditionParserException
     * @throws DOMException
     */
    public function renderParagraphBlock() : void
    {
        $wordElementPElementGenerator = $this->getWordParagraphElementGenerator();
        $blockElements = $wordElementPElementGenerator->getBlocks();

        $this->assertCount(1, $blockElements);
        $this->assertInstanceOf(ParagraphBlock::class, $blockElements[0]);

        $paragraphBlockRender = new ParagraphBlockRender($blockElements[0], []);
        $this->assertInstanceOf(ParagraphBlockRender::class, $paragraphBlockRender);

        $paragraphBlockRender->render($blockElements, $this->getBlockValues());

        /* must not exist */
        $startParagraph = $wordElementPElementGenerator->getContentPart()->getXPath()->query('//w:p[@id="start"]');
        $this->assertEquals(0, $startParagraph->length);

        /* must not exist */
        $endParagraph = $wordElementPElementGenerator->getContentPart()->getXPath()->query('//w:p[@id="end"]');
        $this->assertEquals(0, $endParagraph->length);

        /* there must be exactly 22 */
        $endBlock = $wordElementPElementGenerator->getContentPart()->getXPath()->query('//w:p[@type="content"]');
        $this->assertEquals(22, $endBlock->length);
    }

    /**
     * @return array
     */
    #[ArrayShape(['fields' => "array", 'blocks' => "\array[][]"])]
    private function getBlockValues() : array
    {
        return [
            'fields' =>
                [
                    'rechnung_id' => 100001,
                    1757          => null,
                    1744          => 'Frau',
                    1752          => null,
                    1745          => 'Erika',
                    1747          => 'Mustermann',
                    1748          => 'Musterstraße 1',
                    1749          => '12345',
                    1750          => 'Musterstadt',
                    1858          => null,
                    1860          => null,
                    1862          => null,
                    1863          => null,
                    1760          => null,
                    1761          => null,
                    1762          => null,
                    1741          => '1000001',
                    1742          => '2021-02-11',
                    1768          => null,
                    1765          => 'Beispielkurs 1',
                    1921          => null,
                    1771          => 45000,
                    1918          => 4500,
                    1769          => '2020-04-01',
                    1770          => '2022-02-19',
                    1772          => 49500,
                    1780          => '1',
                    1922          => null,
                    1783          => '1',
                    1676          => 'PL - 00001',
                    68            => null,
                ],
            'blocks' =>
                [
                    0 =>
                        [
                            'fields' =>
                                [
                                    0 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2020-04-01',
                                        ],
                                    1 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2020-05-01',
                                        ],
                                    2 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2020-06-01',
                                        ],
                                    3 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2020-07-01',
                                        ],
                                    4 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2020-08-01',
                                        ],
                                    5 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2020-09-01',
                                        ],
                                    6 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2020-10-01',
                                        ],
                                    7 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2020-11-01',
                                        ],
                                    8 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2020-12-01',
                                        ],
                                    9 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2021-01-01',
                                        ],
                                    10 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2021-02-01',
                                        ],
                                    11 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2021-03-01',
                                        ],
                                    12 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2021-04-01',
                                        ],
                                    13 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2021-05-01',
                                        ],
                                    14 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2021-06-01',
                                        ],
                                    15 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2021-07-01',
                                        ],
                                    16 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2021-08-01',
                                        ],
                                    17 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2021-09-01',
                                        ],
                                    18 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2021-10-01',
                                        ],
                                    19 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2021-11-01',
                                        ],
                                    20 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2021-12-01',
                                        ],
                                    21 =>
                                        [
                                            'rechnung_id' => 100001,
                                            1787          => 2250,
                                            1786          => '2022-01-01',
                                        ],
                                ]
                        ],
                ],
        ];
    }

    /**
     * @test
     *
     * @return void
     * @throws ConditionParserException
     * @throws DOMException
     * @throws ElementException
     * @throws RenderException
     */
    public function renderParagraphBlockInCorrectSequence() : void
    {
        $values = $this->getBlockValues();

        $wordElementPElementGenerator = $this->getWordParagraphElementGenerator();
        $blockElements = $wordElementPElementGenerator->getBlocks();

        $this->assertCount(1, $blockElements);
        $this->assertInstanceOf(ParagraphBlock::class, $blockElements[0]);

        $paragraphBlockRender = new ParagraphBlockRender($blockElements[0], []);
        $this->assertInstanceOf(ParagraphBlockRender::class, $paragraphBlockRender);

        $paragraphBlockRender->render($blockElements, $this->getBlockValues());

        /* there must be exactly 22 */
        $contentElements = $wordElementPElementGenerator->getContentPart()->getXPath()->query('//w:p[@type="content"]');
        $this->assertEquals(22, $contentElements->length);

        /* check correct sequence */
        for ($i = 0; $i < $contentElements->length; $i++) {
            $this->assertTrue(
                StringUtils::contains(
                    trim($contentElements->item($i)->nodeValue),
                    $values['blocks'][0]['fields'][$i][1786]
                )
            );
        }
    }

}