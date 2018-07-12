<?php
namespace ImmediateSolutions\Shipple\Converter;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface NormalizerInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function normalize(ServerRequestInterface $request);
}