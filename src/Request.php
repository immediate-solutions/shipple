<?php
namespace ImmediateSolutions\Shipple;

use Psr\Http\Message\RequestInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class Request
{
    /**
     * @var RequestInterface
     */
    private $request;


    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function getPath(): string
    {
        return $this->request->getUri()->getPath();
    }

    public function getMethod(): string
    {
        return strtoupper($this->request->getMethod());
    }


    public function getData(): array
    {
        if ($this->getMethod() === 'POST') {

        }

        return [];
    }
}