<?php
namespace ImmediateSolutions\Shipple\Code\Matcher;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\InvalidCodeException;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class LengthMatcher implements MatcherInterface
{
    /**
     * @param mixed $value
     * @param Arguments $arguments
     * @return bool
     */
    public function match($value, Arguments $arguments): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $length = $arguments->getOrdered()[0] ?? null;

        if (!is_int($length) && $length < 1) {
            throw new InvalidCodeException('Length must be an integer and greater than 0');
        }

        return mb_strlen($value) === $length;
    }
}