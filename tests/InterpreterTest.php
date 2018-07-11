<?php
namespace ImmediateSolutions\Shipple\Tests;

use ImmediateSolutions\Shipple\Interpreter;
use ImmediateSolutions\Shipple\Tests\Mock\Interpreter\Providers\DummyProvider;
use ImmediateSolutions\Shipple\Tests\Mock\Interpreter\Providers\ProductProvider;
use ImmediateSolutions\Shipple\Tests\Mock\Interpreter\Providers\SumProvider;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class InterpreterTest extends TestCase
{
    public function testInterpret()
    {
        $interpreter = new Interpreter([
            'sum' => new SumProvider(),
            'product' => new ProductProvider(),
            'dummy' => new DummyProvider()
        ], []);

        $result = $interpreter->interpret(2);

        Assert::assertEquals(2, $result);
        Assert::assertTrue(is_int($result));

        $result = $interpreter->interpret('{{ sum: 10, 20 }}');

        Assert::assertEquals(30, $result);
        Assert::assertTrue(is_int($result));

        $result = $interpreter->interpret('{{ sum: aaa, 20 }}');

        Assert::assertEquals('{{ sum: aaa, 20 }}', $result);

        $result = $interpreter->interpret('{{ sum: 20 }}');

        Assert::assertEquals('{{ sum: 20 }}', $result);

        $result = $interpreter->interpret('some other stuff  {{ sum: 10, 20 }} yes \{\{ "wow" ');

        Assert::assertEquals('some other stuff  30 yes {{ "wow" ', $result);

        $result = $interpreter->interpret('some other stuff
          {{ sum: 10, 20 }} yes \{\{ "wow" {{   product:10,   5 }} something 
          fake {{ fake: a1=\'I am fake\' }} and end.');

        Assert::assertEquals('some other stuff
          30 yes {{ "wow" 50 something 
          fake {{ fake: a1=\'I am fake\' }} and end.', $result);


        $result = $interpreter->interpret('some other stuff  {{ sum: 10, 20 }} yes \{\{ "wow" {{ sum: 2, 10 }} and {{ sum: 10, 20 }}\}');

        Assert::assertEquals('some other stuff  30 yes {{ "wow" 12 and 30}', $result);


        $result = $interpreter->interpret('test mixed {{product:b=10,a=22}}  ');

        Assert::assertEquals('test mixed 220  ', $result);

        $result = $interpreter->interpret('test mixed {{product:10,a=22}}  ');

        Assert::assertEquals('test mixed {{product:10,a=22}}  ', $result);

        $result = $interpreter->interpret('test mixed {{product:10,b=22}}  ');

        Assert::assertEquals('test mixed 220  ', $result);

        $result = $interpreter->interpret('test mixed {{product:a=10,22}}  ');

        Assert::assertEquals('test mixed {{product:a=10,22}}  ', $result);

        $result = $interpreter->interpret('{{dummy:null  }}');

        Assert::assertNull($result);

        $result = $interpreter->interpret('{{dummy:true  }}');

        Assert::assertTrue($result);

        $result = $interpreter->interpret('{{dummy:false}}');

        Assert::assertFalse($result);

        $result = $interpreter->interpret('{{dummy:\'false\'}}');

        Assert::assertTrue(is_string($result));

        $result = $interpreter->interpret('{{dummy:89}}');

        Assert::assertTrue(is_int($result));

        $result = $interpreter->interpret('{{dummy:0.2}}');

        Assert::assertTrue(is_float($result));

        $result = $interpreter->interpret('this is \'{{dummy:\'foo\'}}\'');

        Assert::assertEquals('this is \'foo\'', $result);

        $result = $interpreter->interpret('this is {{ \'{{dummy:\'foo\'}}\'');

        Assert::assertEquals('this is {{ \'{{dummy:\'foo\'}}\'', $result);

        $result = $interpreter->interpret('this is \{\{ \'{{dummy:\'foo\'}}\'');

        Assert::assertEquals('this is {{ \'foo\'', $result);

        $result = $interpreter->interpret('this is \{\{ \'{{dummy:\'}}\'}}\'');

        Assert::assertEquals('this is {{ \'{{dummy:\'}}\'}}\'', $result);

        $result = $interpreter->interpret('this is \{\{ \'{{dummy:\'\}\}\'}}\'');

        Assert::assertEquals('this is {{ \'}}\'', $result);

        $result = $interpreter->interpret('this is \{\{ \'{{dummy:\'\\\'\{\{\'}}\'');

        Assert::assertEquals('this is {{ \'\'{{\'', $result);
    }
}