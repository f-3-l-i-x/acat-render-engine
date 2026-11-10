<?php

namespace ACAT\Parser\Normalizer;

use DOMNode;
use Psr\Log\LogLevel;
use ACAT\Utils\DOMUtils;
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
     * The paragraph the accumulated text nodes belong to.
     *
     * @var DOMNode|null
     */
    private ?DOMNode $paragraph = null;

    /**
     * @var ContentPart
     */
    private ContentPart $contentPart;

    /**
     * Creates a normalizer, optionally logging its processing steps.
     *
     * @param   LoggerInterface|null  $logger
     */
    public function __construct(private readonly ?LoggerInterface $logger = null)
    {
    }

    /**
     * Reassembles placeholder markers that Word split across several text runs
     * (e.g. due to mixed formatting) into a single text node for every "w:t"
     * node in the given content part, so they can be parsed as one marker.
     *
     * @return void
     *
     * @param   ContentPart  $contentPart  The content part to be normalized.
     */
    public function normalize(ContentPart $contentPart): void
    {
        $this->log(LogLevel::INFO, 'starting normalizer for content part ' . $contentPart->getPath());
        $this->contentPart = $contentPart;

        // nodes accumulated in another content part must never be merged into this one
        $this->textNodes = [];
        $this->paragraph = null;

        $textNodes = $this->contentPart->getXPath()->query(ParserConstants::WORD_TEXT_NODES);
        foreach ($textNodes as $textNode) {
            $this->processTextNode($textNode);
        }

        $this->log(LogLevel::DEBUG, $this->contentPart->getDomDocument()->saveXML());
        $this->log(LogLevel::INFO, 'normalizer finished with ' . $textNodes->length . ' text nodes');
    }

    /**
     * Strips any complete placeholder markers already present in the node,
     * then feeds what remains into the run-merging state machine if a
     * partial marker still needs to be tracked or completed.
     *
     * @return void
     *
     * @param   DOMNode  $textNode  The text node to be processed.
     */
    private function processTextNode(DOMNode $textNode): void
    {
        $nodeValue = $textNode->nodeValue;
        $this->log(LogLevel::DEBUG, 'processing node with value ' . $nodeValue);

        $this->resetNodesOnNewParagraph($textNode);
        $nodeValue = $this->removePlaceholders($nodeValue);

        if ($this->shouldProcessNode($nodeValue)) {
            $this->handleNodeProcessing($textNode, $nodeValue);
        }
    }

    /**
     * A marker never spans more than one paragraph. Nodes accumulated in a
     * previous paragraph (e.g. because of a single "$" in the text) are
     * discarded, otherwise the next "}" would merge whole paragraphs.
     *
     * @return void
     *
     * @param   DOMNode  $textNode  The current text node being processed.
     */
    private function resetNodesOnNewParagraph(DOMNode $textNode): void
    {
        $paragraph = DOMUtils::getParentNode($textNode, 'w:p');

        if ($paragraph !== $this->paragraph) {
            $this->textNodes = [];
        }

        $this->paragraph = $paragraph;
    }

    /**
     * Determines whether the node needs further handling: either a merge is
     * already in progress, or the value still contains a stray marker
     * delimiter that could be the start or end of a split marker.
     *
     * @return bool
     *
     * @param   string  $nodeValue
     */
    private function shouldProcessNode(string $nodeValue): bool
    {
        return !empty($this->textNodes)
            || str_contains($nodeValue, self::PLACEHOLDER_START)
            || str_contains($nodeValue, self::PLACEHOLDER_END);
    }

    /**
     * Advances the run-merging state machine by one node: discards a stale
     * accumulation if a new marker is starting, keeps accumulating while the
     * marker is still open, or merges the accumulated nodes once it closes.
     *
     * @return void
     *
     * @param   string   $nodeValue  The text content of the node to be analyzed.
     * @param   DOMNode  $textNode   The current text node being processed.
     */
    private function handleNodeProcessing(DOMNode $textNode, string $nodeValue): void
    {
        if ($this->shouldResetNodes($nodeValue)) {
            $this->textNodes = [];
        }

        if (!str_contains($nodeValue, self::PLACEHOLDER_END)) {
            $this->textNodes[] = $textNode;
        } elseif ($this->shouldProcessPartialPlaceholder($nodeValue)) {
            $this->processPartialPlaceholder($textNode);
        } else {
            $this->textNodes[] = $textNode;
            $this->mergeNodes();
        }
    }

    /**
     * Determines whether the currently accumulated nodes must be discarded
     * because a new marker starts here without the pending one having closed.
     *
     * @return bool
     *
     * @param   string  $nodeValue  The value of the node to evaluate.
     */
    private function shouldResetNodes(string $nodeValue): bool
    {
        return !empty($this->textNodes)
            && str_contains($nodeValue, self::PLACEHOLDER_START)
            && !str_contains($nodeValue, self::PLACEHOLDER_END);
    }

    /**
     * Determines whether this node both closes the pending marker and opens
     * another one within the same run (e.g. "...}some text${...").
     *
     * @return bool
     *
     * @param   string  $nodeValue  The value of the node to be checked for partial placeholders.
     */
    private function shouldProcessPartialPlaceholder(string $nodeValue): bool
    {
        return !empty($this->textNodes) && str_contains($nodeValue, self::PLACEHOLDER_START);
    }

    /**
     * Closes the pending marker with the text up to the first marker end,
     * merges it, then starts a new accumulation with the rest of the node,
     * which contains the start of the next marker. The node is cut by
     * position, so complete markers in between stay where they are.
     *
     * @return void
     *
     * @param   DOMNode  $textNode   The text node containing the partial placeholder to process.
     */
    private function processPartialPlaceholder(DOMNode $textNode): void
    {
        $nodeValue = $textNode->nodeValue;
        $length = strpos($nodeValue, self::PLACEHOLDER_END) + 1;

        array_last($this->textNodes)->nodeValue .= substr($nodeValue, 0, $length);
        $this->mergeNodes();

        $textNode->nodeValue = substr($nodeValue, $length);
        $this->textNodes[] = $textNode;
    }

    /**
     * Removes every complete "${...}" marker already contained in the value,
     * leaving only plain text and any not-yet-closed marker fragment behind.
     *
     * @return string
     *
     * @param   string  $nodeValue  The value of the node from which placeholders should be removed.
     */
    private function removePlaceholders(string $nodeValue): string
    {
        $this->log(LogLevel::DEBUG, 'removing placeholder from node with value ' . $nodeValue);

        preg_match_all(ParserConstants::MARKER_REG_EX, $nodeValue, $matches);
        if (array_key_exists(0, $matches)) {
            foreach ($matches[0] as $placeHolder) {
                $nodeValue = str_replace($placeHolder, '', $nodeValue);
            }
        }

        $this->log(LogLevel::DEBUG, 'removing finished. new value is ' . $nodeValue);

        return $nodeValue;
    }

    /**
     * Combines the accumulated nodes' text and, only if it actually forms a
     * valid marker, writes it to the first node and removes the rest from
     * the document; otherwise the nodes are left untouched.
     *
     * @return void
     */
    private function mergeNodes(): void
    {
        $this->log(LogLevel::DEBUG, 'merging nodes');

        $placeHolderValue = $this->getPlaceHolderValue();
        preg_match_all(ParserConstants::MARKER_REG_EX, $placeHolderValue, $matches);

        if (!empty($matches[0])) {
            $this->textNodes[0]->nodeValue = $placeHolderValue;
            for ($i = 1; $i < count($this->textNodes); $i++) {
                $this->cleanUpNodes($this->textNodes[$i]);
            }
        }

        $this->textNodes = [];
        $this->log(LogLevel::DEBUG, 'merging finished');
    }

    /**
     * Concatenates the text of all accumulated nodes into a single string.
     *
     * @return string
     */
    private function getPlaceHolderValue(): string
    {
        $value = implode('', array_map(fn(DOMNode $node) => $node->nodeValue, $this->textNodes));
        $this->log(LogLevel::DEBUG, 'new placeholder value ' . $value);

        return $value;
    }

    /**
     * Removes the given node and, as long as it leaves its parent without any
     * remaining children of the configured hierarchy (e.g. "w:t", "w:r",
     * "w:p"), removes that now-empty parent too, walking up the tree.
     *
     * @return void
     *
     * @param   DOMNode  $node  The DOM node to be cleaned up from the hierarchy.
     */
    private function cleanUpNodes(DOMNode $node): void
    {
        $this->log(LogLevel::DEBUG, 'cleaning up nodes');

        foreach ($this->contentPart->getHierarchy() as $level) {
            if (!$node->parentNode) {
                break;
            }

            $parentNode = $node->parentNode;
            $node->parentNode->removeChild($node);

            $siblingQuery = 'count(' . $parentNode->getNodePath() . '/' . $level . ')';
            if ($this->contentPart->getXPath()->evaluate($siblingQuery) > 0) {
                break;
            }

            $node = $parentNode;
        }

        $this->log(LogLevel::DEBUG, 'finished cleaning up nodes');
    }

    /**
     * Forwards a message to the configured logger, if any.
     *
     * @return void
     *
     * @param   string  $level
     * @param   string  $message
     */
    private function log(string $level, string $message): void
    {
        $this->logger?->log($level, $message);
    }
}
