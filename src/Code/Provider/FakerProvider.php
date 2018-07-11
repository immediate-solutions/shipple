<?php
namespace ImmediateSolutions\Shipple\Code\Provider;

use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Faker\Generator;
use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\Context;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class FakerProvider implements ProviderInterface
{
    /**
     * @var Faker
     */
    private $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create();
    }

    /**
     * @param Arguments $arguments
     * @param Context $context
     * @return mixed
     */
    public function provide(Arguments $arguments, Context $context)
    {
        $many = $arguments->getNamed()['many'] ?? null;

        if (!is_int($many) && $many !== null) {
            throw new \InvalidArgumentException();
        }

        $unique = $arguments->getNamed()['unique'] ?? false;

        if (!is_bool($unique)) {
            throw new \InvalidArgumentException();
        }

        $uniqueMaxRetries = $arguments->getNamed()['unique_max_retries'] ?? 10000;

        if (!is_int($uniqueMaxRetries)) {
            throw new \InvalidArgumentException();
        }

        $optional = $arguments->getNamed()['optional'] ?? false;

        if (!is_bool($optional)) {
            throw new \InvalidArgumentException();
        }

        $optionalWeight = $arguments->getNamed()['optional_weight'] ?? 0.5;

        if (!is_int($optionalWeight) && !is_float($optionalWeight)) {
            throw new \InvalidArgumentException();
        }

        $optionalDefault = $arguments->getNamed()['optional_default'] ?? null;

        $normalizedArguments = $this->normalize($arguments);

        if ($many !== null) {
            $result = [];

            if ($unique) {
                $this->faker->unique(true, $uniqueMaxRetries);
            }

            if ($optional) {
                $this->faker->optional($optionalWeight, $optionalDefault);
            }

            for ($i = 0; $i < $many; $i ++) {
                $result[] = $this->generate($this->faker, $normalizedArguments);
            }

            return $result;
        }

        return $this->generate($this->faker, $normalizedArguments);
    }

    abstract protected function normalize(Arguments $arguments): array;

    abstract protected function generate(Generator $generator, array $arguments);
}