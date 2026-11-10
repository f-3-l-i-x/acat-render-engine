<?php

namespace ACAT\Render\Condition\Action;

use DateTime;
use ACAT\Exception\ElementException;
use ACAT\Parser\Element\ConditionElement;
use ACAT\Exception\ConditionParserException;

/**
 *
 */
class ConditionParser
{
    /**
     * @var array|string[]
     */
    private array $lex = ['>', '<', '='];

    /**
     * @var array|string[]
     */
    private array $operators = ['<>', '=', '<', '>', '>=', '<='];

    /**
     * @param ConditionElement $conditionElement
     * @param array $values
     * @return bool
     * @throws ConditionParserException
     * @throws ElementException
     */
    #[\NoDiscard]
    public function evaluateCondition(ConditionElement $conditionElement, array $values): bool
    {
        $fieldValue = null;
        $termValues = $this->getCompareValue($conditionElement->getExpression());

        if (empty($termValues)) {
            throw new ConditionParserException('unknown condition ' . $conditionElement->getExpression());
        }

        if (array_key_exists($conditionElement->getFieldId(), $values)) {
            $fieldValue = $values[$conditionElement->getFieldId()];
        }

        $operator = $termValues['operator'];
        $compareValue = $termValues['value'];

        if ($compareValue !== null && str_starts_with($compareValue, "FIELD_COMPARE_")) {
            $compareFieldId = str_replace('FIELD_COMPARE_', '', $compareValue);
            if ($compareFieldId && array_key_exists($compareFieldId, $values)) {
                $compareValue = $values[$compareFieldId];
            } else {
                $compareValue = null;
            }
        }

        $fieldValue = $this->castData($fieldValue);
        $compareValue = $this->castData($compareValue);

        return match ($operator) {
            '<>' => $compareValue === null ? !empty($fieldValue) : ($compareValue <> $fieldValue),
            '=' => $compareValue === null ? empty($fieldValue) : ($compareValue == $fieldValue),
            '<' => $fieldValue < ($compareValue ?? 0),
            '>' => $fieldValue > ($compareValue ?? 0),
            '>=' => $fieldValue >= ($compareValue ?? 0),
            '<=' => $fieldValue <= ($compareValue ?? 0),
            default => throw new ConditionParserException('unknown operator ' . $conditionElement->getExpression()),
        };
    }

    /**
     * @param string $condition
     * @return array
     */
    public function getCompareValue(string $condition): array
    {
        $term = [];
        $operator = $this->getOperator($condition);

        if ($operator !== null && in_array($operator, $this->operators, true)) {
            $term['operator'] = $operator;
            // only the leading operator is cut off, the value itself may contain operator characters
            $value = trim(substr($condition, strlen($operator)));
            if ($value === '') {
                $term['value'] = null;
            } else {
                $term['value'] = $value;
            }
        }

        return $term;
    }

    /**
     * @param string $value
     * @return string|null
     */
    private function getOperator(string $value): ?string
    {
        $operator = null;

        for ($i = 0; $i < strlen($value); $i++) {
            $frac = substr($value, $i, 1);
            if (in_array($frac, $this->lex)) {
                $operator .= $frac;
            } else {
                break;
            }
        }

        return $operator;
    }

    /**
     * @param string|null $value
     * @return string|null
     */
    private function castData(?string $value): ?string
    {
        if (!$value || !str_contains($value, '.')) {
            return $value;
        }

        $parts = explode('.', $value);

        // createFromFormat() silently normalizes out-of-range values (e.g. 31.02.2024
        // becomes 02.03.2024) instead of rejecting them, so the calendar validity is
        // checked explicitly before trusting the parsed result.
        if (count($parts) !== 3 || !checkdate((int) $parts[1], (int) $parts[0], (int) $parts[2])) {
            return $value;
        }

        $dateObj = DateTime::createFromFormat('d.m.Y', $value);

        if ($dateObj) {
            return $dateObj->format('Y-m-d');
        }

        return $value;
    }
}
