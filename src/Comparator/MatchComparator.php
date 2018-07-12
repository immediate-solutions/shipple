<?php
namespace ImmediateSolutions\Shipple\Comparator;

use ImmediateSolutions\Shipple\Code\Interpreter;
use ImmediateSolutions\Shipple\Preference;
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

    public function __construct(Interpreter $interpreter, Preference $preference)
    {
        $this->comparators = [
            new PathComparator($interpreter, $preference),
            new MethodComparator($interpreter, $preference),
            new BodyComparator($interpreter, $preference),
            new QueryComparator($interpreter, $preference),
            new FileComparator($interpreter, $preference)
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