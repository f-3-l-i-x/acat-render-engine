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
     * Creates a placeholder with a freshly generated UUID as its id.
     */
    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    /**
     * Parses a "${...}" marker string and creates the matching placeholder
     * subtype (field, condition, text, block or view) based on its leading
     * letter.
     *
     * @param string $placeHolderStr
     * @return ACatPlaceholder
     * @throws PlaceholderException
     */
    public static function getPlaceholder(string $placeHolderStr): ACatPlaceholder
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
     * Creates a field placeholder from a "F:<fieldId>" marker string.
     *
     * @param string $placeholderStr
     * @return FieldPlaceholder
     * @throws PlaceholderException
     */
    private static function createFieldPlaceholder(string $placeholderStr): FieldPlaceholder
    {
        $params = explode(':', $placeholderStr);
        if (!$params || count($params) <> 2) {
            throw new PlaceholderException($placeholderStr . ' is malformed');
        }
        return new FieldPlaceholder($params[1]);
    }

    /**
     * Creates a condition placeholder from a "C:<fieldId>:<expression>:<action>"
     * marker string.
     *
     * @param string $placeholderStr
     * @return ConditionPlaceholder
     * @throws PlaceholderException
     */
    private static function createConditionPlaceholder(string $placeholderStr): ConditionPlaceholder
    {
        $params = explode(':', $placeholderStr);
        if (count($params) != 4) {
            throw new PlaceholderException($placeholderStr . ' is malformed');
        }
        return new ConditionPlaceholder($params[1], $params[3], $params[2]);
    }

    /**
     * Creates a text placeholder from a "T:<text>" marker string.
     *
     * @param string $placeholderStr
     * @return TextPlaceholder
     * @throws PlaceholderException
     */
    private static function createTextPlaceholder(string $placeholderStr): TextPlaceholder
    {
        $params = explode(':', $placeholderStr);
        if (!$params || count($params) <> 2) {
            throw new PlaceholderException($placeholderStr . ' is malformed');
        }
        return new TextPlaceholder($params[1]);
    }

    /**
     * Creates a start or end block placeholder from a "B:0" or "B:1" marker
     * string.
     *
     * @param string $placeholderStr
     * @return BlockPlaceholder
     * @throws PlaceholderException
     */
    private static function createBlockPlaceholder(string $placeholderStr): BlockPlaceholder
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
     * Creates a view placeholder from a "V:<fieldId>" marker string.
     *
     * @param string $placeholderStr
     * @return ViewPlaceholder
     * @throws PlaceholderException
     */
    private static function createViewPlaceHolder(string $placeholderStr): ViewPlaceholder
    {
        $params = explode(':', $placeholderStr);
        if (!$params || count($params) <> 2) {
            throw new PlaceholderException($placeholderStr . ' is malformed');
        }
        return new ViewPlaceholder($params[1]);
    }

    /**
     * Returns the placeholder's generated id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns a human-readable representation of the placeholder, used for
     * logging.
     *
     * @return string
     */
    abstract public function getXMLTagAsString(): string;

    /**
     * Builds the DOM node that represents this placeholder in the document.
     *
     * @param DOMDocument $domDocument
     * @return DOMNode
     */
    abstract public function getDOMNode(DOMDocument $domDocument): DOMNode;

    /**
     * Returns the length, in characters, of the original marker text this
     * placeholder was parsed from.
     *
     * @return int
     */
    abstract public function length(): int;

    /**
     * Returns the original "${...}" marker text this placeholder was parsed
     * from.
     *
     * @return string
     */
    abstract public function getNodeAsString(): string;
}
