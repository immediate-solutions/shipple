<?php
/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */

$application = new \ImmediateSolutions\Shipple\Application(new \ImmediateSolutions\Shipple\Loaders\ArrayLoader([]));

$application->run();