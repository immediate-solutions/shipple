<?php
namespace ImmediateSolutions\Shipple\Converter;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface ConverterInterface
{
    /**
     * @param string $data
     * @return mixed
     */
    public function toNormal(string $data);

    /**
     * @param mixed $data
     * @return string
     */
    public function fromNormal($data): string;
}