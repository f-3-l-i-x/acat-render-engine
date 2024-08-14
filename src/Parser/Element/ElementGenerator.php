<?php

namespace ACAT\Parser\Element;

use DOMNode;
use Exception;
use DOMElement;
use DOMNodeList;
use Psr\Log\LogLevel;
use ACAT\Utils\DOMUtils;
use Psr\Log\LoggerInterface;
use ACAT\Parser\ParserConstants;
use ACAT\Exception\ElementException;
use ACAT\Document\Word\ContentPart;

/**
 *
 */
class ElementGenerator
{

    /**
     * @var LoggerInterface|null
     */
    private ?LoggerInterface $logger;

    /**
     * @var ContentPart
     */
    private ContentPart $contentPart;

    /**
     * @var array
     */
    private array $blocks = [];

    /**
     * @var array
     */
    private array $textBlocks = [];

    /**
     * @var array
     */
    private array $paragraphBlocks = [];

    /**
     * @var array
     */
    private array $tableCellBlocks = [];

    /**
     * @var array
     */
    private array $tableRowBlocks = [];

    /**
     * @var array|string[]
     */
    protected array $blockTypes = ['w:t', 'w:p', 'w:tc', 'w:tr'];

    /**
     * @param ContentPart $contentPart
     * @param LoggerInterface|null $logger
     * @throws ElementException
     */
    public function __construct(ContentPart $contentPart, LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        $this->contentPart = $contentPart;

        $this->generateElements();
    }

    /**
     * @return void
     * @throws ElementException
     */
    public function generateElements() : void
    {
        $this->blocks = $this->getBlocks();
    }

    /**
     * @return array
     * @throws ElementException
     */
    public function getBlocks() : array
    {
        if (!empty($this->blocks)) {
            return $this->blocks;
        }

        foreach ($this->getStartBlockNodes() as $startNode) {
            foreach ($this->blockTypes as $blockType) {
                $block = $this->getBlock($startNode, $blockType);
                if ($block) {
                    $this->blocks[] = $block;
                    if ($block instanceof TextBlock) {
                        $this->textBlocks[] = $block;
                    } elseif ($block instanceof ParagraphBlock) {
                        $this->paragraphBlocks[] = $block;
                    } elseif ($block instanceof TableCellBlock) {
                        $this->tableCellBlocks[] = $block;
                    } elseif ($block instanceof TableRowBlock) {
                        $this->tableRowBlocks[] = $block;
                    }
                    break;
                }
            }
        }

        return $this->blocks;
    }

    /**
     * @return DOMNodeList
     */
    protected function getStartBlockNodes() : DOMNodeList
    {
        return $this->contentPart->getXPath()->query("//acat:block[@type='start']");
    }

    /**
     * @param DOMNode $contextNode
     * @param string $blockType
     * @return BlockElement|null
     * @throws ElementException
     * @throws Exception
     */
    protected function getBlock(DOMNode $contextNode, string $blockType) : ?BlockElement
    {
        $children = [];
        $documentBlock = null;

        if ($blockType === 'w:t') {
            $parentNode = $contextNode;
        } else {
            $parentNode = DOMUtils::getParentNode($contextNode, $blockType);
        }

        if ($parentNode) {
            foreach ($this->getSiblings($parentNode) as $sibling) {
                $endBlockNode = $this->getEndBlockNode($sibling);
                if ($endBlockNode) {
                    if ($endBlockNode->getAttribute('type') == 'end') {
                        $documentBlock = match ($blockType) {
                            'w:t'   => new TextBlock($contextNode, $endBlockNode),
                            'w:p'   => new ParagraphBlock($contextNode, $endBlockNode),
                            'w:tc'  => new TableCellBlock($contextNode, $endBlockNode),
                            'w:tr'  => new TableRowBlock($contextNode, $endBlockNode),
                            default => throw new ElementException($blockType . ' is not supported'),
                        };
                        $documentBlock->setChildren($children);
                        break;
                    } else {
                        throw new ElementException('invalid block type');
                    }
                } else {
                    $children[] = new ChildBlockElement($sibling);
                }
            }
        }

        if ($documentBlock) {
            $documentBlock->setTextElements(
                $this->getElements(ParserConstants::ACAT_TEXT_NODES, $documentBlock->getChildren())
            );
            $documentBlock->setFieldElements(
                $this->getElements(ParserConstants::ACAT_FIELD_NODE, $documentBlock->getChildren())
            );
            $documentBlock->setViewElements(
                $this->getElements(ParserConstants::ACAT_VIEW_NODE, $documentBlock->getChildren())
            );
            $documentBlock->setConditionElements(
                $this->getElements(ParserConstants::ACAT_CONDITION_NODE, $documentBlock->getChildren())
            );
        }

        return $documentBlock;
    }

    /**
     * @param DOMNode $contextNode
     * @return DOMNodeList
     */
    protected function getSiblings(DOMNode $contextNode) : DOMNodeList
    {
        return $this->contentPart->getXPath()->query("following-sibling::*", $contextNode);
    }

    /**
     * @param DOMNode $contextNode
     * @return DOMNode|null
     */
    protected function getEndBlockNode(DOMNode $contextNode) : ?DOMElement
    {
        if ($contextNode->nodeName == 'acat:block') {
            return $contextNode;
        }

        $nodeList = $this->contentPart->getXPath()->query(".//acat:block[@type]", $contextNode);

        if ($nodeList->item(0)) {
            return $nodeList->item(0);
        }

        return null;
    }

    /**
     * @param string $elementType
     * @param array $children
     * @return array
     * @throws ElementException
     */
    protected function getElements(string $elementType, array $children) : array
    {
        $elements = [];

        foreach ($children as $child) {
            if ($elementType == ParserConstants::ACAT_TEXT_NODES) {
                $elements = array_merge($elements, $child->getTextElements());
            } elseif ($elementType == ParserConstants::ACAT_FIELD_NODE) {
                $elements = array_merge($elements, $child->getFieldElements());
            } elseif ($elementType == ParserConstants::ACAT_CONDITION_NODE) {
                $elements = array_merge($elements, $child->getConditionElements());
            } elseif ($elementType == ParserConstants::ACAT_VIEW_NODE) {
                $elements = array_merge($elements, $child->getViewElements());
            } else {
                throw new ElementException('type ' . $elementType . 'is not known');
            }
        }

        return $elements;
    }

    /**
     * @return array
     * @throws ElementException
     */
    public function getTextElements() : array
    {
        $this->log(LogLevel::INFO, 'looking for text elements in ' . $this->contentPart->getPath());

        $found = false;
        $textElements = [];

        $nodes = $this->getContentPart()->getXPath()->query('//' . ParserConstants::ACAT_TEXT_NODES);

        $this->log(
            LogLevel::INFO,
            'message found ' . $nodes->length . ' text nodes in ' . $this->contentPart->getPath()
        );

        foreach ($nodes as $node) {
            foreach ($this->getBlocks() as $block) {
                foreach ($block->getTextElements() as $textElement) {
                    if ($node->isSameNode($textElement->getElement())) {
                        $this->log(LogLevel::DEBUG, 'found text element ' . $node->nodeValue . ' in block -> skipping');
                        $found = true;
                        break;
                    }
                }
                if ($found) {
                    break;
                }
            }
            if (!$found) {
                $textElements[] = new TextElement($node);
            } else {
                $found = false;
            }
        }

        $this->log(LogLevel::INFO, 'processing ' . count($textElements) . ' text elements');

        return $textElements;
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
     * @return ContentPart
     */
    public function getContentPart() : ContentPart
    {
        return $this->contentPart;
    }

    /**
     * @return array
     * @throws ElementException
     */
    public function getFieldElements() : array
    {
        return $this->getContentElements();
    }

    /**
     * @param string $nodeType
     * @return array
     * @throws ElementException
     */
    public function getContentElements(string $nodeType = ParserConstants::ACAT_FIELD_NODE) : array
    {
        $this->log(LogLevel::INFO, 'looking for content elements in ' . $this->contentPart->getPath());

        $found = false;
        $fieldElements = [];

        $nodes = $this->getContentPart()->getXPath()->query('//' . $nodeType);

        $this->log(LogLevel::INFO, 'found ' . $nodes->length . ' content elements');

        foreach ($nodes as $node) {
            if ($nodeType === ParserConstants::ACAT_VIEW_NODE) {
                $fieldElement = new ViewElement($node);
            } else {
                $fieldElement = new FieldElement($node);
            }

            foreach ($this->getBlocks() as $block) {
                if (in_array($fieldElement->getId(), $block->getElementIds())) {
                    $this->log(LogLevel::DEBUG, $fieldElement->getType() . ' is already in block -> skipping');

                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $fieldElements[] = $fieldElement;
            } else {
                $found = false;
            }
        }

        $this->log(LogLevel::INFO, 'processing ' . count($fieldElements) . ' content elements');

        return $fieldElements;
    }

    /**
     * @return array
     * @throws ElementException
     */
    public function getConditionElements() : array
    {
        $found = false;
        $conditionElements = [];

        $this->log(LogLevel::INFO, 'looking for condition elements in ' . $this->contentPart->getPath());

        $nodes = $this->getContentPart()->getXPath()->query('//' . ParserConstants::ACAT_CONDITION_NODE);

        foreach ($nodes as $node) {
            $conditionElement = new ConditionElement($node);
            foreach ($this->getBlocks() as $block) {
                if (in_array($conditionElement->getId(), $block->getElementIds())) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $conditionElements[] = $conditionElement;
            } else {
                $found = false;
            }
        }

        return $conditionElements;
    }

    /**
     * @return array
     * @throws ElementException
     */
    public function getViewElements() : array
    {
        return $this->getContentElements(ParserConstants::ACAT_VIEW_NODE);
    }

    /**
     * @return array
     */
    public function getTextBlocks() : array
    {
        return $this->textBlocks;
    }

    /**
     * @return array
     */
    public function getTableRowBlocks() : array
    {
        return $this->tableRowBlocks;
    }

}