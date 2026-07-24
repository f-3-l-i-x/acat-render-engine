<?php

namespace Tests\Render\Block;

use DOMException;
use ACAT\Exception\RenderException;
use ACAT\Exception\ElementException;
use Tests\Render\AbstractRenderTest;
use ACAT\Parser\Element\TableRowBlock;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Render\Block\TableRowBlockRender;
use ACAT\Exception\ConditionParserException;

/**
 *
 */
class TableRowBlockRenderTest extends AbstractRenderTest
{

    /**
     * @throws ElementException
     * @return void
     */
    #[Test]
    public function aTableRowBlockRenderCanBeCreated() : void
    {
        $wordTableRowElementGenerator = $this->getWordTableRowElementGenerator();
        $blockElements = $wordTableRowElementGenerator->getBlocks();

        $this->assertCount(1, $blockElements);
        $this->assertInstanceOf(TableRowBlock::class, $blockElements[0]);

        $tableRowBlockRender = new TableRowBlockRender($blockElements[0], []);
        $this->assertInstanceOf(TableRowBlockRender::class, $tableRowBlockRender);
    }

    /**
     * @throws ConditionParserException
     * @throws DOMException
     * @throws ElementException
     * @throws RenderException
     * @return void
     */
    #[Test]
    public function renderTableRowBlock() : void
    {
        $wordTableRowElementGenerator = $this->getWordTableRowElementGenerator();
        $blockElements = $wordTableRowElementGenerator->getBlocks();

        $this->assertCount(1, $blockElements);
        $this->assertInstanceOf(TableRowBlock::class, $blockElements[0]);

        $tableRowBlockRender = new TableRowBlockRender($blockElements[0], $this->getBlockValues());
        $this->assertInstanceOf(TableRowBlockRender::class, $tableRowBlockRender);

        $tableRowBlockRender->render($blockElements, $this->getBlockValues());

        /* must not exist */
        $startBlock = $wordTableRowElementGenerator->getContentPart()->getXPath()->query('//w:tr[@id="b_start"]');
        $this->assertEquals(0, $startBlock->length);

        /* must not exist */
        $endBlock = $wordTableRowElementGenerator->getContentPart()->getXPath()->query('//w:tr[@id="b_end"]');
        $this->assertEquals(0, $endBlock->length);

        /* there must be exactly two */
        $endBlock = $wordTableRowElementGenerator->getContentPart()->getXPath()->query('//w:tr[@type="b_content"]');
        $this->assertEquals(2, $endBlock->length);

        /* there must be exactly two */
        $endBlock = $wordTableRowElementGenerator->getContentPart()->getXPath()->query('//w:tr[@type="content"]');
        $this->assertEquals(2, $endBlock->length);
    }

    /**
     * @return array
     */
    private function getBlockValues() : array
    {
        $values['fields']["invoice_id"] = 100002;
        $values['fields']["111"] = null;
        $values['fields']["104"] = "Frau";
        $values['fields']["110"] = null;
        $values['fields']["105"] = "Erika";
        $values['fields']["106"] = "Musterfrau";
        $values['fields']["107"] = "Beispielweg 1";
        $values['fields']["108"] = "54321";
        $values['fields']["109"] = "Musterstadt";
        $values['fields']["115"] = null;
        $values['fields']["116"] = null;
        $values['fields']["117"] = null;
        $values['fields']["118"] = null;
        $values['fields']["112"] = null;
        $values['fields']["113"] = null;
        $values['fields']["114"] = null;
        $values['fields']["203"] = null;
        $values['fields']["204"] = null;
        $values['fields']["206"] = null;
        $values['fields']["205"] = "Beispielkurs 2";
        $values['fields']["219"] = null;
        $values['fields']["209"] = 45000;
        $values['fields']["218"] = 1822;
        $values['fields']["207"] = "2020-10-01";
        $values['fields']["208"] = "2021-03-31";
        $values['fields']["210"] = 20047;
        $values['fields']["211"] = "0";
        $values['fields']["220"] = null;
        $values['fields']["212"] = "1";
        $values['fields']["202"] = "PL - 00002";
        $values['fields']["201"] = 90000;

        $values['blocks'][0]['fields'][0]["215"] = "Musterkarte";
        $values['blocks'][0]['fields'][0]["216"] = "1000000";
        $values['blocks'][0]['fields'][0]["217"] = 2250;

        $values['blocks'][0]['fields'][1]["215"] = "Musterkarte";
        $values['blocks'][0]['fields'][1]["216"] = "1000000";
        $values['blocks'][0]['fields'][1]["217"] = 2250;

        return $values;
    }

    /**
     * @throws ConditionParserException
     * @throws DOMException
     * @throws ElementException
     * @throws RenderException
     * @return void
     */
    #[Test]
    public function renderTableRowInCorrectSequence() : void
    {
        $expectedSequence = ['b_content', 'b_content', 'content', 'content'];

        $wordTableRowElementGenerator = $this->getWordTableRowElementGenerator();
        $blockElements = $wordTableRowElementGenerator->getBlocks();

        $this->assertCount(1, $blockElements);
        $this->assertInstanceOf(TableRowBlock::class, $blockElements[0]);

        $tableRowBlockRender = new TableRowBlockRender($blockElements[0], $this->getBlockValues());
        $this->assertInstanceOf(TableRowBlockRender::class, $tableRowBlockRender);

        $tableRowBlockRender->render($blockElements, $this->getBlockValues());
        $tableRows = $wordTableRowElementGenerator->getContentPart()->getXPath()->query('//w:tr');

        /* there must be exactly four */
        $this->assertEquals(4, $tableRows->length);

        /* check correct sequence */
        for ($i = 0; $i < $tableRows->length; $i++) {
            $type = $tableRows->item($i)->getAttribute('type');
            $this->assertEquals($expectedSequence[$i], $type);
        }
    }

}