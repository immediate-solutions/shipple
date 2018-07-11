<?php
namespace ImmediateSolutions\Shipple\Code\Matcher;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\Context;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface MatcherInterface
{
    /**
     * @param mixed $value
     * @param Arguments $arguments
     * @param Context $context
     * @return bool
     */
    public function match($value, Arguments $arguments, Context $context) : bool;
}