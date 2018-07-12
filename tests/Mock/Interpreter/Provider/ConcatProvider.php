<?php
namespace ImmediateSolutions\Shipple\Tests\Mock\Interpreter\Provider;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\Provider\ProviderInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class ConcatProvider implements ProviderInterface
{
    /**
     * @param Arguments $arguments
     * @return mixed
     */
    public function provide(Arguments $arguments)
    {
        $delimiter = $arguments->getNamed()['delimiter'] ?? '';

        return implode($delimiter, $arguments->getOrdered());
    }
}