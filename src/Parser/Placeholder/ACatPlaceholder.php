<?php

namespace ACAT\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use Symfony\Component\Uid\Uuid;
use ACAT\Exception\PlaceholderException;

/**
 *
 */
abstract class ACatPlaceholder
{

    /**
     * @var string
     */
    protected string $id;

    /**
     * @var string
     */
    protected string $namespace = 'https://schemas.acat.akademie.uni-bremen.de';

    /**
     * ACatNode constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    /**
     * @param string $placeHolderStr
     * @return ACatPlaceholder
     * @throws PlaceholderException
     */
    public static function getPlaceholder(string $placeHolderStr) : ACatPlaceholder
    {
        $placeHolderStr = str_replace(['$', '{', '}'], '', $placeHolderStr);

        if (str_starts_with($placeHolderStr, 'F')) {
            $placeHolderObj = self::createFieldPlaceholder($placeHolderStr);
        } elseif (str_starts_with($placeHolderStr, 'C')) {
            $placeHolderObj = self::createConditionPlaceholder($placeHolderStr);
        } elseif (str_starts_with($placeHolderStr, 'T')) {
            $placeHolderObj = self::createTextPlaceholder($placeHolderStr);
        } elseif (str_starts_with($placeHolderStr, 'B')) {
            $placeHolderObj = self::createBlockPlaceholder($placeHolderStr);
        } elseif (str_starts_with($placeHolderStr, 'V')) {
            $placeHolderObj = self::createViewPlaceHolder($placeHolderStr);
        } else {
            throw new PlaceholderException($placeHolderStr . ' is unsupported');
        }

        return $placeHolderObj;
    }

    /**
     * @param string $placeholderStr
     * @return FieldPlaceholder
     * @throws PlaceholderException
     */
    private static function createFieldPlaceholder(string $placeholderStr) : FieldPlaceholder
    {
        $params = explode(':', $placeholderStr);
        if (!$params || count($params) <> 2) {
            throw new PlaceholderException($placeholderStr . ' is malformed');
        }
        return new FieldPlaceholder($params[1]);
    }

    /**
     * @param string $placeholderStr
     * @return ConditionPlaceholder
     * @throws PlaceholderException
     */
    private static function createConditionPlaceholder(string $placeholderStr) : ConditionPlaceholder
    {
        $params = explode(':', $placeholderStr);
        if (count($params) != 4) {
            throw new PlaceholderException($placeholderStr . ' is malformed');
        }
        return new ConditionPlaceholder($params[1], $params[3], $params[2]);
    }

    /**
     * @param string $placeholderStr
     * @return TextPlaceholder
     * @throws PlaceholderException
     */
    private static function createTextPlaceholder(string $placeholderStr) : TextPlaceholder
    {
        $params = explode(':', $placeholderStr);
        if (!$params || count($params) <> 2) {
            throw new PlaceholderException($placeholderStr . ' is malformed');
        }
        return new TextPlaceholder($params[1]);
    }

    /**
     * @param string $placeholderStr
     * @return BlockPlaceholder
     * @throws PlaceholderException
     */
    private static function createBlockPlaceholder(string $placeholderStr) : BlockPlaceholder
    {
        $params = explode(':', $placeholderStr);

        if (count($params) != 2) {
            throw new PlaceholderException($placeholderStr . ' is malformed');
        }

        if ($params[1] == 0) {
            return new StartBlockPlaceholder();
        } elseif ($params[1] == 1) {
            return new EndBlockPlaceholder();
        } else {
            throw new PlaceholderException('block type ' . $params[1] . ' is not supported');
        }
    }

    /**
     * @param string $placeholderStr
     * @return ViewPlaceholder
     * @throws PlaceholderException
     */
    private static function createViewPlaceHolder(string $placeholderStr) : ViewPlaceholder
    {
        $params = explode(':', $placeholderStr);
        if (!$params || count($params) <> 2) {
            throw new PlaceholderException($placeholderStr . ' is malformed');
        }
        return new ViewPlaceholder($params[1]);
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    abstract public function getXMLTagAsString() : string;

    /**
     * @param DOMDocument $domDocument
     * @return DOMNode
     */
    abstract public function getDOMNode(DOMDocument $domDocument) : DOMNode;

    /**
     * @return int
     */
    abstract public function length() : int;

    /**
     * @return string
     */
    abstract public function getNodeAsString() : string;

}