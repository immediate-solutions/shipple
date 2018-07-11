<?php
namespace ImmediateSolutions\Shipple\Code\Provider;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\Context;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class NumberProvider implements ProviderInterface
{
    /**
     * @param Arguments $arguments
     * @param Context $context
     * @return mixed
     */
    public function provide(Arguments $arguments, Context $context)
    {
        $min = $arguments->getOrdered()[0] ?? ($arguments->getNamed()['min'] ?? PHP_INT_MIN);
        $max = $arguments->getOrdered()[1] ?? ($arguments->getNamed()['max'] ?? PHP_INT_MAX);

        $many = $arguments->getNamed()['many'] ?? null;

        if (!is_int($many) && $many !== null) {
            throw new \InvalidArgumentException();
        }

        $text = $arguments->getNamed()['text'] ?? false;

        if ($many !== null) {

            $result = [];

            for ($i = 0; $i < $many; $i ++ ){
                $result[] = $this->generate($min, $max, $text);
            }

            return $result;
        }

        return $this->generate($min, $max, $text);
    }

    private function generate(int $min, int $max, bool $text)
    {
        $number = rand($min, $max);

        return $text ? (string) $number : $number;
    }
}