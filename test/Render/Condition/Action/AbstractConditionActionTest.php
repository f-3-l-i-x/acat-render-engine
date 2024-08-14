<?php

namespace Tests\Render\Condition\Action;

use PHPUnit\Framework\TestCase;
use ACAT\Exception\ElementException;
use ACAT\Document\Word\ContentPart;
use ACAT\Parser\Element\ElementGenerator;

/**
 *
 */
abstract class AbstractConditionActionTest extends TestCase
{

    /**
     * @return ElementGenerator
     * @throws ElementException
     */
    public function getConditionElementGenerator() : ElementGenerator
    {
        return new ElementGenerator($this->getConditionContentPart());
    }

    /**
     * @return ContentPart
     */
    protected function getConditionContentPart() : ContentPart
    {
        $testXMLFile = __DIR__ . '/../../../Resources/Render/Condition/Action/DeleteUntilNextElementAction.xml';

        $xmlContent = file_get_contents($testXMLFile);
        $this->assertIsString($xmlContent);

        $contentPart = new ContentPart($xmlContent, $testXMLFile);
        $this->assertInstanceOf(ContentPart::class, $contentPart);

        return $contentPart;
    }

}