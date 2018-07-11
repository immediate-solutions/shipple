<?php
namespace ImmediateSolutions\Shipple\Code;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class Context
{
    private $template;

    public function __construct(string $template)
    {
        $this->template = $template;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function onlyCode(): bool
    {
        return mb_substr($this->template, 0, 2) === '{{' && mb_substr($this->template, -2) === '}}';
    }
}