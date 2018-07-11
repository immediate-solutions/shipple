<?php
namespace ImmediateSolutions\Shipple\Code\Provider;

use ImmediateSolutions\Shipple\Code\Arguments;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface ProviderInterface
{
    /**
     * @param Arguments $arguments
     * @return mixed
     */
    public function provide(Arguments $arguments);
}