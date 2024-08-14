<?php

namespace ACAT\Parser\Normalizer;

use DOMNode;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use ACAT\Parser\ParserConstants;
use ACAT\Document\Word\ContentPart;


/**
 *
 */
class Normalizer
{

    /**
     * @var array
     */
    private array $textNodes = [];

    /**
     * @var ContentPart
     */
    private ContentPart $contentPart;

    /**
     * @var LoggerInterface|null
     */
    private ?LoggerInterface $logger;

    /**
     * @param LoggerInterface|null $logger
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param ContentPart $contentPart
     * @return void
     */
    public function normalize(ContentPart $contentPart) : void
    {
        $this->log(LogLevel::INFO, 'starting normalizer for content part ' . $contentPart->getPath());

        $this->contentPart = $contentPart;
        $textNodes = $this->contentPart->getXPath()->query(ParserConstants::WORD_TEXT_NODES);

        foreach ($textNodes as $textNode) {
            $this->processTextNode($textNode);
        }

        $this->log(LogLevel::DEBUG, $this->contentPart->getDomDocument()->saveXML());
        $this->log(LogLevel::INFO, 'normalizer finished with ' . $textNodes->length . ' text nodes');
    }

    /**
     * @param string $level
     * @param string $message
     * @return void
     */
    private function log(string $level, string $message) : void
    {
        $this->logger?->log($level, $message);
    }

    /**
     * @param DOMNode $textNode
     */
    private function processTextNode(DOMNode $textNode) : void
    {
        $nodeValue = $textNode->nodeValue;
        $this->log(LogLevel::DEBUG, 'processing node with value ' . $nodeValue);

        preg_match_all(ParserConstants::MARKER_REG_EX, $nodeValue, $matches);
        $nodeValue = $this->removePlaceHoldersFromNodeValue($matches, $nodeValue);

        if ((!empty($this->textNodes) || str_contains($nodeValue, '$') || str_contains($nodeValue, '}'))) {
            if (!empty($this->textNodes) && str_contains($nodeValue, '$') && !str_contains($nodeValue, '}')) {
                $this->textNodes = [];
            }

            if (!str_contains($nodeValue, '}')) {
                $this->textNodes[] = $textNode;
            } elseif (!empty($this->textNodes) && str_contains($nodeValue, '$')) {
                [$end] = explode('$', $nodeValue);
                $this->textNodes[count($this->textNodes) - 1]->nodeValue .= $end;

                $this->mergeNodes();

                $textNode->nodeValue = str_replace($end, '', $textNode->nodeValue);
                $this->textNodes[] = $textNode;
            } else {
                $this->textNodes[] = $textNode;
                $this->mergeNodes();
            }
        }
    }

    /**
     * @param array $placeHolders
     * @param string $nodeValue
     * @return string
     */
    private function removePlaceHoldersFromNodeValue(array $placeHolders, string $nodeValue) : string
    {
        $this->log(LogLevel::DEBUG, 'removing placeholder from node with value ' . $nodeValue);

        if (array_key_exists(0, $placeHolders)) {
            foreach ($placeHolders[0] as $placeHolder) {
                $nodeValue = str_replace($placeHolder, '', $nodeValue);
            }
        }

        $this->log(LogLevel::DEBUG, 'removing finished. new value is ' . $nodeValue);

        return $nodeValue;
    }

    /**
     *
     */
    private function mergeNodes() : void
    {
        $this->log(LogLevel::DEBUG, 'merging nodes');

        $placeHolder = $this->getPlaceHolderValue();
        preg_match_all(ParserConstants::MARKER_REG_EX, $placeHolder, $matches);

        if (!empty($matches)) {
            $this->textNodes[0]->nodeValue = $placeHolder;
            for ($i = 1; $i < count($this->textNodes); $i++) {
                $this->cleanUpNodes($this->textNodes[$i]);
            }
        }

        $this->textNodes = [];

        $this->log(LogLevel::DEBUG, 'merging finished');
    }

    /**
     * @return string
     */
    private function getPlaceHolderValue() : string
    {
        $value = "";

        foreach ($this->textNodes as $textNode) {
            $value .= $textNode->nodeValue;
        }

        $this->log(LogLevel::DEBUG, 'new placeholder value ' . $value);

        return $value;
    }

    /**
     * @param DOMNode $node
     */
    private function cleanUpNodes(DOMNode $node) : void
    {
        $this->log(LogLevel::DEBUG, 'cleaning up nodes');

        foreach ($this->contentPart->getHierarchy() as $level) {
            if ($node->parentNode) {
                $parentNode = $node->parentNode;
                $node->parentNode->removeChild($node);
                if ($this->contentPart->getXPath()
                                      ->evaluate('count(' . $parentNode->getNodePath() . '/' . $level . ')') > 0) {
                    return;
                }
                $node = $parentNode;
            } else {
                return;
            }
        }
        $this->log(LogLevel::DEBUG, 'finished cleaning up nodes');
    }

}