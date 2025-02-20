<?php

namespace ACAT\Utils;

use DOMNode;

class DOMUtils
{

    /**
     * @param DOMNode $node
     * @param string|null $parentNodeName
     * @param string $rootNode
     * @return DOMNode|null
     */
    public static function getParentNode(
        DOMNode $node,
        ?string $parentNodeName = null,
        string $rootNode = 'w:document'
    ) : ?DOMNode {
        if (!$parentNodeName) {
            return $node->parentNode;
        }

        if ($node->tagName != $rootNode) {
            if ($node->tagName === $parentNodeName) {
                return $node;
            } else {
                return self::getParentNode($node->parentNode, $parentNodeName);
            }
        } else {
            return null;
        }
    }

    /**
     * @param DOMNode $node
     * @return bool
     */
    public static function isRemoved(DOMNode $node) : bool
    {
        return !isset($node->nodeType) || $node->parentNode == null;
    }

}