<?php
namespace ImmediateSolutions\Shipple\Code\Matcher;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\Context;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class PatternMatcher implements MatcherInterface
{
    /**
     * @param mixed $value
     * @param Arguments $arguments
     * @param Context $context
     * @return bool
     */
    public function match($value, Arguments $arguments, Context $context): bool
    {
        $pattern = $arguments->getOrdered()[0] ?? ($arguments->getNamed()['pattern'] ?? null);

        if ($pattern === null) {
            throw new \InvalidArgumentException();
        }

        $pattern = '/' . $pattern . '/';

        return preg_match($pattern, $value) > 0;
    }
}