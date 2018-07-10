<?php
namespace ImmediateSolutions\Shipple\Response;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class Error404ResponseFactory implements ResponseFactoryInterface
{
    public function create(): ResponseInterface
    {
        return new JsonResponse(['message' => 'Not Found'], 404);
    }
}