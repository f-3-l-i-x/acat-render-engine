<?php

namespace Tests\Document\Word;

use PHPUnit\Framework\TestCase;
use ACAT\Document\Word\ContentPart;
use ACAT\Exception\DocumentException;
use PHPUnit\Framework\Attributes\Test;

/**
 *
 */
class ContentPartTest extends TestCase
{

    #[Test]
    public function malformedXmlThrowsInsteadOfSilentlyProducingAnEmptyDocument() : void {

        $this->expectException(DocumentException::class);

        (new ContentPart('this is not XML'))->getDomDocument();

    }

    /**
     * @throws DocumentException
     */
    #[Test]
    public function wellFormedXmlIsParsedNormally() : void {

        $contentPart = new ContentPart('<root><child/></root>');

        $this->assertSame('child', $contentPart->getDomDocument()->documentElement->firstChild->nodeName);

    }

}
