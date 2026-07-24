<?php

namespace Tests\Render\Condition\Action;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use ACAT\Parser\Element\ConditionElement;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Render\Condition\Action\ConditionParser;

/**
 *
 */
class ConditionParserTest extends TestCase
{

    private const string NAMESPACE = 'https://schemas.acat.akademie.uni-bremen.de';

    private function conditionElement(string $fieldId, string $expression) : ConditionElement {

        $dom = new DOMDocument();
        $dom->loadXML(
            '<root xmlns:acat="' . self::NAMESPACE . '">'
            . '<acat:condition id="1" field="' . $fieldId . '"><![CDATA[' . $expression . ']]></acat:condition>'
            . '</root>'
        );

        $node = $dom->getElementsByTagNameNS(self::NAMESPACE, 'condition')->item(0);

        return new ConditionElement($node);

    }

    public function testGetCompareValueKeepsAnExplicitZero() : void {

        $term = (new ConditionParser())->getCompareValue('<>0');

        $this->assertSame('<>', $term['operator']);
        $this->assertSame('0', $term['value']);

    }

    public function testGetCompareValueWithNoValueIsNull() : void {

        $term = (new ConditionParser())->getCompareValue('<>');

        $this->assertSame('<>', $term['operator']);
        $this->assertNull($term['value']);

    }

    #[Test]
    public function distinguishesAFieldThatIsZeroFromAnEmptyField() : void {

        $parser = new ConditionParser();
        $element = $this->conditionElement('101', '<>0');

        $this->assertTrue($parser->evaluateCondition($element, ['101' => '5']));
        $this->assertFalse($parser->evaluateCondition($element, ['101' => '0']));
        $this->assertTrue($parser->evaluateCondition($element, ['101' => '']));

    }

    #[Test]
    public function equalsZeroOnlyMatchesAFieldThatIsActuallyZero() : void {

        $parser = new ConditionParser();
        $element = $this->conditionElement('101', '=0');

        $this->assertTrue($parser->evaluateCondition($element, ['101' => '0']));
        $this->assertFalse($parser->evaluateCondition($element, ['101' => '5']));
        $this->assertFalse($parser->evaluateCondition($element, ['101' => '']));

    }

    #[Test]
    public function anEmptyExpressionFallsBackToAnEmptinessCheck() : void {

        $parser = new ConditionParser();
        $element = $this->conditionElement('101', '<>');

        $this->assertTrue($parser->evaluateCondition($element, ['101' => 'something']));
        $this->assertFalse($parser->evaluateCondition($element, ['101' => '']));

    }

    #[Test]
    public function normalizesAValidGermanDate() : void {

        $parser = new ConditionParser();
        $element = $this->conditionElement('101', '=01.02.2024');

        $this->assertTrue($parser->evaluateCondition($element, ['101' => '2024-02-01']));

    }

    #[Test]
    public function doesNotSilentlyNormalizeAnInvalidCalendarDate() : void {

        $parser = new ConditionParser();
        // 31.02. does not exist; DateTime::createFromFormat() would otherwise roll
        // this over to 02.03. instead of rejecting it.
        $element = $this->conditionElement('101', '=31.02.2024');

        $this->assertTrue($parser->evaluateCondition($element, ['101' => '31.02.2024']));
        $this->assertFalse($parser->evaluateCondition($element, ['101' => '2024-03-02']));

    }

}
