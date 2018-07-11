<?php
namespace ImmediateSolutions\Shipple\Comparator;

use ImmediateSolutions\Shipple\Context;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface ComparatorInterface
{
    public function compare(array $match, Context $context) : bool;
}