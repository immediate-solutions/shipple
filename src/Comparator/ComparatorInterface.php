<?php
namespace ImmediateSolutions\Shipple\Comparator;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface ComparatorInterface
{
    public function compare(array $match, ServerRequestInterface $request) : bool;
}