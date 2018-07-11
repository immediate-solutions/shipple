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

        $normalizedArguments = $this->normalize($arguments);

        if ($many !== null) {

            $result = [];

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