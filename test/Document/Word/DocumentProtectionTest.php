<?php

namespace Tests\Document\Word;

use PHPUnit\Framework\TestCase;
use ACAT\Exception\DocumentException;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Document\Word\PasswordEncoder;
use ACAT\Document\Word\DocumentProtection;

/**
 *
 */
class DocumentProtectionTest extends TestCase
{

    #[Test]
    public function rejectsAnUnknownAlgorithm() : void {

        $this->expectException(DocumentException::class);

        (new DocumentProtection('secret'))->setAlgorithm('bogus');

    }

    #[Test]
    public function rejectsAnUnknownEditMode() : void {

        $this->expectException(DocumentException::class);

        (new DocumentProtection('secret'))->setEditing('bogus');

    }

    #[Test]
    public function acceptsEveryDocumentedEditMode() : void {

        $protection = new DocumentProtection('secret');

        foreach ([
            DocumentProtection::EDIT_NONE,
            DocumentProtection::EDIT_READ_ONLY,
            DocumentProtection::EDIT_COMMENTS,
            DocumentProtection::EDIT_FORMS,
            DocumentProtection::EDIT_TRACKED_CHANGES,
        ] as $mode) {
            $protection->setEditing($mode);
            $this->assertSame($mode, $protection->getEditing());
        }

    }

    /**
     * @throws DocumentException
     */
    #[Test]
    public function everyDocumentedAlgorithmProducesAHash() : void {

        foreach (PasswordEncoder::getAlgorithmNames() as $algorithm) {
            $protection = new DocumentProtection('secret');
            $protection->setAlgorithm($algorithm);

            $this->assertNotEmpty($protection->getPassword());
        }

    }

}
