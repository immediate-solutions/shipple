<?php
namespace ImmediateSolutions\Shipple\Code\Provider;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\InvalidCodeException;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class DateTimeProvider implements ProviderInterface
{
    /**
     * @param Arguments $arguments
     * @return mixed
     */
    public function provide(Arguments $arguments)
    {
        $qualifier = $arguments->getOrdered()[0] ?? ($arguments->getNamed()['qualifier']);
        $format = $arguments->getOrdered()[1] ?? ($arguments->getNamed()['format']);

        if ($format === null || $qualifier === null) {
            throw new InvalidCodeException('Format and qualifier are required');
        }

        try {
            return (new \DateTime($qualifier))->format($format);
        } catch (\Exception $ex) {
            throw new InvalidCodeException('Format is invalid', $ex);
        }
    }
}