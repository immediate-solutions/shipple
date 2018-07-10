<?php
namespace ImmediateSolutions\Shipple\Comparators;

use ImmediateSolutions\Shipple\Comparators\ComparatorInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class PartialComparator implements ComparatorInterface
{
    public function compare(array $match, RequestInterface $request): bool
    {
        // TODO: Implement compare() method.
    }
}