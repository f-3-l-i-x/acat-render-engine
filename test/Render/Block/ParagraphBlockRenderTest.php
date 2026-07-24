<?php

namespace Tests\Render\Block;

use DOMException;
use ACAT\Exception\RenderException;
use ACAT\Exception\ElementException;
use Tests\Render\AbstractRenderTest;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Parser\Element\ParagraphBlock;
use ACAT\Render\Block\ParagraphBlockRender;
use ACAT\Exception\ConditionParserException;

/**
 *
 */
class ParagraphBlockRenderTest extends AbstractRenderTest
{

    /**
     * @throws ElementException
     * @return void
     */
    #[Test]
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
     *
     * @throws ElementException
     * @throws RenderException
     * @throws ConditionParserException
     * @throws DOMException
     *@return void
     */
    #[Test]
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
    private function getBlockValues() : array
    {
        return [
            'fields' =>
                [
                    'invoice_id' => 100001,
                    111          => null,
                    104          => 'Frau',
                    110          => null,
                    105          => 'Erika',
                    106          => 'Mustermann',
                    107          => 'Musterstraße 1',
                    108          => '12345',
                    109          => 'Musterstadt',
                    115          => null,
                    116          => null,
                    117          => null,
                    118          => null,
                    112          => null,
                    113          => null,
                    114          => null,
                    203          => '1000001',
                    204          => '2021-02-11',
                    206          => null,
                    205          => 'Beispielkurs 1',
                    219          => null,
                    209          => 45000,
                    218          => 4500,
                    207          => '2020-04-01',
                    208          => '2022-02-19',
                    210          => 49500,
                    211          => '1',
                    220          => null,
                    212          => '1',
                    202          => 'PL - 00001',
                    201          => null,
                ],
            'blocks' =>
                [
                    0 =>
                        [
                            'fields' =>
                                [
                                    0 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2020-04-01',
                                        ],
                                    1 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2020-05-01',
                                        ],
                                    2 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2020-06-01',
                                        ],
                                    3 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2020-07-01',
                                        ],
                                    4 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2020-08-01',
                                        ],
                                    5 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2020-09-01',
                                        ],
                                    6 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2020-10-01',
                                        ],
                                    7 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2020-11-01',
                                        ],
                                    8 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2020-12-01',
                                        ],
                                    9 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2021-01-01',
                                        ],
                                    10 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2021-02-01',
                                        ],
                                    11 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2021-03-01',
                                        ],
                                    12 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2021-04-01',
                                        ],
                                    13 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2021-05-01',
                                        ],
                                    14 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2021-06-01',
                                        ],
                                    15 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2021-07-01',
                                        ],
                                    16 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2021-08-01',
                                        ],
                                    17 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2021-09-01',
                                        ],
                                    18 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2021-10-01',
                                        ],
                                    19 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2021-11-01',
                                        ],
                                    20 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2021-12-01',
                                        ],
                                    21 =>
                                        [
                                            'invoice_id' => 100001,
                                            214          => 2250,
                                            213          => '2022-01-01',
                                        ],
                                ]
                        ],
                ],
        ];
    }

    /**
     *
     * @return void
     * @throws ConditionParserException
     * @throws DOMException
     * @throws ElementException
     * @throws RenderException
     */
    #[Test]
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
                str_contains(
                    trim($contentElements->item($i)->nodeValue),
                    $values['blocks'][0]['fields'][$i][213]
                )
            );
        }
    }

}