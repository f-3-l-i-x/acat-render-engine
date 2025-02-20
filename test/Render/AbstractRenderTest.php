<?php

namespace Tests\Render;

use Monolog\Logger;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use ACAT\Exception\ElementException;
use Monolog\Formatter\LineFormatter;
use ACAT\Document\Word\ContentPart;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Parser\Element\ElementGenerator;

/**
 *
 */
class AbstractRenderTest extends TestCase
{

    /**
     * @return ElementGenerator
     * @throws ElementException
     */
    public function getWordElementGenerator() : ElementGenerator
    {
        $wordContentPart = $this->getWordContentPart();
        return new ElementGenerator($wordContentPart);
    }

    /**
     *
     * @return ContentPart
     */
    #[Test]
    public function getWordContentPart() : ContentPart
    {
        $testXMLFile = __DIR__ . '/../Resources/Render/Element/document.xml';

        $xmlContent = file_get_contents($testXMLFile);
        $this->assertIsString($xmlContent);

        return new ContentPart($xmlContent, $testXMLFile);
    }

    /**
     * @return ElementGenerator
     * @throws ElementException
     */
    public function getWordParagraphElementGenerator() : ElementGenerator
    {
        return new ElementGenerator($this->getParagraphBlockContentPart());
    }

    /**
     * @return ContentPart
     */
    private function getParagraphBlockContentPart() : ContentPart
    {
        $testXMLFile = __DIR__ . '/../Resources/Render/Block/ParagraphBlock.xml';

        $xmlContent = file_get_contents($testXMLFile);
        $this->assertIsString($xmlContent);

        $contentPart = new ContentPart($xmlContent, $testXMLFile);
        $this->assertInstanceOf(ContentPart::class, $contentPart);

        return $contentPart;
    }

    /**
     * @return ElementGenerator
     * @throws ElementException
     */
    public function getWordTableRowElementGenerator() : ElementGenerator
    {
        return new ElementGenerator($this->getTableRowBlockContentPart());
    }

    /**
     * @return ContentPart
     */
    private function getTableRowBlockContentPart() : ContentPart
    {
        $testXMLFile = __DIR__ . '/../Resources/Render/Block/TableRowBlock.xml';

        $xmlContent = file_get_contents($testXMLFile);
        $this->assertIsString($xmlContent);

        $contentPart = new ContentPart($xmlContent, $testXMLFile);
        $this->assertInstanceOf(ContentPart::class, $contentPart);

        return $contentPart;
    }

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

        $testXMLFile = __DIR__ . '/../Resources/Render/Condition/Conditions.xml';

        $xmlContent = file_get_contents($testXMLFile);
        $this->assertIsString($xmlContent);

        $contentPart = new ContentPart($xmlContent, $testXMLFile);
        $this->assertInstanceOf(ContentPart::class, $contentPart);

        return $contentPart;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger() : LoggerInterface
    {
        $name = 'acat-render-engine';
        $path = __DIR__ . '/../logs/acat-render-engine.log';
        $level = LogLevel::DEBUG;

        $logger = new Logger($name);

        $dateFormat = "d.m.Y - H:i:s";
        $output = "[%datetime%] %channel%.%level_name%: %message% \n";

        $formatter = new LineFormatter($output, $dateFormat, true);
        $formatter->includeStacktraces();

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler($path, $level);
        $handler->setFormatter($formatter);

        $logger->pushHandler($handler);

        return $logger;
    }

    /**
     * @return array
     */
    protected function getValues() : array
    {
        $values['156'] = 156;
        $values['1121'] = 1121;
        $values['1744'] = 1744;
        $values['1745'] = 1745;
        $values['1747'] = 1747;
        $values['1748'] = 1748;

        return $values;
    }

}