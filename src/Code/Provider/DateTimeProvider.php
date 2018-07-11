<?php
namespace ImmediateSolutions\Shipple\Code\Provider;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\Context;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class DateTimeProvider implements ProviderInterface
{
    /**
     * @param Arguments $arguments
     * @param Context $context
     * @return mixed
     */
    public function provide(Arguments $arguments, Context $context)
    {
        $qualifier = $arguments->getOrdered()[0] ?? ($arguments->getNamed()['qualifier']);
        $format = $arguments->getOrdered()[1] ?? ($arguments->getNamed()['format']);

        if ($format === null || $qualifier === null) {
            throw new \InvalidArgumentException();
        }

        return (new \DateTime($qualifier))->format($format);
    }
}