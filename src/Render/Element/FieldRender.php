<?php

namespace ACAT\Render\Element;

use DOMException;
use ACAT\Render\Render;
use ACAT\Exception\RenderException;
use ACAT\Exception\ElementException;
use ACAT\Parser\Element\FieldElement;
use ACAT\Parser\Placeholder\WordTextPlaceholder;

/**
 *
 */
class FieldRender extends Render
{

    /**
     * @param array $elements
     * @param array $values
     * @return void
     * @throws DOMException
     * @throws ElementException
     * @throws RenderException
     */
    public function render(array $elements, array $values = []) : void
    {
        foreach ($elements as $fieldElement) {
            $this->renderFieldElement($fieldElement, $values);
        }
    }

    /**
     * @param FieldElement $fieldElement
     * @param array $values
     * @return void
     * @throws RenderException
     * @throws ElementException|DOMException
     */
    public function renderFieldElement(FieldElement $fieldElement, array $values) : void
    {
        $fieldId = $fieldElement->getFieldId();

        if ($fieldId) {
            if (array_key_exists($fieldId, $values)) {
                $wordTextNode = new WordTextPlaceholder($values[$fieldId]);
                $this->appendRenderedNode(
                    $fieldElement->getElement(),
                    $wordTextNode->getDOMNode($fieldElement->getDomDocument())
                );
            }
        } else {
            throw new RenderException($fieldElement->getElement()->nodeName . ' does not contains a field id');
        }

        $fieldElement->delete();
    }

}