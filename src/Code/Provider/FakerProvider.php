<?php
namespace ImmediateSolutions\Shipple\Code\Provider;

use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Faker\Generator;
use ImmediateSolutions\Shipple\Code\Arguments;

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
     * @return mixed
     */
    public function provide(Arguments $arguments)
    {
        $many = $arguments->getNamed()['many'] ?? null;

        if (!is_int($many) && $many !== null) {
            throw new \InvalidArgumentException();
        }

        $text = false;

        if ($this->canText()) {

            $text = $arguments->getNamed()['text'] ?? false;

            if (!is_bool($text)) {
                throw new \InvalidArgumentException();
            }
        }

        $normalizedArguments = $this->normalize($arguments);

        if ($many !== null) {

            $result = [];

            for ($i = 0; $i < $many; $i ++) {

                $value = $this->generate($this->faker, $normalizedArguments);

                if ($text) {
                    $value = (string) $value;
                }

                $result[] = $value;
            }

            return $result;
        }

        $value = $this->generate($this->faker, $normalizedArguments);

        return $text ? (string) $value : $value;
    }

    protected function canText(): bool
    {
        return false;
    }

    abstract protected function normalize(Arguments $arguments): array;

    abstract protected function generate(Generator $generator, array $arguments);
}