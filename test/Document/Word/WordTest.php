<?php

namespace Tests\Document\Word;

use PHPUnit\Framework\TestCase;
use ACAT\Document\Word\WordDocument;
use ACAT\Exception\DocumentException;
use PHPUnit\Framework\Attributes\Test;

/**
 *
 */
class WordTest extends TestCase
{

    /**
     *
     * @throws DocumentException
     *@return void
     */
    #[Test]
    public function createWordDocument() : void
    {
        $path = __DIR__ . '/../../Resources/Document/Word/TEST.docx';
        $wordDocument = new WordDocument($path);

        $this->assertInstanceOf(WordDocument::class, $wordDocument);
    }

    /**
     *
     * @throws DocumentException
     *@return void
     */
    #[Test] public function invalidWordDocument() : void
    {
        $path = __DIR__ . '/../../Resources/Document/Word/invalid_word_document';
        $wordDocument = new WordDocument($path);

        $this->assertInstanceOf(WordDocument::class, $wordDocument);

        $this->expectException(DocumentException::class);
        $wordDocument->open();
    }

}