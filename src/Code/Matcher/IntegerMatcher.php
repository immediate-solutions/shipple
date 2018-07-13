<?php
namespace ImmediateSolutions\Shipple\Code\Matcher;

use ImmediateSolutions\Shipple\Code\Arguments;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class IntegerMatcher implements MatcherInterface
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

        return preg_match('/^(?:-)?(?:0|[1-9][0-9]*)$/', $value) > 0;
    }
}