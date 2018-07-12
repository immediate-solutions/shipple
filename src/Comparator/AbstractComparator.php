<?php
namespace ImmediateSolutions\Shipple\Comparator;

use ImmediateSolutions\Shipple\Code\Interpreter;
use ImmediateSolutions\Shipple\Preference;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractComparator implements ComparatorInterface
{
    protected $interpreter;

    protected $preference;

    public function __construct(Interpreter $interpreter, Preference $preference)
    {
        $this->interpreter = $interpreter;
        $this->preference = $preference;
    }

    public function getMergedOptions(array $match): MergedOptions
    {
        return new MergedOptions($match['options'] ?? [], $this->preference);
    }
}