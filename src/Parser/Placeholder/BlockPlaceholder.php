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
     * @return string
     * @throws PlaceholderException
     */
    public function getXMLTagAsString() : string
    {
        return '<acat:block type="' . $this->getType() . '" />';
    }

    /**
     * @return string
     * @throws PlaceholderException
     */
    public function getType() : string
    {
        if ($this->isStart()) {
            return "start";
        } elseif ($this->isEnd()) {
            return "end";
        }
        throw new PlaceholderException("unsupported block type");
    }

    /**
     * @return bool
     */
    public function isStart() : bool
    {
        return $this->type == 0;
    }

    /**
     * @return bool
     */
    public function isEnd() : bool
    {
        return $this->type == 1;
    }

    /**
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws PlaceholderException
     * @throws DOMException
     */
    public function getDOMNode(DOMDocument $domDocument) : DOMNode
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
     * @return int
     */
    public function length() : int
    {
        return strlen($this->getNodeAsString());
    }

    /**
     * @return string
     */
    public function getNodeAsString() : string
    {
        return '${B:' . $this->type . '}';
    }
}