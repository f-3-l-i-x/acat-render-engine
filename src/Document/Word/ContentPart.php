<?php

namespace ACAT\Document\Word;

use DOMXPath;
use DOMDocument;
use ACAT\Parser\ParserConstants;
use ACAT\Exception\DocumentException;

/**
 *
 */
class ContentPart
{

    /**
     * @var string
     */
    protected string $path;

    /**
     * @var string
     */
    protected string $content;

    /**
     * @var array|string[]
     */
    protected array $hierarchy = ['w:t', 'w:r', 'w:p'];

    /**
     * @var DOMXPath|null
     */
    protected ?DOMXPath $domXpath = null;

    /**
     * @var DOMDocument|null
     */
    protected ?DOMDocument $domDocument = null;

    public function __construct(string $content, string $path = "")
    {
        $this->path = $path;
        $this->content = $content;
    }

    /**
     * @return DOMXPath
     * @throws DocumentException
     */
    public function getXPath() : DOMXPath
    {
        if (!$this->domXpath) {
            $this->domXpath = new DOMXPath($this->getDomDocument());
            foreach ($this->getNamespaces() as $prefix => $url) {
                $this->domXpath->registerNamespace($prefix, $url);
            }
        }
        return $this->domXpath;
    }

    /**
     * @return DOMDocument
     * @throws DocumentException
     */
    public function getDomDocument() : DOMDocument
    {
        if (!$this->domDocument) {
            $domDocument = new DOMDocument('1.0', 'utf-8');

            $previousSetting = libxml_use_internal_errors(true);
            $parsed = $domDocument->loadXML($this->content);
            libxml_clear_errors();
            libxml_use_internal_errors($previousSetting);

            if (!$parsed) {
                throw new DocumentException('could not parse XML content of ' . ($this->path ?: 'content part'));
            }

            $this->domDocument = $domDocument;
        }
        return $this->domDocument;
    }

    /**
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getNamespaces() : array
    {
        return ParserConstants::$wordNamespaces;
    }

    /**
     * @return array
     */
    public function getHierarchy() : array
    {
        return $this->hierarchy;
    }

    /**
     * @return string
     */
    public function getContent() : string
    {
        return $this->getDomDocument()->saveXML();
    }

}