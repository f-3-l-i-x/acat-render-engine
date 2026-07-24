<?php

namespace Tests\Document\Word;

use PHPUnit\Framework\TestCase;
use ACAT\Exception\DocumentException;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Document\Word\PasswordEncoder;

/**
 *
 */
class PasswordEncoderTest extends TestCase
{

    #[Test]
    public function rejectsAnUnknownAlgorithm() : void {

        $this->expectException(DocumentException::class);

        PasswordEncoder::hashPassword('secret', 'bogus', '0123456789012345');

    }

    #[Test]
    public function restoresTheOriginalInternalEncodingEvenAfterAFailure() : void {

        $original = mb_internal_encoding();
        mb_internal_encoding('ISO-8859-1');

        try {
            try {
                PasswordEncoder::hashPassword('secret', 'bogus', '0123456789012345');
                $this->fail('expected a DocumentException');
            } catch (DocumentException) {
                // expected
            }

            $this->assertSame('ISO-8859-1', mb_internal_encoding());
        } finally {
            mb_internal_encoding($original);
        }

    }

    /**
     * @throws DocumentException
     */
    #[Test]
    public function ripemdProducesAHashInsteadOfCrashing() : void {

        $hash = PasswordEncoder::hashPassword('secret', PasswordEncoder::ALGORITHM_RIPEMD, '0123456789012345');

        $this->assertNotEmpty($hash);

    }

}
