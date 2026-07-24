<?php

namespace Tests\Render;

use DOMDocument;
use ACAT\Render\RenderEngine;
use ACAT\Document\Word\WordDocument;
use PHPUnit\Framework\Attributes\Test;

/**
 *
 */
class RenderEngineTest extends AbstractRenderTest
{

    private const string TEMPLATE = __DIR__ . '/../Resources/Render/RenderEngine/document.docx';

    private function renderedDocument(array $values) : DOMDocument {

        $workingCopy = __DIR__ . '/../Resources/Render/RenderEngine/document_lck.docx';
        copy(self::TEMPLATE, $workingCopy);

        $wordDocument = new WordDocument($workingCopy);
        (new RenderEngine())->render($wordDocument, ['word/document.xml' => $values]);

        // reopen from disk to make sure the rendered result was actually persisted
        $reopened = new WordDocument($workingCopy);
        $reopened->open();
        $domDocument = $reopened->getContentParts()['word/document.xml']->getDomDocument();
        $reopened->close();

        return $domDocument;

    }

    #[Test]
    public function rendersFieldsConditionsTextAndABlockEndToEnd() : void {

        $domDocument = $this->renderedDocument([
            'fields' => [
                '301' => 'Erika',
                '302' => '4711',
                '303' => '1',
            ],
            'blocks' => [
                0 => [
                    'fields' => [
                        0 => ['304' => 'A'],
                        1 => ['304' => 'B'],
                    ],
                ],
            ],
        ]);

        $text = $domDocument->documentElement->textContent;
        $xml = $domDocument->saveXML();

        $this->assertStringContainsString('Hallo Erika, deine Nummer ist 4711.', $text);
        $this->assertStringContainsString('Bedingter Absatz.', $text);
        $this->assertStringContainsString('Dieser Text ist immer da.', $text);
        $this->assertStringContainsString('Positionen: A B Ende.', $text);

        $this->assertStringNotContainsString('${', $xml);
        $this->assertStringNotContainsString('acat:field', $xml);
        $this->assertStringNotContainsString('acat:condition', $xml);
        $this->assertStringNotContainsString('acat:text', $xml);
        $this->assertStringNotContainsString('acat:block', $xml);

    }

    #[Test]
    public function removesTheWholeParagraphWhenItsConditionMatches() : void {

        $domDocument = $this->renderedDocument([
            'fields' => [
                '301' => 'Erika',
                '302' => '4711',
                '303' => '0',
            ],
            'blocks' => [
                0 => [
                    'fields' => [
                        0 => ['304' => 'A'],
                    ],
                ],
            ],
        ]);

        $text = $domDocument->documentElement->textContent;

        $this->assertStringNotContainsString('Bedingter Absatz.', $text);

    }

}
