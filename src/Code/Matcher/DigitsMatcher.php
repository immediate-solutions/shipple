<?php
namespace ImmediateSolutions\Shipple\Code\Matcher;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\InvalidCodeException;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class DigitsMatcher implements MatcherInterface
{
    /**
     * @param mixed $value
     * @param Arguments $arguments
     * @return bool
     */
    public function match($value, Arguments $arguments): bool
    {
        if (!is_numeric($value)) {
            return false;
        }

        $quantity = $arguments->getOrdered()[0] ?? null;

        if (!is_int($quantity) && $quantity !== null) {
            throw new InvalidCodeException('Quantity must be an integer or null');
        }

        if ($quantity !== null && $quantity < 1) {
            throw new InvalidCodeException('Quantity must be greater than 0');
        }

        $quantity = $quantity === null ? '+' : '{'.$quantity.'}';

        return preg_match('/^[0-9]'.$quantity.'$/', $value) > 0;
    }
}