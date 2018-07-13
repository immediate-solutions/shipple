<?php
namespace ImmediateSolutions\Shipple\Code\Matcher;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\InvalidCodeException;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class ComparableMatcher implements MatcherInterface
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

        $target = $arguments->getOrdered()[0] ?? null;

        if (!is_int($target) && !is_float($target)){
            throw new InvalidCodeException('Target must be either an integer or a float');
        }

        $value = (float) $value;

        return $this->compare($value, $target);
    }

    abstract protected function compare(float $value, float $target): bool;
}