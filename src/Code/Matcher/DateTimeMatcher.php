<?php
namespace ImmediateSolutions\Shipple\Code\Matcher;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\InvalidCodeException;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class DateTimeMatcher implements MatcherInterface
{
    /**
     * @param mixed $value
     * @param Arguments $arguments
     * @return bool
     */
    public function match($value, Arguments $arguments): bool
    {
        $format = $arguments->getOrdered()[0] ?? ($arguments->getNamed()['format'] ?? null);

        if ($format == null) {
            throw new InvalidCodeException('Format is required');
        }

        return \DateTime::createFromFormat($format, $value) !== false;
    }
}