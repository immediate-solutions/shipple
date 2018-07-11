<?php
namespace ImmediateSolutions\Shipple\Code\Provider;

use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\Context;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class FakerProvider implements ProviderInterface
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
        $accessor = $arguments->getOrdered()[0] ?? null;

        if ($accessor === null || !is_string($accessor) || trim($accessor) === '') {
            throw new \InvalidArgumentException();
        }

        $many = $arguments->getNamed()['many'] ?? null;

        if (!is_int($many) && $many !== null) {
            throw new \InvalidArgumentException();
        }

        $unique = $arguments->getNamed()['unique'] ?? false;
        $uniqueMaxRetries = $arguments->getNamed()['unique_max_retries'] ?? 1000;

        $optional = $arguments->getNamed()['optional'] ?? false;
        $optionalWeight = $arguments->getNamed()['optional_weight'] ?? 0.5;
        $optionalDefault = $arguments->getNamed()['optional_default'] ?? null;

        $parameters = $arguments->getOrdered();

        unset($parameters[0]);

        $parameters = array_values($parameters);

        if ($many !== null) {

            $faker = $this->faker;

            if ($unique) {
                $faker = $faker->unique(true, $uniqueMaxRetries);
            }

            if ($optional) {
                $faker->optional($optionalWeight, $optionalDefault);
            }

            $result = [];

            for ($i = 0; $i < $many; $i ++ ){
                $result[] = call_user_func_array([$faker, $accessor], $parameters);
            }

            return $result;
        }

        return call_user_func_array([$this->faker, $accessor], $parameters);
    }
}