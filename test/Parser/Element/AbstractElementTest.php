<?php

namespace Tests\Parser\Element;

use PHPUnit\Framework\TestCase;
use ACAT\Document\Word\ContentPart;

/**
 *
 */
abstract class AbstractElementTest extends TestCase
{

    /**
     * @return ContentPart
     */
    protected function getWordContentPart() : ContentPart
    {
        return new ContentPart(file_get_contents(__DIR__ . '/../../Resources/Parser/Element/document.xml'));
    }

}