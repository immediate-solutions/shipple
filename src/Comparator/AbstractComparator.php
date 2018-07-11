<?php
namespace ImmediateSolutions\Shipple\Comparator;

use ImmediateSolutions\Shipple\Code\Interpreter;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractComparator implements ComparatorInterface
{
    protected $interpreter;

    public function __construct(Interpreter $interpreter)
    {
        $this->interpreter = $interpreter;
    }
}