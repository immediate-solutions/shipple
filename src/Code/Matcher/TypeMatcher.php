<?php
namespace ImmediateSolutions\Shipple\Code\Matcher;

use ImmediateSolutions\Shipple\Code\Arguments;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class TypeMatcher implements MatcherInterface
{
    const TYPE_NUMBER = 'number';
    const TYPE_INT = 'int';
    const TYPE_BOOL = 'bool';
    const TYPE_TEXT = 'text';

    /**
     * @param mixed $value
     * @param Arguments $arguments
     * @return bool
     */
    public function match($value, Arguments $arguments): bool
    {
        $type = $arguments->getOrdered()[0] ?? ($arguments->getNamed()['name'] ?? null);

        $availableTypes = [self::TYPE_BOOL, self::TYPE_NUMBER, self::TYPE_INT, self::TYPE_TEXT];

        if (!in_array($type, $availableTypes)) {
            throw new \InvalidArgumentException();
        }

        if ($type === self::TYPE_TEXT) {
            return is_string($value);
        }

        if ($type === self::TYPE_INT) {
            return is_int($value);
        }

        if ($type === self::TYPE_NUMBER) {
            return is_int($value) || is_float($value);
        }

        if ($type === self::TYPE_BOOL) {
            return is_bool($value);
        }

        return false;
    }
}