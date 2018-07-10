<?php
namespace ImmediateSolutions\Shipple\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class StubResponseFactory implements ResponseFactoryInterface
{
    private $stub;

    public function __construct(array $stub)
    {
        $this->stub = $stub;
    }


    public function create(): ResponseInterface
    {
        // TODO: Implement create() method.
    }
}