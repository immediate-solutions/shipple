<?php
namespace ImmediateSolutions\Shipple\Comparator;

use Psr\Http\Message\RequestInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class PathComparator extends AbstractComparator
{
    public function compare(array $match, RequestInterface $request): bool
    {
        $template = $match['path'] ?? null;

        if (!$template) {
            return true;
        }

        $path = $request->getUri()->getPath();

        return $this->interpreter->match($template, $path);
    }
}