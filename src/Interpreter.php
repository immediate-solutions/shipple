<?php
namespace ImmediateSolutions\Shipple;

use ImmediateSolutions\Shipple\Matchers\MatcherInterface;
use ImmediateSolutions\Shipple\Providers\ProviderInterface;

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
     * @param string $pattern
     * @param $source
     * @return mixed
     */
    public function interpret(string $pattern, $source)
    {
        $codes = $this->extractCodes($pattern);

        foreach ($codes as $code) {

            if ($provider = $this->parseCode($code)) {

            }

        }
    }

    private function parseCode(string $code): ?array
    {
        $result = [];

        preg_match('/^{{ *([a-zA-Z_][a-zA-Z0-9_]*):/', $code, $result);

        $parsedCode = ['name' => $result[1], 'arguments' => []];

        $code = trim(substr($code, strlen($result[0]), strlen($code) - 2));

        $patterns = [
            '/^([a-zA-Z_][a-zA-Z0-9_]*=)?(\'(?:\\\\.|[^\'])*\')(?: *,|,|$)/', // text
            '/^([a-zA-Z_][a-zA-Z0-9_]*=)?((?:0|[1-9][0-9]*)(\.[0-9]+)?)(?: *,|,|$)/', // numbers
            '/^([a-zA-Z_][a-zA-Z0-9_]*=)?(true|false|null)(?: *,|,|$)/' // true, false, null
        ];


        while ($code !== '') {

            $result = (function(string $code, array $patterns){
                foreach ($patterns as $pattern) {

                    $result = [];

                    if (preg_match($pattern, $code, $result)) {
                        return $result;
                    }
                }

                return [];
            })($code, $patterns);

            if (!$result) {
                return null;
            } else {
                if (count($result) == 2) {
                    $parsedCode['arguments'] = $this->cast($result[1]);
                } elseif (count($result) == 3) {
                    $parsedCode['arguments'] = [$result[1], $this->cast($result[2])];
                }

                $code = trim(substr($code, strlen($result[0])));
            }
        }

        return $parsedCode;
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

        return trim($value, '\'');
    }

    private function extractCodes(string $text): array
    {
        $result  = [];

        preg_match_all('/{{(?:\\\\.|[^}])*}}/', $text, $result);

        return $result[0] ?? [];
    }
}