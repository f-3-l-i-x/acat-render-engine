<?php

namespace ACAT\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;
use ACAT\Exception\PlaceholderException;

/**
 *
 */
abstract class BlockPlaceholder extends ACatPlaceholder
{
    /**
     * @var int
     */
    private int $type = 0;

    /**
     * Creates a block placeholder of the given type (0 = start, 1 = end).
     *
     * @param int $type
     * @throws PlaceholderException
     */
    public function __construct(int $type)
    {
        if ($type < 0 || $type > 1) {
            throw new PlaceholderException('unsupported block type');
        }

        $this->type = $type;
        parent::__construct();
    }

    /**
     * Returns a human-readable representation of the block marker, used for
     * logging.
     *
     * @return string
     * @throws PlaceholderException
     */
    public function getXMLTagAsString(): string
    {
        return '<acat:block type="' . $this->getType() . '" />';
    }

    /**
     * Returns the block's type as "start" or "end".
     *
     * @return string
     * @throws PlaceholderException
     */
    public function getType(): string
    {
        if ($this->isStart()) {
            return "start";
        } elseif ($this->isEnd()) {
            return "end";
        }
        throw new PlaceholderException("unsupported block type");
    }

    /**
     * Determines whether this is a block-start marker.
     *
     * @return bool
     */
    public function isStart(): bool
    {
        return $this->type == 0;
    }

    /**
     * Determines whether this is a block-end marker.
     *
     * @return bool
     */
    public function isEnd(): bool
    {
        return $this->type == 1;
    }

    /**
     * Builds the "acat:block" DOM element carrying the placeholder's id and
     * type.
     *
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws PlaceholderException
     * @throws DOMException
     */
    public function getDOMNode(DOMDocument $domDocument): DOMNode
    {
        $elementNode = $domDocument->createElementNS($this->namespace, 'acat:block');

        $idAttribute = $domDocument->createAttribute('id');
        $idAttribute->value = $this->getId();

        $typeAttribute = $domDocument->createAttribute('type');
        $typeAttribute->value = $this->getType();

        $elementNode->appendChild($idAttribute);
        $elementNode->appendChild($typeAttribute);

        return $elementNode;
    }

    /**
     * Returns the length of the original "${B:0}"/"${B:1}" marker text.
     *
     * @return int
     */
    public function length(): int
    {
        return strlen($this->getNodeAsString());
    }

    /**
     * Returns the original "${B:0}"/"${B:1}" marker text.
     *
     * @return string
     */
    public function getNodeAsString(): string
    {
        return '${B:' . $this->type . '}';
    }
}
