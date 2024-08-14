<?php

namespace ACAT\Parser\Tag;

use DOMNode;
use DOMNodeList;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use ACAT\Parser\ParserConstants;
use ACAT\Document\Word\ContentPart;
use ACAT\Exception\PlaceholderException;
use ACAT\Parser\Placeholder\ACatPlaceholder;
use ACAT\Parser\Placeholder\WordTextPlaceholder;

/**
 *
 */
class TagGenerator
{

    /**
     * @var ContentPart
     */
    protected ContentPart $contentPart;
    /**
     * @var LoggerInterface|null
     */
    private ?LoggerInterface $logger;

    /**
     * @param ContentPart $contentPart
     * @param LoggerInterface|null $logger
     */
    public function __construct(ContentPart $contentPart, LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        $this->contentPart = $contentPart;
    }

    /**
     * @param array $matches
     * @param string $nodeValue
     * @return array
     */
    public function getTags(array $matches, string $nodeValue) : array
    {
        $nodes = [];
        $currentIndex = 0;

        foreach ($matches[0] as $match) {
            $value = $match[0];
            $offset = $match[1];

            $this->log(LogLevel::DEBUG, 'replacing "' . $nodeValue . '" from "' . $value . '"');

            if ($offset > 0 && $currentIndex < $offset) {
                $currentValue = substr($nodeValue, $currentIndex, ($offset - $currentIndex));
                $node = new WordTextPlaceholder($currentValue);
                $currentIndex = $currentIndex + $node->length();

                $this->log(LogLevel::DEBUG, 'created node ' . $node->getXMLTagAsString());

                $nodes[] = $node;
            }

            try {
                $node = ACatPlaceholder::getPlaceholder($value);
            } catch (PlaceholderException $e) {
                $this->log(LogLevel::WARNING, $e);
                $node = new WordTextPlaceholder($value);
            }

            $this->log(LogLevel::DEBUG, 'created node ' . $node->getXMLTagAsString());

            $currentIndex = $currentIndex + $node->length();
            $nodes[] = $node;
        }

        if ($currentIndex > 0 && $currentIndex < strlen($nodeValue)) {
            $currentValue = substr($nodeValue, $currentIndex, (strlen($nodeValue) - $currentIndex));
            $node = new WordTextPlaceholder($currentValue);

            $this->log(LogLevel::DEBUG, 'created node ' . $node->getXMLTagAsString());

            $nodes[] = $node;
        }

        $this->log(LogLevel::INFO, 'created ' . count($nodes) . ' nodes');

        return $nodes;
    }

    /**
     * @param string $level
     * @param string $message
     * @return void
     */
    protected function log(string $level, string $message) : void
    {
        $this->logger?->log($level, $message);
    }

    /**
     * @return void
     */
    public function generateTags() : void
    {
        $this->log(LogLevel::INFO, 'starting tag generator for content part ' . $this->contentPart->getPath());

        $textNodes = $this->getTextNodes();

        $this->log(LogLevel::DEBUG, 'content part hast ' . $textNodes->length . ' text nodes');

        foreach ($textNodes as $textNode) {
            preg_match_all(ParserConstants::MARKER_REG_EX, $textNode->nodeValue, $matches, PREG_OFFSET_CAPTURE);
            if ($matches[0]) {
                $nodes = $this->getTags($matches, $textNode->nodeValue);
                $this->insertNodes($nodes, $textNode);
            }
        }

        $this->log(LogLevel::DEBUG, $this->contentPart->getDomDocument()->saveXML());
        $this->log(LogLevel::INFO, 'finished tag generator');
    }

    /**
     * @return DOMNodeList
     */
    protected function getTextNodes() : DOMNodeList
    {
        return $this->contentPart->getXPath()->query(ParserConstants::WORD_TEXT_NODES);
    }

    /**
     * @param array $nodes
     * @param DOMNode $textNode
     */
    private function insertNodes(array $nodes, DOMNode $textNode) : void
    {
        $this->log(LogLevel::INFO, 'inserting tags in content part');

        $beforeNode = $textNode;

        for ($i = count($nodes) - 1; $i >= 0; $i--) {
            $this->log(LogLevel::DEBUG, 'inserting tag ' . $nodes[$i]->getXMLTagAsString());

            $insertNode = $nodes[$i]->getDOMNode($this->contentPart->getDomDocument());
            $beforeNode = $textNode->parentNode->insertBefore($insertNode, $beforeNode);
        }

        $textNode->parentNode->removeChild($textNode);
    }

}