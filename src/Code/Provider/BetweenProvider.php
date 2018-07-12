<?php
namespace ImmediateSolutions\Shipple\Code\Provider;

use Faker\Generator;
use ImmediateSolutions\Shipple\Code\Arguments;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class BetweenProvider extends FakerProvider
{
    private const MAX = 2147483647;

    protected function normalize(Arguments $arguments): array
    {
        $min = $arguments->getOrdered()[0] ?? ($arguments->getNamed()['min'] ?? 0);

        if (!is_int($min) || $min < 0) {
            throw new \InvalidArgumentException();
        }

        $max = $arguments->getOrdered()[1] ?? ($arguments->getNamed()['max'] ?? self::MAX);

        if (!is_int($max) || $max > self::MAX) {
            throw new \InvalidArgumentException();
        }

        return [$min, $max];
    }

    protected function canText(): bool
    {
        return true;
    }

    protected function generate(Generator $generator, array $arguments)
    {
        return call_user_func_array([$generator, 'numberBetween'], $arguments);
    }
}