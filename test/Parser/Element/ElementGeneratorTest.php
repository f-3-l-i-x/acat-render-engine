<?php

namespace Tests\Parser\Element;

use DOMNode;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\TestCase;
use ACAT\Parser\Element\TextBlock;
use ACAT\Document\Word\ContentPart;
use ACAT\Exception\ElementException;
use ACAT\Parser\Element\FieldElement;
use ACAT\Parser\Element\TableRowBlock;
use ACAT\Parser\Element\ParagraphBlock;
use ACAT\Parser\Element\TableCellBlock;
use ACAT\Parser\Element\ElementGenerator;

/**
 *
 */
class ElementGeneratorTest extends TestCase
{

    /**
     * @test
     *
     * @return void
     * @throws ElementException
     */
    public function getTextBlock() : void
    {
        $elementGenerator = $this->getElementGenerator();
        $textBlocks = $elementGenerator->getTextBlocks();

        $this->assertCount(1, $textBlocks);

        foreach ($textBlocks as $textBlock) {
            $this->assertNotNull($textBlock);
            $this->assertInstanceOf(TextBlock::class, $textBlock);

            $this->assertCount(3, $textBlock->getChildren());#

            //start and end
            $this->assertInstanceOf(DOMNode::class, $textBlock->getStart());
            $this->assertInstanceOf(DOMNode::class, $textBlock->getEnd());

            //first children
            $this->assertEquals('w:t', $textBlock->getChildren()[0]->getElement()->tagName);
            $this->assertIsString($textBlock->getChildren()[0]->getElement()->nodeValue);

            //second children
            $this->assertEquals('acat:field', $textBlock->getChildren()[1]->getElement()->tagName);
            $this->assertEquals(
                'd9134945-880b-4f5d-ab1d-74830f028bbf',
                $textBlock->getChildren()[1]->getElement()->getAttribute('id')
            );
            $this->assertEquals('157', $textBlock->getChildren()[1]->getElement()->getAttribute('field'));

            //third children
            $this->assertEquals('w:t', $textBlock->getChildren()[2]->getElement()->tagName);

            //field element
            $fieldNodes = $textBlock->getFieldElements();
            $this->assertCount(1, $fieldNodes);

            $fieldElement = $fieldNodes[0];

            $this->assertInstanceOf(FieldElement::class, $fieldElement);
            $this->assertEquals('157', $fieldElement->getFieldId());
            $this->assertEquals('d9134945-880b-4f5d-ab1d-74830f028bbf', $fieldElement->getId());
        }
    }

    /**
     * @return ElementGenerator
     * @throws ElementException
     */
    private function getElementGenerator() : ElementGenerator
    {
        return new ElementGenerator($this->getContentPart());
    }

    /**
     * @return ContentPart
     */
    #[Pure(true)]
    protected function getContentPart() : ContentPart
    {
        return new ContentPart(file_get_contents(__DIR__ . '/../../Resources/Parser/Element/document.xml'));
    }

    /**
     * @test
     *
     * @return void
     * @throws ElementException
     */
    public function getFieldElements() : void
    {
        $elementGenerator = $this->getElementGenerator();
        $fieldElements = $elementGenerator->getFieldElements();

        $this->assertCount(20, $fieldElements);
    }

    /**
     * @test
     *
     * @return void
     * @throws ElementException
     */
    public function getTextElements() : void
    {
        $elementGenerator = $this->getElementGenerator();
        $textElements = $elementGenerator->getTextElements();

        $this->assertCount(3, $textElements);
    }

    /**
     * @test
     *
     * @return void
     * @throws ElementException
     */
    public function getConditionElements() : void
    {
        $elementGenerator = $this->getElementGenerator();
        $fieldElements = $elementGenerator->getConditionElements();

        $this->assertCount(14, $fieldElements);
    }

    /**
     * @test
     *
     * @return void
     * @throws ElementException
     */
    public function getTableRowBlock() : void
    {
        $elementGenerator = $this->getElementGenerator();
        $tableRowBlocks = $elementGenerator->getTableRowBlocks();

        $this->assertCount(2, $tableRowBlocks);

        $tableRowBlock = $tableRowBlocks[0];
        $this->assertInstanceOf(TableRowBlock::class, $tableRowBlock);

        $this->assertCount(1, $tableRowBlock->getChildren());#

        //start and end
        $this->assertInstanceOf(DOMNode::class, $tableRowBlock->getStart());
        $this->assertInstanceOf(DOMNode::class, $tableRowBlock->getEnd());

        //child
        $this->assertEquals('w:tr', $tableRowBlock->getChildren()[0]->getElement()->tagName);
    }

    /**
     * @test
     *
     * @return void
     * @throws ElementException
     */
    public function getAllBlocks() : void
    {
        $elementGenerator = $this->getElementGenerator();
        $blocks = $elementGenerator->getBlocks();

        $this->assertCount(6, $blocks);

        $textBlocks = [];
        $paragraphBlocks = [];
        $tableCellBlocks = [];
        $tableRowBlocks = [];

        foreach ($blocks as $block) {
            if ($block instanceof TextBlock) {
                $textBlocks[] = $block;
            } elseif ($block instanceof ParagraphBlock) {
                $paragraphBlocks[] = $block;
            } elseif ($block instanceof TableCellBlock) {
                $tableCellBlocks[] = $block;
            } elseif ($block instanceof TableRowBlock) {
                $tableRowBlocks[] = $block;
            }
        }

        $this->assertCount(1, $textBlocks);
        $this->assertCount(2, $paragraphBlocks);
        $this->assertCount(1, $tableCellBlocks);
        $this->assertCount(2, $tableRowBlocks);
    }


}