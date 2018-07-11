<?php
namespace ImmediateSolutions\Shipple\Code\Provider;

use ImmediateSolutions\Shipple\Code\Arguments;
use ImmediateSolutions\Shipple\Code\Context;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class TextProvider implements ProviderInterface
{
    /**
     * @var FakerProvider
     */
    private $fakerProvider;

    public function __construct()
    {
        $this->fakerProvider = new FakerProvider();
    }

    /**
     * @param Arguments $arguments
     * @param Context $context
     * @return mixed
     */
    public function provide(Arguments $arguments, Context $context)
    {
        $ordered = ['text'];
        $named = [];

        if (isset($arguments->getNamed()['many'])) {
            $named['many'] = $arguments->getNamed()['many'];
        }

        return $this->fakerProvider->provide(new Arguments($ordered, $named), $context);
    }
}