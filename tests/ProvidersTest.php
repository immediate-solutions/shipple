<?php
namespace ImmediateSolutions\Shipple\Tests;

use ImmediateSolutions\Shipple\Code\Interpreter;
use ImmediateSolutions\Shipple\Code\Provider\DateTimeProvider;
use ImmediateSolutions\Shipple\Code\Provider\NumberProvider;
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

    public function testNumber()
    {
        $interpreter = new Interpreter([
            'number' => new NumberProvider()
        ], []);

        $result = $interpreter->interpret("{{ number: 1, 10 }}");

        Assert::assertTrue(is_int($result));

        Assert::assertTrue($result >= 1 && $result <= 10);

        $result = $interpreter->interpret("{{ number: min=1, max=10 }}");

        Assert::assertTrue($result >= 1 && $result <= 10);

        $result = $interpreter->interpret("{{ number }}");

        Assert::assertTrue(is_int($result));

        $result = $interpreter->interpret("{{ number: 100 }}");

        Assert::assertTrue($result >= 100 );

        $result = $interpreter->interpret("{{ number: max=10 }}");

        Assert::assertTrue($result <= 10);

        $result = $interpreter->interpret("{{ number: max=10, text=true }}");

        Assert::assertTrue(is_string($result));

        $result = $interpreter->interpret("{{ number: max=10, many=5 }}");

        Assert::assertTrue(is_array($result));

        Assert::assertCount(5, $result);
    }
}