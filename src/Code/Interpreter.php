<?php
namespace ImmediateSolutions\Shipple\Code;

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
     * @param string $template
     * @param mixed $source
     * @return bool
     */
    public function match($template, $source): bool
    {
        if (!is_string($template)) {
            return $template === $source;
        }

        if ($this->onlyCode($template)) {
            return $this->matchCode($template, $source);
        }

        if (!is_string($source)) {
            return false;
        }

        $codes = $this->extractCodes($template);

        $segments = $this->breakByCodes($template, array_unique($codes));

        $pattern = '';

        $placeholder = '';

        foreach ($segments as $segment) {

            $segment = $this->escapeSegment($segment);

            $pattern .= $placeholder.preg_quote($segment[0], '/');

            $placeholder = '(.*)';
        }

        $pattern = '/^' . $pattern . '$/';

        $result = [];

        if (!preg_match($pattern, $source, $result)) {
            return false;
        }

        unset($result[0]);

        $result = array_values($result);

        foreach ($codes as $index => $code) {
            if (!$this->matchCode($code, $result[$index])) {
                return false;
            }
        }

        return true;
    }

    private function matchCode(string $code, $value): bool
    {
        $parsedCode = $this->parseCode($code);

        if (!$parsedCode) {
            return false;
        }

        if (!isset($this->matchers[$parsedCode['name']])) {
            return false;
        }

        $matcher = $this->matchers[$parsedCode['name']];

        try {

            $matched = $matcher->match($value, new Arguments(
                $parsedCode['arguments']['ordered'],
                $parsedCode['arguments']['named']
            ));

            if (!$matched) {
                return false;
            }

        } catch (InvalidCodeException $ex) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed $template
     * @return mixed
     */
    public function interpret($template)
    {
        if (!is_string($template)){
            return $template;
        }

        $onlyCode = $this->onlyCode($template);

        $codes = array_unique($this->extractCodes($template));

        $template = $this->escapeTemplate($template, $codes);

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
                } catch (InvalidCodeException $ex) {
                    continue ;
                }

                if ($onlyCode) {
                    return $value;
                }

                if (!is_string($value) && !is_int($value) && !is_float($value)) {
                    continue ;
                }

                $template = str_replace($code, $value, $template);
            }
        }

        return $template;
    }

    private function escapeTemplate(string $template, array $codes): string
    {
        $segments = $this->breakByCodes($template, $codes);

        $escapedSource = '';

        foreach ($segments as $segment) {

            $segment = $this->escapeSegment($segment);

            $escapedSource .= $segment[0] . $segment[1];
        }

        return $escapedSource;
    }

    private function escapeSegment(array $segment): array
    {
        $segment[0] = str_replace(['\{', '\}'], ['{','}' ], $segment[0]);

        return $segment;
    }

    private function breakByCodes(string $template, array $codes): array
    {
        $result = [[$template, '']];

        foreach ($codes as $code) {
            $result = $this->breakByCode($result, $code);
        }


        return $result;
    }

    private function breakByCode(array $template, string $code): array
    {
        $result = [];

        foreach ($template as $segment) {
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

        preg_match('/^{{ *([a-zA-Z_][a-zA-Z0-9_]*)(?::| *}})/', $code, $result);

        if (count($result) !== 2) {
            return null;
        }

        $code = trim(mb_substr($code, mb_strlen($result[0]), -2));

        return ['name' => $result[1], 'arguments' => $this->parseArguments($code)];
    }

    private function parseArguments(string $code): array
    {
        $patterns = [
            '/^([a-zA-Z_][a-zA-Z0-9_]*=)?(\'(?:\\\\.|[^\'])*\')(?: *,|,|$)/', // text
            '/^([a-zA-Z_][a-zA-Z0-9_]*=)?((?:-)?(?:0|[1-9][0-9]*)(?:\.[0-9]+)?)(?: *,|,|$)/', // numbers
            '/^([a-zA-Z_][a-zA-Z0-9_]*=)?(true|false|null)(?: *,|,|$)/', // true, false, null
        ];

        $arguments = [];

        $empty = ['ordered' => [], 'named' => []];

        while ($code !== '') {

            $result = $this->matchArgument($code, $patterns);

            if (!$result || count($result) !== 3) {
                return $empty;
            } else {

                if ($result[1] === '') {

                    $arguments[] = $this->cast($result[2]);

                } elseif ($result[1] !== '') {

                    $key = mb_substr($result[1], 0, mb_strlen($result[1]) - 1);

                    $arguments[] = [$key, $this->cast($result[2])];
                } else {
                    return $empty;
                }

                $code = trim(mb_substr($code, mb_strlen($result[0])));
            }
        }

        $normalizedArguments = $empty;

        $startedNamed = false;

        $isNamed = function($item){
            return is_array($item) && count($item) > 0;
        };

        foreach ($arguments as $item) {

            if (!$isNamed($item) && $startedNamed) {
                return $empty;
            }

            if ($isNamed($item)) {
                $startedNamed = true;
                $normalizedArguments['named'][$item[0]] = $item[1];
            } else {
                $normalizedArguments['ordered'][] = $item;
            }
        }

        return $normalizedArguments;
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

        return str_replace(['\\\'', '\}', '\{'], ['\'', '}', '{'], substr($value, 1, -1));
    }

    private function extractCodes(string $text): array
    {
        $result  = [];

        preg_match_all('/{{(?:\\\\.|[^}])*}}/', $text, $result);

        return $result[0] ?? [];
    }

    private function onlyCode(string $template): bool
    {
        return mb_substr($template, 0, 2) === '{{' && mb_substr($template, -2) === '}}'
            && mb_substr_count($template, '{{') === 1 &&  mb_substr_count($template, '}}') === 1;
    }
}