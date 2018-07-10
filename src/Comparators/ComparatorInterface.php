<?php
namespace ImmediateSolutions\Shipple\Comparators;

use Psr\Http\Message\RequestInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface ComparatorInterface
{
    public function compare(array $match, RequestInterface $request) : bool;
}