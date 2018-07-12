<?php
namespace ImmediateSolutions\Shipple\Code;

use Exception;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class InvalidCodeException extends \RuntimeException
{
    public function __construct($message = "Unknown error", Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}