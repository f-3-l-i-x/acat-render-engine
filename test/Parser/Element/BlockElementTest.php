<?php

namespace Tests\Parser\Element;

use PHPUnit\Framework\TestCase;
use ACAT\Parser\Tag\TagGenerator;
use ACAT\Document\Word\ContentPart;
use ACAT\Exception\ElementException;
use ACAT\Exception\DocumentException;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Parser\Normalizer\Normalizer;
use ACAT\Parser\Element\ElementGenerator;

/**
 *
 */
class BlockElementTest extends TestCase
{

    /**
     * @throws ElementException
     * @throws DocumentException
     */
    #[Test]
    public function everyViewOfABlockIsPartOfItsFieldIds() : void {

        $contentPart = new ContentPart(
            '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body>'
            . '<w:p><w:r><w:t>${B:0}</w:t></w:r></w:p>'
            . '<w:p><w:r><w:t>${V:V_FOO} ${V:V_BAR} ${V:V_FOO} ${F:12} ${F:12} ${F:13}</w:t></w:r></w:p>'
            . '<w:p><w:r><w:t>${B:1}</w:t></w:r></w:p>'
            . '</w:body></w:document>',
            'word/document.xml'
        );

        (new Normalizer())->normalize($contentPart);
        (new TagGenerator($contentPart))->generateTags();

        $blocks = (new ElementGenerator($contentPart))->getBlocks();
        $this->assertCount(1, $blocks);

        $fieldIds = $blocks[0]->getFieldIds();

        $this->assertSame(['V_FOO', 'V_BAR'], array_values($fieldIds['views']));
        $this->assertSame(['12', '13'], array_values($fieldIds['fields']));

    }

}
