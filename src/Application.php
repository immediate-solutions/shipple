<?php
namespace ImmediateSolutions\Shipple;

use ImmediateSolutions\Shipple\Comparators\MatchComparator;
use ImmediateSolutions\Shipple\Loaders\LoaderInterface;
use ImmediateSolutions\Shipple\Matchers\ChoiceMatcher;
use ImmediateSolutions\Shipple\Matchers\MatcherInterface;
use ImmediateSolutions\Shipple\Matchers\PatternMatcher;
use ImmediateSolutions\Shipple\Matchers\TypeMatcher;
use ImmediateSolutions\Shipple\Providers\DateProvider;
use ImmediateSolutions\Shipple\Providers\DateTimeProvider;
use ImmediateSolutions\Shipple\Providers\ProviderInterface;
use ImmediateSolutions\Shipple\Providers\RandomNumberProvider;
use ImmediateSolutions\Shipple\Providers\RandomTextProvider;
use ImmediateSolutions\Shipple\Providers\UuidProvider;
use ImmediateSolutions\Shipple\Response\Error404ResponseFactory;
use ImmediateSolutions\Shipple\Response\ResponseFactoryInterface;
use ImmediateSolutions\Shipple\Response\StubResponseFactory;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class Application
{
    private $providers = [];

    private $matchers = [];

    /**
     * @var ResponseFactoryInterface
     */
    private $mismatchResponseFactory;

    /**
     * @var LoaderInterface
     */
    private $loader;

    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;

        $this->mismatchResponseFactory = new Error404ResponseFactory();

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

    public function setMismatchResponseFactory(ResponseFactoryInterface $responseFactory) {
        $this->mismatchResponseFactory = $responseFactory;
    }

    public function run(RequestInterface $request = null): void
    {
        $request = $request ?: ServerRequestFactory::fromGlobals();

        $stub = $this->match($request);

        if (!$stub) {
            $response = $this->mismatchResponseFactory->create();
        } else {
            $response = (new StubResponseFactory($stub))->create();
        }

        (new SapiEmitter())->emit($response);
    }

    private function match(RequestInterface $request): array
    {
        $comparator = new MatchComparator();

        foreach ($this->loader->load() as $stub) {
            if ($comparator->compare($stub['match'], $request)) {
                return $stub;
            }
        }

        return null;
    }
}