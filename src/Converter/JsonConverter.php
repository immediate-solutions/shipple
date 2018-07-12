<?php
namespace ImmediateSolutions\Shipple\Converter;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class JsonConverter implements ConverterInterface
{
    /**
     * @param string $data
     * @return mixed
     */
    public function toNormal(string $data)
    {
        return json_decode($data, true);
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function fromNormal($data): string
    {
        return json_encode($data);
    }
}