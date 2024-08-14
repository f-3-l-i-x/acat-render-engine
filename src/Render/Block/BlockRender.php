<?php

namespace ACAT\Render\Block;

use DOMException;
use ACAT\Render\Render;
use ACAT\Parser\Element\TextBlock;
use ACAT\Exception\RenderException;
use ACAT\Render\Element\TextRender;
use ACAT\Exception\ElementException;
use ACAT\Render\Element\FieldRender;
use ACAT\Parser\Element\BlockElement;
use ACAT\Parser\Element\TableRowBlock;
use ACAT\Parser\Element\ParagraphBlock;
use ACAT\Parser\Element\TableCellBlock;
use ACAT\Render\Condition\ConditionRender;
use ACAT\Render\Element\ViewElementRender;
use ACAT\Exception\ConditionParserException;

/**
 *
 */
class BlockRender extends Render
{

    /**
     * @var BlockElement
     */
    protected BlockElement $blockElement;

    /**
     * @var array
     */
    protected array $values = [];

    /**
     * @return void
     * @throws DOMException
     * @throws ElementException
     * @throws RenderException
     * @throws ConditionParserException
     */
    private function renderBlock() : void
    {
        $textRender = new TextRender();
        $fieldRender = new FieldRender();
        $conditionRender = new ConditionRender();
        $viewElementRender = new ViewElementRender();

        /* render field elements */
        if (array_key_exists('fields', $this->values)) {
            foreach ($this->values['fields'] as $blockNo => $blockValues) {
                /* get children */
                foreach ($this->blockElement->getChildren() as $childElement) {
                    /* create a clone */
                    $clonedChildElement = $childElement->getClonedChildBlockElement(
                        $this->blockElement->getContextNode()
                    );

                    /* render condition elements */
                    $conditionElements = $clonedChildElement->getConditionElements();
                    $conditionRender->render($conditionElements, $blockValues);

                    /* render text elements */
                    $textElements = $clonedChildElement->getTextElements();
                    $textRender->render($textElements, $blockValues);

                    /* render field elements */
                    $fieldElements = $clonedChildElement->getFieldElements();
                    $fieldRender->render($fieldElements, $blockValues);

                    /* render view elements */
                    $viewElements = $clonedChildElement->getViewElements();

                    if (array_key_exists('views', $this->values) && array_key_exists(
                            $blockNo,
                            $this->values['views']
                        )) {
                        $viewValues = $this->values['views'][$blockNo];
                    } else {
                        $viewValues = [];
                    }

                    $viewElementRender->render($viewElements, $viewValues);
                }
            }
        }
    }

    /**
     * @param array $elements
     * @param array $values
     * @return void
     * @throws ConditionParserException
     * @throws DOMException
     * @throws ElementException
     * @throws RenderException
     */
    public function render(array $elements, array $values = []) : void
    {
        foreach ($elements as $key => $blockElement) {
            $blockValues = $this->getValues($key, $values);
            $blockRender = $this->getBlockRender($blockElement, $blockValues);

            $blockRender->renderBlock();
            $blockRender->cleanUpBlock();
        }
    }

    /**
     * @param $blockKey
     * @param $values
     * @return array
     */
    private function getValues($blockKey, $values) : array
    {
        if (array_key_exists('blocks', $values) && array_key_exists($blockKey, $values['blocks'])) {
            $blockValues = $values['blocks'][$blockKey];
        } else {
            $blockValues = [];
        }

        if ($blockValues && array_key_exists('fields', $blockValues)) {
            foreach ($blockValues['fields'] as $rowNo => $rowValues) {
                foreach ($rowValues as $fieldKey => $fieldValue) {
                    if (!array_key_exists($fieldKey, $blockValues['fields'][$rowNo])) {
                        $blockValues[$rowNo][$fieldKey] = $fieldValue;
                    }
                }
            }
        }

        return $blockValues;
    }

    /**
     * @param BlockElement $blockElement
     * @param array $values
     * @return BlockRender
     * @throws RenderException
     */
    public function getBlockRender(BlockElement $blockElement, array $values) : BlockRender
    {
        if ($blockElement instanceof TextBlock) {
            return new TextBlockRender($blockElement, $values);
        } elseif ($blockElement instanceof ParagraphBlock) {
            return new ParagraphBlockRender($blockElement, $values);
        } elseif ($blockElement instanceof TableCellBlock) {
            return new TableCellBlockRender($blockElement, $values);
        } elseif ($blockElement instanceof TableRowBlock) {
            return new TableRowBlockRender($blockElement, $values);
        } else {
            throw new RenderException('unknown block render');
        }
    }

    /**
     *
     */
    public function cleanUpBlock() : void
    {
        foreach ($this->blockElement->getChildren() as $child) {
            foreach ($child->getTextElements() as $textElement) {
                $textElement->delete();
            }
            foreach ($child->getFieldElements() as $fieldElement) {
                $fieldElement->delete();
            }
            foreach ($child->getConditionElements() as $conditionElement) {
                $conditionElement->delete();
            }
            $child->delete();
        }
    }

}