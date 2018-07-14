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

        $size = $arguments->getOrdered()[0] ?? null;

        $from = $arguments->getNamed()['from'] ?? null;
        $to = $arguments->getNamed()['to'] ?? null;

        if ($size === null && $to === null && $from === null) {
            throw new InvalidCodeException('Either size or from/to is required');
        }

        if ($size !== null && !is_int($size)) {
            throw new InvalidCodeException('Size must be an integer');
        }

        if ($size !== null && $size < 1) {
            throw new InvalidCodeException('Size must be greater than 0');
        }

        if ($from !== null && !is_int($from)) {
            throw new InvalidCodeException('From must be an integer');
        }

        if ($to !== null && !is_int($to)) {
            throw new InvalidCodeException('To must be an integer');
        }

        if (($from !== null && $from < 1) || ($to !== null && $to < 1)) {
            throw new InvalidCodeException('From/to must be greater than 0');
        }

        if ($from !== null && $to !== null && $from > $to) {
            throw new InvalidCodeException('From must be less than to');
        }

        if ($to !== null && $from !== null && $to < $from) {
            throw new InvalidCodeException('To must be greater than from');
        }

        $length = mb_strlen($value);

        if ($size !== null) {
            return $length === $size;
        }

        if ($from !== null && $to === null) {
            return $length >= $from;
        }

        if ($to !== null && $from === null) {
            return $length <= $to;
        }

        return $length >= $from && $length <= $to;
    }
}