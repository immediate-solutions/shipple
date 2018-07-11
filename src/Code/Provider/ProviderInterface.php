<?php
namespace ImmediateSolutions\Shipple\Code\Provider;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\Context;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface ProviderInterface
{
    /**
     * @param Arguments $arguments
     * @param Context $context
     * @return mixed
     */
    public function provide(Arguments $arguments, Context $context);
}