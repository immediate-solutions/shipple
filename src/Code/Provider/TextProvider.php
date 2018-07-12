<?php
namespace ImmediateSolutions\Shipple\Code\Provider;

use Faker\Generator;
use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\InvalidCodeException;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class TextProvider extends FakerProvider
{
    protected function normalize(Arguments $arguments): array
    {
        $size = $arguments->getOrdered()[0] ?? ($arguments->getNamed()['size'] ?? 100);

        if (!is_int($size)){
            throw new InvalidCodeException('Size must be an integer');
        }

        return [$size];
    }

    protected function generate(Generator $generator, array $arguments)
    {
        return call_user_func_array([$generator, 'text'], $arguments);
    }
}