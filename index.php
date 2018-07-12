<?php
/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */

require_once  __DIR__ . '/vendor/autoload.php';


$template = "{{ text: 10, many=100 }}";


$interpreter = new \ImmediateSolutions\Shipple\Code\Interpreter([
    'text' => new \ImmediateSolutions\Shipple\Code\Provider\TextProvider()
], []);


$result = $interpreter->interpret($template);

print_r($result);

