<?php
namespace ImmediateSolutions\Shipple\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface ResponseFactoryInterface
{
    public function create(): ResponseInterface;
}