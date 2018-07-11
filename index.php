<?php
/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */

require_once  __DIR__ . '/vendor/autoload.php';


$path = "/users/documents/{{ type: '\{\{ \' 99 \}\}', 102, 10.0, true, '\{\{ \' 99 \}\}', null, false }}/active/{{ type: 'string'}}";


$interpreter = new \ImmediateSolutions\Shipple\Code\Interpreter([], []);

$interpreter->interpret($path);

