<?php

namespace Tests\Document\Word;

use PHPUnit\Framework\TestCase;
use ACAT\Document\Word\WordDocument;
use ACAT\Exception\DocumentException;

class WordTest extends TestCase
{

    /**
     * @test
     *
     * @return void
     * @throws DocumentException
     */
    public function createWordDocument() : void
    {
        $path = __DIR__ . '/../../Resources/Document/Word/TEST.docx';
        $wordDocument = new WordDocument($path);

        $this->assertInstanceOf(WordDocument::class, $wordDocument);
    }

    /**
     * @test
     *
     * @return void
     * @throws DocumentException
     */
    public function invalidWordDocument() : void
    {
        $path = __DIR__ . '/../../Resources/Document/Word/invalid_word_document';
        $wordDocument = new WordDocument($path);

        $this->assertInstanceOf(WordDocument::class, $wordDocument);

        $this->expectException(DocumentException::class);
        $wordDocument->open();
    }

}