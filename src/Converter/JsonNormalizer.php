<?php
namespace ImmediateSolutions\Shipple\Converter;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class JsonNormalizer implements NormalizerInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function normalize(ServerRequestInterface $request)
    {
        return json_decode((string) $request->getBody(), true);
    }
}