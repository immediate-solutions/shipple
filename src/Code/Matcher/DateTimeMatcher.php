<?php
namespace ImmediateSolutions\Shipple\Code\Matcher;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\Context;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class DateTimeMatcher implements MatcherInterface
{
    /**
     * @param mixed $value
     * @param Arguments $arguments
     * @param Context $context
     * @return bool
     */
    public function match($value, Arguments $arguments, Context $context): bool
    {
        $format = $arguments->getOrdered()[0] ?? ($arguments->getNamed()['format'] ?? null);

        if ($format == null) {
            throw new \InvalidArgumentException();
        }

        return \DateTime::createFromFormat($format, $value) !== false;
    }
}