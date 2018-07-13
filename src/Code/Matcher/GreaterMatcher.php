<?php
namespace ImmediateSolutions\Shipple\Code\Matcher;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class GreaterMatcher extends ComparableMatcher
{
    protected function compare(float $value, float $target): bool
    {
        return $value > $target;
    }
}