<?php
namespace ImmediateSolutions\Shipple\Tests;

use ImmediateSolutions\Shipple\Code\Interpreter;
use ImmediateSolutions\Shipple\Code\Provider\DateTimeProvider;
use ImmediateSolutions\Shipple\Code\Provider\BetweenProvider;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class ProvidersTest extends TestCase
{
    public function testDateTime()
    {
        $interpreter = new Interpreter([
            'datetime' => new DateTimeProvider()
        ], []);

        $result = $interpreter->interpret("Today is {{ datetime: 'now', 'Y-m-d H:i:s' }} !!!");

        Assert::assertContains("Today is " . (new \DateTime())->format('Y-m-d'), $result);

        $result = $interpreter->interpret("Today is {{ datetime: 'Y-m-d H:i:s' }} !!!");

        Assert::assertContains("Today is {{ datetime: 'Y-m-d H:i:s' }} !!!", $result);

        $result = $interpreter->interpret("Today is {{ datetime: 'now' }} !!!");

        Assert::assertContains("Today is {{ datetime: 'now' }} !!!", $result);

        $result = $interpreter->interpret("Today is {{ datetime: 'some random stuff', 'some random stuff' }} !!!");

        Assert::assertContains("Today is {{ datetime: 'some random stuff', 'some random stuff' }} !!!", $result);

    }

    public function testBetween()
    {
        $interpreter = new Interpreter([
            'between' => new BetweenProvider()
        ], []);

        $result = $interpreter->interpret("{{ between: 1, 10 }}");

        Assert::assertTrue(is_int($result));

        Assert::assertTrue($result >= 1 && $result <= 10);

        $result = $interpreter->interpret("{{ between: min=1, max=10 }}");

        Assert::assertTrue($result >= 1 && $result <= 10);

        $result = $interpreter->interpret("{{ between }}");

        Assert::assertTrue(is_int($result));

        $result = $interpreter->interpret("{{ between: 100 }}");

        Assert::assertTrue($result >= 100 );

        $result = $interpreter->interpret("{{ between: max=10 }}");

        Assert::assertTrue($result <= 10);

        $result = $interpreter->interpret("{{ between: max=10, text=true }}");

        Assert::assertTrue(is_string($result));

        $result = $interpreter->interpret("{{ between: max=10, many=5 }}");

        Assert::assertTrue(is_array($result));

        Assert::assertCount(5, $result);

        $result = $interpreter->interpret("{{ between: max=2147483650 }}");

        Assert::assertEquals("{{ between: max=2147483650 }}", $result);

        $result = $interpreter->interpret("{{ between: -10, 100 }}");

        Assert::assertEquals("{{ between: -10, 100 }}", $result);
    }

    public function testFake()
    {
        $interpreter = new Interpreter([
            'between' => new BetweenProvider()
        ], []);

        $result = $interpreter->interpret("{{ between: max=10, text=19 }}");

        Assert::assertEquals("{{ between: max=10, text=19 }}", $result);

        $result = $interpreter->interpret("{{ between: max=10, many=true }}");

        Assert::assertEquals("{{ between: max=10, many=true }}", $result);
    }
}