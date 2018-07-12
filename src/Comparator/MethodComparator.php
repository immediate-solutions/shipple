<?php
namespace ImmediateSolutions\Shipple\Comparator;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class MethodComparator extends AbstractComparator
{
    public function compare(array $match, ServerRequestInterface $request): bool
    {
        $template = $match['method'] ?? null;

        if (!$template) {
            return true;
        }

        $method = strtoupper($request->getMethod());

        return $this->interpreter->match($template, $method);
    }
}