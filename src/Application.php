<?php
namespace ImmediateSolutions\Shipple;

use ImmediateSolutions\Shipple\Code\Interpreter;
use ImmediateSolutions\Shipple\Code\Provider\FakerProvider;
use ImmediateSolutions\Shipple\Comparator\MatchComparator;
use ImmediateSolutions\Shipple\Loader\LoaderInterface;
use ImmediateSolutions\Shipple\Code\Matcher\ChoiceMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\MatcherInterface;
use ImmediateSolutions\Shipple\Code\Matcher\PatternMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\TypeMatcher;
use ImmediateSolutions\Shipple\Code\Provider\DateProvider;
use ImmediateSolutions\Shipple\Code\Provider\DateTimeProvider;
use ImmediateSolutions\Shipple\Code\Provider\ProviderInterface;
use ImmediateSolutions\Shipple\Code\Provider\NumberProvider;
use ImmediateSolutions\Shipple\Code\Provider\TextProvider;
use ImmediateSolutions\Shipple\Code\Provider\UuidProvider;
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
    /**
     * @var ResponseFactoryInterface
     */
    private $mismatchResponseFactory;

    /**
     * @var ProviderInterface[]
     */
    private $providers = [];

    /**
     * @var MatcherInterface[]
     */
    private $matchers = [];

    /**
     * @var LoaderInterface
     */
    private $loader;


    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;

        $this->addProvider('datetime', new DateTimeProvider());
        $this->addProvider('uuid', new UuidProvider());
        $this->addProvider('text', new TextProvider());
        $this->addProvider('number', new NumberProvider());

        $this->addMatcher('type', new TypeMatcher());
        $this->addMatcher('choice', new ChoiceMatcher());
        $this->addMatcher('pattern', new PatternMatcher());

        $this->setMismatchResponseFactory(new Error404ResponseFactory());
    }


    /**
     * @var ProviderInterface[] $providers
     */
    public function setProviders(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @return ProviderInterface[]
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @var MatcherInterface[] $matchers
     */
    public function setMatchers(array $matchers)
    {
        $this->matchers = $matchers;
    }

    /**
     * @return MatcherInterface[]
     */
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

    public function setMismatchResponseFactory(ResponseFactoryInterface $responseFactory)
    {
        $this->mismatchResponseFactory = $responseFactory;
    }

    public function getMismatchResponseFactory(): ResponseFactoryInterface
    {
        return $this->mismatchResponseFactory;
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
        $comparator = new MatchComparator(new Interpreter($this->getProviders(), $this->getMatchers()));

        foreach ($this->loader->load() as $stub) {
            if ($comparator->compare($stub['match'], new Request($request))) {
                return $stub;
            }
        }

        return null;
    }
}