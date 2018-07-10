<?php
namespace ImmediateSolutions\Shipple\Loaders;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class ArrayLoader implements LoaderInterface
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function load(): array
    {
        return $this->data;
    }
}