<?php

namespace Tests\Render;

use ACAT\Document\Word\WordDocument;
use ACAT\Render\RecordStructure;
use PHPUnit\Framework\Attributes\Test;

/**
 *
 */
class RecordStructureTest extends AbstractRenderTest
{

    private const string WELL_FORMED_TEMPLATE = __DIR__ . '/../Resources/Render/RenderEngine/document.docx';

    private const string BROKEN_TEMPLATE = __DIR__ . '/../Resources/Render/RecordStructure/broken_blocks.docx';

    #[Test]
    public function aWellFormedTemplateHasNoValidationProblems() : void {

        $structure = new RecordStructure(new WordDocument(self::WELL_FORMED_TEMPLATE));

        $this->assertSame([], $structure->validate());

    }

    #[Test]
    public function reportsABlockMarkerThatCouldNotBePaired() : void {

        $structure = new RecordStructure(new WordDocument(self::BROKEN_TEMPLATE));

        $problems = $structure->validate();

        $this->assertArrayHasKey('word/document.xml', $problems);
        $this->assertNotEmpty($problems['word/document.xml']);

    }

}
