<?php
namespace ImmediateSolutions\Shipple\Code;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class Arguments
{
    private $ordered = [];

    private $named = [];

    public function __construct(array $ordered, array $named)
    {
        $this->setOrdered($ordered);
        $this->setNamed($named);
    }

    public function setNamed(array $named): void
    {
        $this->named = $named;
    }

    public function getNamed(): array
    {
        return $this->named;
    }

    public function setOrdered(array $ordered): void
    {
        $this->ordered = $ordered;
    }

    public function getOrdered(): array
    {
        return $this->ordered;
    }
}