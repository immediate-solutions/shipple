<?php
namespace ImmediateSolutions\Shipple\Loaders;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface LoaderInterface
{
    public function load(): array;
}