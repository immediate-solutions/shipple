<?php
namespace ImmediateSolutions\Shipple\Comparators;

use ImmediateSolutions\Shipple\Context;
use ImmediateSolutions\Shipple\Interpreter;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class MatchComparator implements ComparatorInterface
{
    /**
     * @var ComparatorInterface[]
     */
    private $comparators = [];

    public function __construct(Interpreter $interpreter)
    {
        $this->comparators = [
            new PathComparator($interpreter),
            new MethodComparator($interpreter),
            new DataComparator($interpreter),
            new PartialComparator($interpreter),
            new PathComparator($interpreter)
        ];
    }

    public function compare(array $match, Context $context): bool
    {
        foreach ($this->comparators as $comparator) {
            if (!$comparator->compare($match, $context)) {
                return false;
            }
        }

        return true;
    }
}