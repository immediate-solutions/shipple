<?php
namespace ImmediateSolutions\Shipple;

use ImmediateSolutions\Shipple\Matchers\ChoiceMatcher;
use ImmediateSolutions\Shipple\Matchers\PatternMatcher;
use ImmediateSolutions\Shipple\Matchers\TypeMatcher;
use ImmediateSolutions\Shipple\Providers\DateProvider;
use ImmediateSolutions\Shipple\Providers\DateTimeProvider;
use ImmediateSolutions\Shipple\Providers\RandomNumberProvider;
use ImmediateSolutions\Shipple\Providers\RandomTextProvider;
use ImmediateSolutions\Shipple\Providers\UuidProvider;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class Application
{
    private $providers = [];

    private $matchers = [];

    public function __construct()
    {
        $this->providers['date'] = new DateProvider();
        $this->providers['datetime'] = new DateTimeProvider();
        $this->providers['uuid'] = new UuidProvider();
        $this->providers['rand_text'] = new RandomTextProvider();
        $this->providers['rand_number'] = new RandomNumberProvider();


        $this->matchers['type'] = new TypeMatcher();
        $this->matchers['choice'] = new ChoiceMatcher();
        $this->matchers['pattern'] = new PatternMatcher();
    }

    public function setProviders(array $providers): void
    {
        $this->providers = $providers;
    }

    public function setMatchers(array $matchers): void
    {
        $this->matchers = $matchers;
    }

    public function getProviders(): array
    {
        return $this->providers;
    }

    public function getMatchers(): array
    {
        return $this->matchers;
    }

    public function addProvider(string $name, ProviderInterface $provider): void
    {
        $this->providers[$name] = $provider;
    }

    public function addMatcher(string $name, MatcherInterface $matcher): void
    {
        $this->matchers[$name] = $matcher;
    }

    public function run(): void
    {
        $request = ServerRequestFactory::fromGlobals();

        $response = new JsonResponse([]);

        (new SapiEmitter())->emit($response);
    }
}