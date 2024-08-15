<?php

namespace Tests\Render\Condition;

use DOMNodeList;
use ACAT\Parser\ParserConstants;
use ACAT\Exception\RenderException;
use ACAT\Exception\ElementException;
use Tests\Render\AbstractRenderTest;
use ACAT\Parser\Element\ConditionElement;
use ACAT\Render\Condition\ConditionRender;
use ACAT\Exception\ConditionParserException;

/**
 *
 */
class ConditionRenderTest extends AbstractRenderTest
{

    /**
     * @test
     */
    public function aConditionRenderCanBeCreated() : void
    {
        $conditionRender = new ConditionRender();
        $this->assertInstanceOf(ConditionRender::class, $conditionRender);
    }

    /**
     * @test
     *
     * @return void
     * @throws ConditionParserException
     * @throws ElementException
     * @throws RenderException
     */
    public function renderConditions() : void
    {
        $values['151'] = 2;

        $conditionRender = new ConditionRender();
        $conditionElementGenerator = $this->getConditionElementGenerator();

        $nodes = $conditionElementGenerator->getContentPart()->getXPath()->query(
            '//' . ParserConstants::ACAT_CONDITION_NODE
        );

        $this->assertInstanceOf(DOMNodeList::class, $nodes);
        $this->assertEquals(6, $nodes->length);

        $conditionElements = $conditionElementGenerator->getConditionElements();
        $this->assertCount(6, $conditionElements);

        $this->expectException(ConditionParserException::class);
        $conditionRender->render($conditionElements, $values);

        $nodes = $conditionElementGenerator->getContentPart()->getXPath()->query(
            '//' . ParserConstants::ACAT_CONDITION_NODE
        );

        $this->assertInstanceOf(DOMNodeList::class, $nodes);
        $this->assertEquals(0, $nodes->length);
    }

    /**
     * @test
     *
     * @return void
     * @throws ConditionParserException
     * @throws ElementException
     * @throws RenderException
     */
    public function renderCondition() : void
    {
        $values['151'] = 2;

        $conditionRender = new ConditionRender();
        $conditionElementGenerator = $this->getConditionElementGenerator();

        $nodes = $conditionElementGenerator->getContentPart()->getXPath()->query(
            '//' . ParserConstants::ACAT_CONDITION_NODE
        );

        $this->assertInstanceOf(DOMNodeList::class, $nodes);
        $this->assertEquals(6, $nodes->length);

        foreach ($nodes as $node) {
            $this->expectException(ConditionParserException::class);
            $conditionRender->renderConditionElement(new ConditionElement($node), $values);
        }

        $nodes = $conditionElementGenerator->getContentPart()->getXPath()->query(
            '//' . ParserConstants::ACAT_CONDITION_NODE
        );

        $this->assertInstanceOf(DOMNodeList::class, $nodes);
        $this->assertEquals(0, $nodes->length);
    }

}