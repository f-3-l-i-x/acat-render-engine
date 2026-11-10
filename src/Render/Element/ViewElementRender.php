<?php

namespace ACAT\Render\Element;

use DOMException;
use ACAT\Render\Render;
use ACAT\Parser\ParserConstants;
use ACAT\Parser\Element\ViewElement;
use ACAT\Exception\ElementException;
use ACAT\Parser\Placeholder\WordTextPlaceholder;
use ACAT\Parser\Placeholder\LineBreakPlaceholder;

/**
 *
 */
class ViewElementRender extends Render
{
    /**
     * @param array $elements
     * @param array|null $values
     * @return void
     * @throws DOMException
     * @throws ElementException
     */
    #[\Override]
    public function render(array $elements, ?array $values = []): void
    {
        if (!$values) {
            $values = [];
        }

        foreach ($elements as $element) {
            $this->renderViewElement($element, $values);
        }
    }

    /**
     * @param ViewElement $viewElement
     * @param array $values
     * @return void
     * @throws ElementException
     * @throws DOMException
     */
    private function renderViewElement(ViewElement $viewElement, array $values): void
    {
        $viewId = $viewElement->getFieldId();

        if (isset($values[$viewId])) {
            // empty parts (leading, trailing or consecutive line breaks) are skipped by PREG_SPLIT_NO_EMPTY
            $elements = preg_split(
                ParserConstants::WORD_LINE_BREAK_NODE_PATTERN,
                (string) $values[$viewId],
                -1,
                PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
            );

            foreach ($elements as $element) {
                $node = $element === ParserConstants::WORD_LINE_BREAK_NODE
                    ? $this->createLineBreakNode()
                    : $this->createWordTextPlaceholder(trim($element));

                $this->appendRenderedNode(
                    $viewElement->getElement(),
                    $node->getDOMNode($viewElement->getDomDocument())
                );
            }
        }

        $viewElement->delete();
    }

    /**
     * @return LineBreakPlaceholder
     */
    private function createLineBreakNode(): LineBreakPlaceholder
    {
        return new LineBreakPlaceholder();
    }

    /**
     * @param string $text
     * @return WordTextPlaceholder
     */
    private function createWordTextPlaceholder(string $text): WordTextPlaceholder
    {
        return new WordTextPlaceholder($text);
    }
}
