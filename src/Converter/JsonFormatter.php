<?php
namespace ImmediateSolutions\Shipple\Converter;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class JsonFormatter implements FormatterInterface
{
    /**
     * @param mixed $data
     * @return mixed
     */
    public function format($data)
    {
        return json_encode($data);
    }
}