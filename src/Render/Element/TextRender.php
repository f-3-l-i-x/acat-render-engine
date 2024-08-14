<?php

namespace ACAT\Render\Element;

use DOMException;
use ACAT\Render\Render;
use ACAT\Exception\RenderException;
use ACAT\Parser\Element\TextElement;
use ACAT\Parser\Placeholder\WordTextPlaceholder;

/**
 *
 */
class TextRender extends Render
{

    /**
     * @param array $elements
     * @param array $values
     * @return void
     * @throws RenderException
     */
    public function render(array $elements, array $values = []) : void
    {
        foreach ($elements as $textElement) {
            try {
                $this->renderTextElement($textElement);
            } catch (DOMException $e) {
                throw new RenderException($e);
            }
        }
    }

    /**
     * @param TextElement $textElement
     * @return void
     * @throws DOMException
     */
    public function renderTextElement(TextElement $textElement) : void
    {
        $wordTextNode = new WordTextPlaceholder($textElement->getText());

        $this->appendRenderedNode(
            $textElement->getElement(),
            $wordTextNode->getDOMNode($textElement->getDomDocument())
        );
        $this->deleteNode($textElement->getElement());
    }

}