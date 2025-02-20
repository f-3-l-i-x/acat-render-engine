<?php

namespace Tests\Render\Condition\Action;

use DOMNodeList;
use ACAT\Parser\ParserConstants;
use ACAT\Exception\RenderException;
use ACAT\Exception\ElementException;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Render\Condition\ConditionRender;
use ACAT\Exception\ConditionParserException;

/**
 *
 */
class DeleteUntilNextElementActionTest extends AbstractConditionActionTest
{

    /**
     * @throws ConditionParserException
     * @throws ElementException
     * @throws RenderException
     * @return void
     */
    #[Test]
    public function renderCondition() : void
    {
        $values['V_TITLE'] = null;
        $values['V_TERMINATE_DATE_2'] = null;
        $values['V_TERMINATE_DATE_1'] = "10.01.2022";

        $conditionRender = new ConditionRender();
        $conditionElementGenerator = $this->getConditionElementGenerator();

        $nodes = $conditionElementGenerator->getContentPart()->getXPath()->query(
            '//' . ParserConstants::ACAT_CONDITION_NODE
        );

        $this->assertInstanceOf(DOMNodeList::class, $nodes);
        $this->assertEquals(3, $nodes->length);

        $conditionElements = $conditionElementGenerator->getConditionElements();
        $this->assertCount(3, $conditionElements);

        $conditionRender->render($conditionElements, $values);

        $viewElements = $conditionElementGenerator->getContentPart()->getXPath()->query(
            '//' . ParserConstants::ACAT_VIEW_NODE
        );
        $this->assertCount(2, $viewElements);

        $fieldElements = $conditionElementGenerator->getContentPart()->getXPath()->query(
            '//' . ParserConstants::ACAT_FIELD_NODE
        );
        $this->assertCount(1, $fieldElements);
    }

}