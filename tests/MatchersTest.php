<?php
namespace ImmediateSolutions\Shipple\Tests;

use ImmediateSolutions\Shipple\Code\Interpreter;
use ImmediateSolutions\Shipple\Code\Matcher\ChoiceMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\DateTimeMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\DigitsMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\FloatMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\GreaterMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\IntegerMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\LessMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\PatternMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\TypeMatcher;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class MatchersTest extends TestCase
{
    public function testPattern()
    {
        $interpreter = new Interpreter([], [
            'pattern' => new PatternMatcher()
        ]);

        $result = $interpreter->match("abc{{ pattern: '^[0-9,]+$' }}def", 'abc1,4,2def');

        Assert::assertTrue($result);

        $result = $interpreter->match("abc{{ pattern: '^[0-9,]+$' }}def", 'abc1,4a,2def');

        Assert::assertFalse($result);

        $result = $interpreter->match("abc{{ pattern: '^[0-9,]+$' }}def", []);

        Assert::assertFalse($result);
    }

    public function testChoice()
    {
        $interpreter = new Interpreter([], [
            'choice' => new ChoiceMatcher()
        ]);

        $result = $interpreter->match("abc{{ choice: '4', '5', 'e' }}def", 'abc5def');

        Assert::assertTrue($result);

        $result = $interpreter->match("abc{{ choice: 4, 5, 'e' }}def", 'abc10def');

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ choice: true, 10, false, 19.9, null }}", 10);

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ choice: true, 10, false, 19.9, null }}", '10');

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ choice: true, 10, 0, [], null }}", false);

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ choice: true, 10, false, [], null }}", false);

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ choice: true, 10, false, [], null }}", []);

        Assert::assertTrue($result);
    }

    public function testType()
    {
        $interpreter = new Interpreter([], [
            'type' => new TypeMatcher()
        ]);

        $result = $interpreter->match("{{ type: 'text' }}", 'abc');

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ type: 'text' }}", 45);

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ type: 'number' }}", 45);

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ type: 'int' }}", 45);

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ type: 'int' }}", 45.3);

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ type: 'number' }}", 45.3);

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ type: 'int' }}", '21');

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ type: 'bool' }}", true);

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ type: 'bool' }}", false);

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ type: 'bool' }}", 1);

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ type: 'bool' }}", 'false');

        Assert::assertFalse($result);

        $result = $interpreter->match("not working thing {{ type: 'int' }}", 'not working thing 2');

        Assert::assertFalse($result);

        $result = $interpreter->match("this is working {{ type: 'text' }}", 'this is working thing');

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ type: '[]' }}", []);

        Assert::assertTrue($result);

    }

    public function testDateTime()
    {
        $interpreter = new Interpreter([], [
            'datetime' => new DateTimeMatcher()
        ]);

        $result = $interpreter->match("{{ datetime: 'Y-m-d H:i:s' }}", '2019-01-12 12:00:10');

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ datetime: format='d.m.Y' }}", '01.02.2021');

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ datetime: 'd.m.Y H:i:s' }}", '2019-01-12 12:00:10');

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ datetime: 'wrong thing' }}", '2019-01-12 12:00:10');

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ datetime: 24 }}", '2019-01-12 12:00:10');

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ datetime: format='d.m.Y' }}", []);

        Assert::assertFalse($result);
    }

    public function testDigits()
    {
        $interpreter = new Interpreter([], [
            'digits' => new DigitsMatcher()
        ]);


        $result = $interpreter->match("/users/documents/{{ digits }}", '/users/documents/23/active');

        Assert::assertFalse($result);

        $result = $interpreter->match("/users/documents/{{ digits }}", '/users/documents/23');

        Assert::assertTrue($result);

        $result = $interpreter->match("/users/documents/{{ digits:3 }}", '/users/documents/3452');

        Assert::assertFalse($result);

        $result = $interpreter->match("/users/documents/{{ digits:3 }}", []);

        Assert::assertFalse($result);

        $result = $interpreter->match("/users/documents/{{ digits:3 }}", '/users/documents/443');

        Assert::assertTrue($result);

        $result = $interpreter->match("/users/documents/{{ digits:'3' }}", '/users/documents/443');

        Assert::assertFalse($result);

        $result = $interpreter->match("/users/documents/{{ digits:3.4 }}", '/users/documents/443');

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ digits }}", []);

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ digits }}", 299);

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ digits }}", '-99');

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ digits }}", true);

        Assert::assertFalse($result);
    }

    public function testFloat()
    {
        $interpreter = new Interpreter([], [
            'float' => new FloatMatcher()
        ]);

        $result = $interpreter->match(
            "Here's what you own me {{ float }}, so don't mess it up with {{ float }}",
            "Here's what you own me 7.4, so don't mess it up with -1");

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ float }}", []);

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ float }}", 2.1);

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ float }}", '-2.1');

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ float }}", 1);

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ float }}", true);

        Assert::assertFalse($result);
    }

    public function testInteger()
    {
        $interpreter = new Interpreter([], [
            'int' => new IntegerMatcher(),
            'float' => new FloatMatcher()
        ]);

        $result = $interpreter->match(
            "Here's what you own me {{ int }}, so don't mess it up with {{ float }}",
            "Here's what you own me -1, so don't mess it up with -1.2");

        Assert::assertTrue($result);

        $result = $interpreter->match(
            "Here's what you own me {{ int }}, so don't mess it up with {{ int }}",
            "Here's what you own me -1, so don't mess it up with -1.2");

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ int }}", []);

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ int }}", 299);

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ int }}", '-9986');

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ int }}", true);

        Assert::assertFalse($result);
    }

    public function testGreater()
    {
        $interpreter = new Interpreter([], [
            'greater' => new GreaterMatcher()
        ]);

        $result = $interpreter->match(
            "Here's what you own me {{ greater: 2.1 }}, so don't mess it up with {{ greater: 10 }}",
            "Here's what you own me 2.2, so don't mess it up with 20");

        Assert::assertTrue($result);

        $result = $interpreter->match(
            "Here's what you own me {{ greater: 2.1 }}, so don't mess it up with {{ greater: 10 }}",
            "Here's what you own me 2.01, so don't mess it up with 20");

        Assert::assertFalse($result);

        $result = $interpreter->match(
            "Here's what you own me {{ greater: 2.1 }}, so don't mess it up",
            "Here's what you own me many, so don't mess it up");

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ greater }}", 0);

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ greater: '12' }}", 19);

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ greater: 19 }}", []);

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ greater: 1 }}", 200);

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ greater:-9 }}", '-2.1');

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ greater: 0 }}", true);

        Assert::assertFalse($result);
    }

    public function testLess()
    {
        $interpreter = new Interpreter([], [
            'less' => new LessMatcher()
        ]);

        $result = $interpreter->match(
            "Here's what you own me {{ less: 2.1 }}, so don't mess it up with {{ less: 10 }}",
            "Here's what you own me 2.0, so don't mess it up with 4");

        Assert::assertTrue($result);

        $result = $interpreter->match(
            "Here's what you own me {{ less: 2.1 }}, so don't mess it up with {{ less: 10 }}",
            "Here's what you own me 2.1001, so don't mess it up with 2");

        Assert::assertFalse($result);

        $result = $interpreter->match(
            "Here's what you own me {{ less: 2.1 }}, so don't mess it up",
            "Here's what you own me many, so don't mess it up");

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ less }}", 1);

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ less: '12' }}", 11);

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ less: 19 }}", []);

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ less: 200 }}", 2);

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ less:-9 }}", '-20.1');

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ less: 1 }}", false);

        Assert::assertFalse($result);
    }
}