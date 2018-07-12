<?php
namespace ImmediateSolutions\Shipple\Comparator;

use Psr\Http\Message\RequestInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class MethodComparator extends AbstractComparator
{
    public function compare(array $match, RequestInterface $request): bool
    {
        $template = $match['method'] ?? null;

        if (!$template) {
            return true;
        }

        $method = strtoupper($request->getMethod());

        return $this->interpreter->match($template, $method);
    }
}