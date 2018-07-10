<?php
namespace ImmediateSolutions\Shipple\Comparators;

use Psr\Http\Message\RequestInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class MatchComparator implements ComparatorInterface
{
    /**
     * @var ComparatorInterface[]
     */
    private $comparators = [];

    public function __construct()
    {
        $this->comparators = [
            new UrlComparator(),
            new MethodComparator(),
            new DataComparator(),
            new PartialComparator(),
            new UrlComparator()
        ];
    }


    public function compare(array $match, RequestInterface $request): bool
    {
        foreach ($this->comparators as $comparator) {
            if (!$comparator->compare($match, $request)) {
                return false;
            }
        }

        return true;
    }
}