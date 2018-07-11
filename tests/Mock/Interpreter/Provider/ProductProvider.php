<?php
namespace ImmediateSolutions\Shipple\Tests\Mock\Interpreter\Provider;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\Context;
use ImmediateSolutions\Shipple\Code\Provider\ProviderInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class ProductProvider implements ProviderInterface
{
    /**
     * @param Arguments $arguments
     * @param Context $context
     * @return mixed
     */
    public function provide(Arguments $arguments, Context $context)
    {
        $a = $arguments->getOrdered()[0] ?? ($arguments->getNamed()['a'] ?? null);
        $b = $arguments->getOrdered()[1] ?? ($arguments->getNamed()['b'] ?? null);

        if ($a === null || $b === null || !(is_int($a) || is_float($a)) || !(is_int($b) || is_float($b))) {
            throw new \InvalidArgumentException();
        }

        return $a * $b;
    }
}