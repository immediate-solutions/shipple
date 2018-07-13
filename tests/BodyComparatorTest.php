<?php
namespace ImmediateSolutions\Shipple\Tests;

use ImmediateSolutions\Shipple\Code\Interpreter;
use ImmediateSolutions\Shipple\Comparator\BodyComparator;
use ImmediateSolutions\Shipple\Preference;
use ImmediateSolutions\Shipple\Tests\Mock\Comparator\Request;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class BodyComparatorTest// extends TestCase
{
    public function testDefault()
    {
        $interpreter = new Interpreter([], []);

        $preference = new Preference();
        $preference->setMatchBodyType(Preference::MATCH_BODY_TYPE_JSON);

        $comparator = new BodyComparator($interpreter, $preference);

        $request = new Request([
            'body' => json_encode([
                'field1' => 12,
                'field2' => 'data1',
                'field3' => [
                    'field31' => true,
                    'field32' => [
                        'field321' => null,
                        'field322' => [],
                    ],
                    'field33' => 'some other data2 and that is it',
                ],
                'field4' => -99.2,

            ])
        ]);

        $result = $comparator->compare([
            'body' => [
                'field1' => 12,
                'field2' => "data{{ choice: '1', '3', 1 }}",
                'field3' => [
                    'field31' => "data{{ choice: true, false }}",
                    'field32' => [
                        'field321' => null,
                        'field322' => [],
                    ],
                    'field33' => "some other data{{ pattern: '^[0-9]+$' }} and that is it",
                ],
                'field4' => -99.2,
            ]
        ], $request);

        Assert::assertTrue($result);
    }
}