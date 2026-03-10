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
     *
     */
    private const string PLACEHOLDER_START = '$';
    /**
     *
     */
    private const string PLACEHOLDER_END = '}';

    /** @var DOMNode[] */
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
     * @param   LoggerInterface|null  $logger
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Normalizes the given content part by processing its text nodes and logging the output.
     *
     * @return void
     *
     * @param   ContentPart  $contentPart  The content part to be normalized.
     *
     */
    public function normalize(ContentPart $contentPart) : void
    {
        $this->log(LogLevel::INFO, 'starting normalizer for content part '.$contentPart->getPath());
        $this->contentPart = $contentPart;

        $textNodes = $this->contentPart->getXPath()->query(ParserConstants::WORD_TEXT_NODES);
        foreach ($textNodes as $textNode) {
            $this->processTextNode($textNode);
        }

        $this->log(LogLevel::DEBUG, $this->contentPart->getDomDocument()->saveXML());
        $this->log(LogLevel::INFO, 'normalizer finished with '.$textNodes->length.' text nodes');
    }

    /**
     * Processes a text node by removing placeholders and handling further processing
     * based on the node's value.
     *
     * @return void
     *
     * @param   DOMNode  $textNode  The text node to be processed.
     *
     */
    private function processTextNode(DOMNode $textNode) : void
    {
        $nodeValue = $textNode->nodeValue;
        $this->log(LogLevel::DEBUG, 'processing node with value '.$nodeValue);

        $nodeValue = $this->removePlaceholders($nodeValue);

        if ($this->shouldProcessNode($nodeValue)) {
            $this->handleNodeProcessing($textNode, $nodeValue);
        }
    }

    /**
     * @return bool
     *
     * @param   string  $nodeValue
     */
    private function shouldProcessNode(string $nodeValue) : bool
    {
        return !empty($this->textNodes)
            || str_contains($nodeValue, self::PLACEHOLDER_START)
            || str_contains($nodeValue, self::PLACEHOLDER_END);
    }

    /**
     * Processes a text node based on the value of its content and determines
     * whether to reset, accumulate, or process placeholders accordingly.
     *
     * @return void
     *
     * @param   string   $nodeValue  The text content of the node to be analyzed.
     *
     * @param   DOMNode  $textNode   The current text node being processed.
     */
    private function handleNodeProcessing(DOMNode $textNode, string $nodeValue) : void
    {
        if ($this->shouldResetNodes($nodeValue)) {
            $this->textNodes = [];
        }

        if (!str_contains($nodeValue, self::PLACEHOLDER_END)) {
            $this->textNodes[] = $textNode;
        } elseif ($this->shouldProcessPartialPlaceholder($nodeValue)) {
            $this->processPartialPlaceholder($textNode, $nodeValue);
        } else {
            $this->textNodes[] = $textNode;
            $this->mergeNodes();
        }
    }

    /**
     * Determines whether the nodes should be reset based on the given node value.
     *
     * @return bool Returns true if the nodes should be reset, false otherwise.
     *
     * @param   string  $nodeValue  The value of the node to evaluate.
     *
     */
    private function shouldResetNodes(string $nodeValue) : bool
    {
        return !empty($this->textNodes)
            && str_contains($nodeValue, self::PLACEHOLDER_START)
            && !str_contains($nodeValue, self::PLACEHOLDER_END);
    }

    /**
     * Determines if a partial placeholder should be processed based on the provided node value.
     *
     * @return bool Returns true if the node value contains the partial placeholder start and the textNodes property is not empty; otherwise, false.
     *
     * @param   string  $nodeValue  The value of the node to be checked for partial placeholders.
     */
    private function shouldProcessPartialPlaceholder(string $nodeValue) : bool
    {
        return !empty($this->textNodes) && str_contains($nodeValue, self::PLACEHOLDER_START);
    }

    /**
     * Processes a partial placeholder within a text node by appending the relevant portion to the last processed node,
     * updating the node value, and maintaining the textNodes list.
     *
     * @return  void  This method does not return any value.
     *
     * @param   string   $nodeValue  The value of the current node being processed for a partial placeholder.
     *
     * @param   DOMNode  $textNode   The text node containing the partial placeholder to process.
     */
    private function processPartialPlaceholder(DOMNode $textNode, string $nodeValue) : void
    {
        [$end] = explode(self::PLACEHOLDER_START, $nodeValue);
        $this->textNodes[count($this->textNodes) - 1]->nodeValue .= $end;
        $this->mergeNodes();
        $textNode->nodeValue = str_replace($end, '', $textNode->nodeValue);
        $this->textNodes[] = $textNode;
    }

    /**
     * Removes placeholders from the provided node value based on defined patterns.
     *
     * @return string Returns the node value after all placeholders have been removed.
     *
     * @param   string  $nodeValue  The value of the node from which placeholders should be removed.
     *
     */
    private function removePlaceholders(string $nodeValue) : string
    {
        $this->log(LogLevel::DEBUG, 'removing placeholder from node with value '.$nodeValue);

        preg_match_all(ParserConstants::MARKER_REG_EX, $nodeValue, $matches);
        if (array_key_exists(0, $matches)) {
            foreach ($matches[0] as $placeHolder) {
                $nodeValue = str_replace($placeHolder, '', $nodeValue);
            }
        }

        $this->log(LogLevel::DEBUG, 'removing finished. new value is '.$nodeValue);

        return $nodeValue;
    }

    /**
     * Merges text nodes by combining their values and cleaning up any additional nodes.
     *
     * This method retrieves the placeholder value, matches specific patterns within it,
     * and assigns the combined value to the first text node. Remaining text nodes are
     * cleaned up and the textNodes property is reset.
     *
     * @return void
     */
    private function mergeNodes() : void
    {
        $this->log(LogLevel::DEBUG, 'merging nodes');

        $placeHolderValue = $this->getPlaceHolderValue();
        preg_match_all(ParserConstants::MARKER_REG_EX, $placeHolderValue, $matches);

        if (!empty($matches)) {
            $this->textNodes[0]->nodeValue = $placeHolderValue;
            for ($i = 1; $i < count($this->textNodes); $i++) {
                $this->cleanUpNodes($this->textNodes[$i]);
            }
        }

        $this->textNodes = [];
        $this->log(LogLevel::DEBUG, 'merging finished');
    }

    /**
     * Retrieves the combined placeholder value by concatenating the values of text nodes.
     *
     * @return string Returns the concatenated placeholder value from all text nodes.
     */
    private function getPlaceHolderValue() : string
    {
        $value = implode('', array_map(fn(DOMNode $node) => $node->nodeValue, $this->textNodes));
        $this->log(LogLevel::DEBUG, 'new placeholder value '.$value);

        return $value;
    }

    /**
     * Cleans up a given DOM node by removing it and its ancestors from the DOM hierarchy
     * until a certain condition defined by the contentPart's hierarchy is met.
     *
     * @return  void  No value is returned as the method operates directly on the DOM structure.
     *
     * @param   DOMNode  $node  The DOM node to be cleaned up from the hierarchy.
     *
     */
    private function cleanUpNodes(DOMNode $node) : void
    {
        $this->log(LogLevel::DEBUG, 'cleaning up nodes');

        foreach ($this->contentPart->getHierarchy() as $level) {
            if (!$node->parentNode) {
                break;
            }

            $parentNode = $node->parentNode;
            $node->parentNode->removeChild($node);

            if ($this->contentPart->getXPath()->evaluate('count('.$parentNode->getNodePath().'/'.$level.')') > 0) {
                break;
            }

            $node = $parentNode;
        }

        $this->log(LogLevel::DEBUG, 'finished cleaning up nodes');
    }

    /**
     * @return void
     *
     * @param   string  $message
     * @param   string  $level
     */
    private function log(string $level, string $message) : void
    {
        $this->logger?->log($level, $message);
    }

}