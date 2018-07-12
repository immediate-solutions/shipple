<?php
namespace ImmediateSolutions\Shipple;

use ImmediateSolutions\Shipple\Code\Interpreter;
use ImmediateSolutions\Shipple\Comparator\MatchComparator;
use ImmediateSolutions\Shipple\Loader\LoaderInterface;
use ImmediateSolutions\Shipple\Code\Matcher\ChoiceMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\PatternMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\TypeMatcher;
use ImmediateSolutions\Shipple\Code\Provider\DateTimeProvider;
use ImmediateSolutions\Shipple\Code\Provider\BetweenProvider;
use ImmediateSolutions\Shipple\Code\Provider\TextProvider;
use ImmediateSolutions\Shipple\Code\Provider\UuidProvider;
use ImmediateSolutions\Shipple\Response\Error404ResponseFactory;
use ImmediateSolutions\Shipple\Response\Error500ResponseFactory;
use ImmediateSolutions\Shipple\Response\StubResponseFactory;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class Application
{
    const MATCH_CONTENT_TYPE_JSON = 'json';
    const MATCH_CONTENT_TYPE_FORM = 'form';
    const MATCH_CONTENT_TYPE_TEXT = 'text';
    const MATCH_CONTENT_TYPE_XML = 'xml';

    const RESPONSE_CONTENT_TYPE_JSON = 'json';
    const RESPONSE_CONTENT_TYPE_TEXT = 'text';
    const RESPONSE_CONTENT_TYPE_XML = 'xml';

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var Preference
     */
    private $preference;

    public function __construct(LoaderInterface $loader, Preference $preference = null)
    {
        $this->loader = $loader;

        if ($preference === null) {

            $preference = new Preference();

            $preference->addProvider('datetime', new DateTimeProvider());
            $preference->addProvider('uuid', new UuidProvider());
            $preference->addProvider('text', new TextProvider());
            $preference->addProvider('between', new BetweenProvider());

            $preference->addMatcher('type', new TypeMatcher());
            $preference->addMatcher('choice', new ChoiceMatcher());
            $preference->addMatcher('pattern', new PatternMatcher());

            $preference->setMismatchResponseFactory(new Error404ResponseFactory());
            $preference->setErrorResponseFactory(new Error500ResponseFactory());

            $preference->setMatchBodyType(Preference::MATCH_BODY_TYPE_JSON);
            $preference->setResponseBodyType(Preference::RESPONSE_BODY_TYPE_JSON);
        }

        $this->preference = $preference;
    }

    public function setPreference(Preference $preference): void
    {
        $this->preference = $preference;
    }

    public function getPreference(): Preference
    {
        return $this->preference;
    }


    public function run(ServerRequestInterface $request = null): void
    {
        $request = $request ?: ServerRequestFactory::fromGlobals();

        $stub = $this->match($request);

        if (!$stub) {
            $response = $this->preference->getMismatchResponseFactory()->create();
        } else {
            $response = (new StubResponseFactory($stub))->create();
        }

        (new SapiEmitter())->emit($response);
    }

    private function match(ServerRequestInterface $request): array
    {
        $comparator = new MatchComparator(new Interpreter(
            $this->preference->getProviders(),
            $this->preference->getMatchers()
        ), $this->getPreference());

        foreach ($this->loader->load() as $stub) {
            if ($comparator->compare($stub['match'], $request)) {
                return $stub;
            }
        }

        return null;
    }
}