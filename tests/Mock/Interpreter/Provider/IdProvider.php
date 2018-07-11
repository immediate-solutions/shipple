<?php
namespace ImmediateSolutions\Shipple\Tests\Mock\Interpreter\Provider;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\Context;
use ImmediateSolutions\Shipple\Code\Provider\ProviderInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class IdProvider implements ProviderInterface
{
    /**
     * @param Arguments $arguments
     * @param Context $context
     * @return mixed
     */
    public function provide(Arguments $arguments, Context $context)
    {
        return 'unique_text';
    }
}