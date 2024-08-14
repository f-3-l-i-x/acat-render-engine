<?php

namespace Tests\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;
use PHPUnit\Framework\TestCase;
use ACAT\Document\Word\DocumentProtection;
use ACAT\Parser\Placeholder\WordDocumentProtectionPlaceholder;

class WordDocumentProtectionPlaceholderTest extends TestCase
{

    /**
     * @test
     * @throws DOMException
     */
    public function aWordDocumentProtectionPlaceholderCanBeCreated() : void
    {
        $documentProtection = new DocumentProtection();

        $this->assertInstanceOf(DocumentProtection::class, $documentProtection);
        $this->assertNotNull($documentProtection->getPassword());
        $this->assertNotNull($documentProtection->getSalt());

        $wordDocumentProtectionPlaceholder = new WordDocumentProtectionPlaceholder($documentProtection);
        $this->assertInstanceOf(WordDocumentProtectionPlaceholder::class, $wordDocumentProtectionPlaceholder);

        $this->assertIsString($wordDocumentProtectionPlaceholder->getXMLTagAsString());
        $this->assertIsInt($wordDocumentProtectionPlaceholder->length());
        $this->assertGreaterThan(0, $wordDocumentProtectionPlaceholder->length());

        $domDocument = new DOMDocument('1.0', "UTF-8");
        $domNode = $wordDocumentProtectionPlaceholder->getDOMNode($domDocument);

        $this->assertInstanceOf(DOMNode::class, $domNode);

        $this->assertNotNull($domNode->getAttribute('w:salt'));
        $this->assertNotNull($domNode->getAttribute('w:hash'));
        $this->assertNotNull($domNode->getAttribute('w:edit'));

        $this->assertNotNull($domNode->getAttribute('w:cryptSpinCount'));
        $this->assertEquals(100000, $domNode->getAttribute('w:cryptSpinCount'));

        $this->assertNotNull($domNode->getAttribute('w:cryptAlgorithmSid'));

        $this->assertNotNull($domNode->getAttribute('w:cryptAlgorithmType'));
        $this->assertEquals("typeAny", $domNode->getAttribute('w:cryptAlgorithmType'));

        $this->assertNotNull($domNode->getAttribute('w:cryptAlgorithmClass'));
        $this->assertEquals("hash", $domNode->getAttribute('w:cryptAlgorithmClass'));

        $this->assertNotNull($domNode->getAttribute('w:cryptProviderType'));
        $this->assertEquals("rsaFull", $domNode->getAttribute('w:cryptProviderType'));

        $this->assertNotNull($domNode->getAttribute('w:enforcement'));
        $this->assertEquals("1", $domNode->getAttribute('w:enforcement'));
    }

}