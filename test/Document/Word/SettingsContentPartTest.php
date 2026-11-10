<?php

namespace Tests\Document\Word;

use DOMException;
use PHPUnit\Framework\TestCase;
use ACAT\Exception\DocumentException;
use PHPUnit\Framework\Attributes\Test;
use ACAT\Document\Word\DocumentProtection;
use ACAT\Document\Word\SettingsContentPart;

/**
 *
 */
class SettingsContentPartTest extends TestCase
{

    private const string SETTINGS = '<w:settings xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
        . '<w:zoom w:percent="100"/>'
        . '</w:settings>';

    /**
     * @throws DocumentException
     * @throws DOMException
     */
    #[Test]
    public function protectingTwiceLeavesExactlyOneProtectionNode() : void {

        $settings = new SettingsContentPart(self::SETTINGS, 'word/settings.xml');

        $first = new DocumentProtection('first');
        $first->setSpinCount(10);

        $second = new DocumentProtection('second');
        $second->setSpinCount(10);

        $settings->protect($first);
        $settings->protect($second);

        // the content is parsed again, the protection node is created without a namespace
        $reloaded = new SettingsContentPart($settings->getContent(), 'word/settings.xml');
        $nodes = $reloaded->getXPath()->query('//w:settings/w:documentProtection');

        $this->assertCount(1, $nodes);
        $this->assertSame($second->getPassword(), $nodes->item(0)->getAttribute('w:hash'));

    }

}
