<?php
namespace ImmediateSolutions\Shipple\Converter;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface FormatterInterface
{
    /**
     * @param mixed $data
     * @return mixed
     */
    public function format($data);
}