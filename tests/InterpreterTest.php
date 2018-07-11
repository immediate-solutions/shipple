<?php
namespace ImmediateSolutions\Shipple\Tests;

use ImmediateSolutions\Shipple\Code\Matcher\ChoiceMatcher;
use ImmediateSolutions\Shipple\Code\Interpreter;
use ImmediateSolutions\Shipple\Code\Provider\NumberProvider;
use ImmediateSolutions\Shipple\Tests\Mock\Interpreter\Provider\ConcatProvider;
use ImmediateSolutions\Shipple\Tests\Mock\Interpreter\Provider\DummyProvider;
use ImmediateSolutions\Shipple\Tests\Mock\Interpreter\Provider\IdProvider;
use ImmediateSolutions\Shipple\Tests\Mock\Interpreter\Provider\ProductProvider;
use ImmediateSolutions\Shipple\Tests\Mock\Interpreter\Provider\SumProvider;
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
            'dummy' => new DummyProvider(),
            'concat' => new ConcatProvider(),
            'id' => new IdProvider(),
            'number' => new NumberProvider()
        ], []);

        $result = $interpreter->interpret(2);

        Assert::assertEquals(2, $result);
        Assert::assertTrue(is_int($result));

        $result = $interpreter->interpret('{{ sum: 10, 20 }}');

        Assert::assertEquals(30, $result);
        Assert::assertTrue(is_int($result));

        $result = $interpreter->interpret('some text to test !@#!%%@^^#%$%$& ()..,');

        Assert::assertEquals('some text to test !@#!%%@^^#%$%$& ()..,', $result);

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

        $result = $interpreter->interpret("some text \\\{\\\{ {{concat:'\{\{ 99 \' \}\}','b', 'false', 'null', delimiter='+'}} and this \\\}\\\} do this {{concat: '', '\{\{', 'foo', '\}\}'}}  the end");

        Assert::assertEquals("some text \{\{ {{ 99 ' }}+b+false+null and this \}\} do this {{foo}}  the end", $result);

        $result = $interpreter->interpret(
            "Я что-то сделал и не \{\{ и вообще й \n\n{{ sum: 10, 3 }}\n\n and ăp{{ concat: 'нафиг', 'мне Ă îș \{ \' that\'s все' }}lîș  вот что еще {{ concat: 'нафиг', 'мне Ă îș \{ \' that\'s все' }} решенно și нет");


        Assert::assertEquals($result, "Я что-то сделал и не {{ и вообще й \n\n13\n\n and ăpнафигмне Ă îș { ' that's всеlîș  вот что еще нафигмне Ă îș { ' that's все решенно și нет");


        $result = $interpreter->interpret('this is my "{{ id }}" don\'t miss it!');

        Assert::assertEquals('this is my "unique_text" don\'t miss it!', $result);

        $result = $interpreter->interpret('this is my "{{ id: }}" don\'t miss it!');

        Assert::assertEquals('this is my "unique_text" don\'t miss it!', $result);

        $result = $interpreter->interpret('this is my "{{ id 23, 44, \'hello\' }}" don\'t miss it!');

        Assert::assertEquals('this is my "{{ id 23, 44, \'hello\' }}" don\'t miss it!', $result);

        $result = $interpreter->interpret('this is my "{{ id }" don\'t miss it!');

        Assert::assertEquals('this is my "{{ id }" don\'t miss it!', $result);

        $result = $interpreter->interpret('this is my "{{ number: many=10 }" don\'t miss it!');

        Assert::assertEquals('this is my "{{ number: many=10 }" don\'t miss it!', $result);
    }

    public function testMatch()
    {
        $interpreter = new Interpreter([], [
            'choice' => new ChoiceMatcher(),
        ]);

        $result = $interpreter->match('what hey see that {{ choice: true }} hey this', 'what hey that hey this');

        Assert::assertFalse($result);

        $result = $interpreter->match('what hey see that {{ choice: \'a\', \'b\', \'c\' }} hey this', 'what hey see that b hey this');

        Assert::assertTrue($result);

        $result = $interpreter->match('what hey see that {{ choice: \'a\', \'b\', \'c\' }} hey this', 'what hey see that x hey this');

        Assert::assertFalse($result);

        $result = $interpreter->match('{{ choice: \'true\', false, 0, 1 }}', true);

        Assert::assertFalse($result);

        $result = $interpreter->match('{{ choice: false, null, true }}', true);

        Assert::assertTrue($result);

        $result = $interpreter->match(
            'what hey see that {{ choice: \'a\', \'b\', \'c\' }} hey this and that is {{ choice: 1, 3, 4 }}',
            'what hey see that a hey this and that is 4');

        Assert::assertTrue($result);

        $result = $interpreter->match('/users/documents/12/active', '/users/documents/14/active');

        Assert::assertFalse($result);

        $result = $interpreter->match('/users/documents/12/active', '/users/documents/12/active');

        Assert::assertTrue($result);

        $result = $interpreter->match(
            "something {{ choice: '\\\}\\\}\'', '\\\}\\\}' , '\\\{\\\{' }} test done and '{{ choice: 'yes', 'no' }}' or {{ choice: '\\\{\\\{ \\ \\\}\\\}', '?', ';'}}",
            "something \}\}' test done and 'yes' or \{\{ \\ \}\}");

        Assert::assertTrue($result);

        $result = $interpreter->match(10, 10);

        Assert::assertTrue($result);

        $result = $interpreter->match(true, false);

        Assert::assertFalse($result);
    }
}