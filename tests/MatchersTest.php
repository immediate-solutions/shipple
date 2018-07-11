<?php
namespace ImmediateSolutions\Shipple\Tests;

use ImmediateSolutions\Shipple\Code\Interpreter;
use ImmediateSolutions\Shipple\Code\Matcher\ChoiceMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\DateTimeMatcher;
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
    }

    public function testChoice()
    {
        $interpreter = new Interpreter([], [
            'choice' => new ChoiceMatcher()
        ]);

        $result = $interpreter->match("abc{{ choice: 4, 5, 'e' }}def", 'abc5def');

        Assert::assertTrue($result);

        $result = $interpreter->match("abc{{ choice: 4, 5, 'e' }}def", 'abc10def');

        Assert::assertFalse($result);

        $result = $interpreter->match("{{ choice: true, 10, false, 19.9, null }}", 10);

        Assert::assertTrue($result);

        $result = $interpreter->match("{{ choice: true, 10, false, 19.9, null }}", '10');

        Assert::assertFalse($result);
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

        $result = $interpreter->match("not working thing {{ type: 'text' }}", 'not working thing text');

        Assert::assertFalse($result);

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
    }
}