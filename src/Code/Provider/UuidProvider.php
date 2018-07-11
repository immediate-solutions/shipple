<?php
namespace ImmediateSolutions\Shipple\Code\Provider;

use Faker\Generator;
use ImmediateSolutions\Shipple\Code\Arguments;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class UuidProvider extends FakerProvider
{
    protected function normalize(Arguments $arguments): array
    {
        return [];
    }

    protected function generate(Generator $generator, array $arguments)
    {
        return call_user_func_array([$generator, 'uuid'], $arguments);
    }
}