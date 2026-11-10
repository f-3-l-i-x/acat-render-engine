<?php

namespace Tests\Utils;

use DOMElement;
use DOMDocument;
use ACAT\Utils\DOMUtils;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 *
 */
class DOMUtilsTest extends TestCase
{

    /**
     *
     */
    private const string NAMESPACE = 'xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"';

    /**
     * @var DOMDocument|null
     */
    private ?DOMDocument $domDocument = null;

    /**
     * @return void
     */
    #[Test]
    public function getParentNodeInDocument() : void
    {
        $textNode = $this->getTextNode('w:document');

        $this->assertSame('w:t', DOMUtils::getParentNode($textNode, 'w:t')->nodeName);
        $this->assertSame('w:p', DOMUtils::getParentNode($textNode, 'w:p')->nodeName);
        $this->assertSame('w:r', DOMUtils::getParentNode($textNode)->nodeName);
        $this->assertNull(DOMUtils::getParentNode($textNode, 'w:tc'));
    }

    /**
     * header and footer do not have a w:document root node
     *
     * @return void
     */
    #[Test]
    public function getParentNodeInHeader() : void
    {
        $textNode = $this->getTextNode('w:hdr');

        $this->assertSame('w:p', DOMUtils::getParentNode($textNode, 'w:p')->nodeName);
        $this->assertNull(DOMUtils::getParentNode($textNode, 'w:tc'));
    }

    /**
     * the search never leaves the given root node
     *
     * @return void
     */
    #[Test]
    public function getParentNodeStopsAtRootNode() : void
    {
        $textNode = $this->getTextNode('w:hdr');

        $this->assertSame('w:hdr', DOMUtils::getParentNode($textNode, 'w:hdr')->nodeName);
        $this->assertNull(DOMUtils::getParentNode($textNode, 'w:hdr', 'w:p'));
    }

    /**
     * @return void
     */
    #[Test]
    public function getParentNodeInRemovedNodes() : void
    {
        $textNode = $this->getTextNode('w:document');

        $run = $textNode->parentNode;
        $run->parentNode->removeChild($run);

        $this->assertSame('w:r', DOMUtils::getParentNode($textNode, 'w:r')->nodeName);
        $this->assertNull(DOMUtils::getParentNode($textNode, 'w:p'));
        $this->assertFalse(DOMUtils::isRemoved($textNode));
        $this->assertTrue(DOMUtils::isRemoved($run));
    }

    /**
     * @param string $rootNode
     * @return DOMElement
     */
    private function getTextNode(string $rootNode) : DOMElement
    {
        $domDocument = new DOMDocument('1.0', 'utf-8');
        $domDocument->loadXML(
            '<' . $rootNode . ' ' . self::NAMESPACE . '><w:p><w:r><w:t>text</w:t></w:r></w:p></' . $rootNode . '>'
        );

        // the document has to outlive this method, otherwise its nodes are released
        $this->domDocument = $domDocument;

        return $domDocument->getElementsByTagName('t')->item(0);
    }

}
