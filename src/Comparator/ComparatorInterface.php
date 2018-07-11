<?php
namespace ImmediateSolutions\Shipple\Comparator;

use ImmediateSolutions\Shipple\Request;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface ComparatorInterface
{
    public function compare(array $match, Request $request) : bool;
}