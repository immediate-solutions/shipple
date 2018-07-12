<?php
namespace ImmediateSolutions\Shipple;

use ImmediateSolutions\Shipple\Code\Matcher\MatcherInterface;
use ImmediateSolutions\Shipple\Code\Provider\ProviderInterface;
use ImmediateSolutions\Shipple\Response\ResponseFactoryInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class Preference
{
    const MATCH_BODY_TYPE_JSON = 'json';
    const MATCH_BODY_TYPE_FORM = 'form';
    const MATCH_BODY_TYPE_TEXT = 'text';
    const MATCH_BODY_TYPE_XML = 'xml';

    const RESPONSE_BODY_TYPE_JSON = 'json';
    const RESPONSE_BODY_TYPE_TEXT = 'text';
    const RESPONSE_BODY_TYPE_XML = 'xml';

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
     * @var string
     */
    private $matchBodyType;

    /**
     * @var string
     */
    private $responseBodyType;

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

    public function setMatchBodyType(string $type): void
    {
        $this->matchBodyType = $type;
    }

    public function getMatchBodyType(): string
    {
        return $this->matchBodyType;
    }

    public function setResponseBodyType(string $type): void
    {
        $this->responseBodyType = $type;
    }

    public function getResponseBodyType(): string
    {
        return $this->matchBodyType;
    }
}