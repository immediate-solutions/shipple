<?php
namespace ImmediateSolutions\Shipple\Comparator;

use ImmediateSolutions\Shipple\Preference;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class MergedOptions
{
    const BODY_SCOPE_PARTIAL = 'partial';
    const BODY_SCOPE_STRICT = 'strict';
    const BODY_SCOPE_SOFT = 'soft';
    const BODY_SCOPE_OPTIONAL = 'optional';

    /**
     * @var Preference
     */
    private $preference;

    private $options = [];

    public function __construct(array $options, Preference $preference)
    {
        $this->options = $options;
        $this->preference = $preference;
    }

    public function getBodyType(): string
    {
        return $this->options['body']['type'] ?? $this->preference->getMatchBodyType();
    }

    public function getBodyScope(): string
    {
        return $this->options['body']['scope'] ?? self::BODY_SCOPE_STRICT;
    }
}