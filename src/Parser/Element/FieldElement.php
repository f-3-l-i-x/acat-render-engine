<?php

namespace ACAT\Parser\Element;

/**
 *
 */
class FieldElement extends Element
{
    /**
     * @return string
     */
    #[\Override]
    public function getType(): string
    {
        return "FIELD";
    }
}
