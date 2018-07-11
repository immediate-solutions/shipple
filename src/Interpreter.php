<?php
namespace ImmediateSolutions\Shipple;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\Matcher\MatcherInterface;
use ImmediateSolutions\Shipple\Code\Provider\ProviderInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class Interpreter
{
    /**
     * @var ProviderInterface[]
     */
    private $providers;

    /**
     * @var MatcherInterface[]
     */
    private $matchers;

    public function __construct(array $providers, array $matchers)
    {
        $this->providers = $providers;
        $this->matchers = $matchers;
    }

    /**
     * @param string $pattern
     * @param mixed $source
     * @return bool
     */
    public function match(string $pattern, $source): bool
    {

    }

    /**
     * @param mixed $source
     * @return mixed
     */
    public function interpret($source)
    {
        if (!is_string($source)){
            return $source;
        }

        $onlyCode = false;

        if (mb_substr($source, 0, 2) === '{{' && mb_substr($source, -2) === '}}') {
            $onlyCode = true;
        }

        $codes = $this->extractCodes($source);

        $segments = $this->breakByCodes($source, $codes);

        $escapedSource = '';

        foreach ($segments as $segment) {
            $segment[0] = str_replace('\{', '{', $segment[0]);
            $segment[0] = str_replace('\}', '}', $segment[0]);

            $escapedSource .= $segment[0] . $segment[1];
        }

        foreach ($codes as $code) {

            if ($parsedCode = $this->parseCode($code)) {

                if (!isset($this->providers[$parsedCode['name']])) {
                    continue ;
                }

                $provider = $this->providers[$parsedCode['name']];

                $value = null;

                try {
                    $value = $provider->provide(new Arguments(
                        $parsedCode['arguments']['ordered'],
                        $parsedCode['arguments']['named']
                    ));
                } catch (\InvalidArgumentException $ex) {
                    continue ;
                }

                if ($onlyCode) {
                    return $value;
                }

                $escapedSource = str_replace($code, $value, $escapedSource);
            }
        }

        return $escapedSource;
    }

    private function breakByCodes(string $source, array $codes): array
    {
        $result = [[$source, '']];

        foreach ($codes as $code) {
            $result = $this->breakByCode($result, $code);
        }


        return $result;
    }

    private function breakByCode(array $source, string $code): array
    {
        $result = [];

        foreach ($source as $segment) {
            $parts = explode($code, $segment[0]);

            $parts = array_map(function(string $value) use ($code) {
                return [$value, $code];
            }, $parts);

            $parts[count($parts) - 1][1] = $segment[1];

            if (count($parts) > 1) {
                $result = array_merge($result, $this->breakByCode($parts, $code));
            } else {
                $result[] = $parts[0];
            }
        }


        return $result;
    }

    private function parseCode(string $code): ?array
    {
        $result = [];

        preg_match('/^{{ *([a-zA-Z_][a-zA-Z0-9_]*):/', $code, $result);

        if (count($result) !== 2) {
            return null;
        }

        $parsedCode = ['name' => $result[1], 'arguments' => []];

        $code = trim(mb_substr($code, mb_strlen($result[0]), -2));

        $patterns = [
            '/^([a-zA-Z_][a-zA-Z0-9_]*=)?(\'(?:\\\\.|[^\'])*\')(?: *,|,|$)/', // text
            '/^([a-zA-Z_][a-zA-Z0-9_]*=)?((?:0|[1-9][0-9]*)(?:\.[0-9]+)?)(?: *,|,|$)/', // numbers
            '/^([a-zA-Z_][a-zA-Z0-9_]*=)?(true|false|null)(?: *,|,|$)/', // true, false, null
        ];


        while ($code !== '') {

            $result = $this->matchArgument($code, $patterns);

            if (!$result || count($result) !== 3) {
                return null;
            } else {

                if ($result[1] === '') {

                    $parsedCode['arguments'][] = $this->cast($result[2]);

                } elseif ($result[1] !== '') {

                    $key = mb_substr($result[1], 0, mb_strlen($result[1]) - 1);

                    $parsedCode['arguments'][] = [$key, $this->cast($result[2])];
                } else {
                    return null;
                }

                $code = trim(mb_substr($code, mb_strlen($result[0])));
            }
        }

        $arguments = ['ordered' => [], 'named' => []];

        $startedNamed = false;

        foreach ($parsedCode['arguments'] as $item) {

            if (!is_array($item) && $startedNamed) {
                return null;
            }

            if (is_array($item)) {
                $startedNamed = true;
                $arguments['named'][$item[0]] = $item[1];
            } else {
                $arguments['ordered'][] = $item;
            }
        }

        $parsedCode['arguments'] = $arguments;

        return $parsedCode;
    }

    private function matchArgument(string $code, array $patterns): array
    {
        foreach ($patterns as $pattern) {

            $result = [];

            if (preg_match($pattern, $code, $result)) {
                return $result;
            }
        }

        return [];
    }

    /**
     * @param string $value
     * @return mixed
     */
    private function cast(string $value)
    {
        if (is_numeric($value)) {

            return strpos($value, '.') > -1 ? (float) $value : (int) $value;
        }

        if (in_array($value, ['true', 'false'], true)) {
            return $value === 'true';
        }

        if ($value === 'null') {
            return null;
        }

        return str_replace('\\\'', '\'', str_replace('\}', '}', str_replace('\{', '{', trim($value, '\''))));
    }

    private function extractCodes(string $text): array
    {
        $result  = [];

        preg_match_all('/{{(?:\\\\.|[^}])*}}/', $text, $result);

        return array_unique($result[0]) ?? [];
    }
}