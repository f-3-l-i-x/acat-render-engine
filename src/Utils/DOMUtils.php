<?php

namespace ACAT\Utils;

use DOMNode;

class DOMUtils
{
    /**
     * Returns the closest node (the given node included) with the given name. Without a name the direct parent
     * is returned. The search stops at the root node, at the top of the document (header, footer, ... do not
     * have a w:document root) and at the top of a detached subtree.
     *
     * @param DOMNode $node
     * @param string|null $parentNodeName
     * @param string $rootNode
     * @return DOMNode|null
     */
    public static function getParentNode(
        DOMNode $node,
        ?string $parentNodeName = null,
        string $rootNode = 'w:document'
    ): ?DOMNode {
        if (!$parentNodeName) {
            return $node->parentNode;
        }

        for ($current = $node; $current !== null; $current = $current->parentNode) {
            if ($current->nodeName === $rootNode) {
                return null;
            }
            if ($current->nodeName === $parentNodeName) {
                return $current;
            }
        }

        return null;
    }

    /**
     * @param DOMNode $node
     * @return bool
     */
    public static function isRemoved(DOMNode $node): bool
    {
        return !isset($node->nodeType) || $node->parentNode == null;
    }
}
