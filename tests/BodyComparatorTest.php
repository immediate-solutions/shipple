<?php
namespace ImmediateSolutions\Shipple\Tests;

use ImmediateSolutions\Shipple\Code\Interpreter;
use ImmediateSolutions\Shipple\Code\Matcher\ChoiceMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\DigitsMatcher;
use ImmediateSolutions\Shipple\Code\Matcher\LessMatcher;
use ImmediateSolutions\Shipple\Comparator\BodyComparator;
use ImmediateSolutions\Shipple\Preference;
use ImmediateSolutions\Shipple\Tests\Mock\Comparator\Request;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class BodyComparatorTest extends TestCase
{
    public function testStrict()
    {
        $interpreter = new Interpreter([], [
            'choice' => new ChoiceMatcher(),
            'less' => new LessMatcher(),
            'digits'  => new DigitsMatcher(),
        ]);

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
                    'field31' => "{{ choice: true, false }}",
                    'field32' => [
                        'field321' => null,
                        'field322' => [],
                    ],
                    'field33' => "some other data{{ digits }} and that is it",
                ],
                'field4' => "{{ less: 0 }}",
            ]
        ], $request);

        Assert::assertTrue($result);
    }

    public function testSoft()
    {
        $interpreter = new Interpreter([], [
            'choice' => new ChoiceMatcher(),
            'less' => new LessMatcher(),
            'digits'  => new DigitsMatcher(),
        ]);

        $preference = new Preference();
        $preference->setMatchBodyType(Preference::MATCH_BODY_TYPE_JSON);

        $comparator = new BodyComparator($interpreter, $preference);

        $request = new Request([
            'body' => json_encode([
                'field1' => 12,
                'field2' => 'data9',
                'field3' => [
                    'field31' => true,
                    'field32' => [
                        'field321' => -10,
                        'field322' => ['a', 'b'],
                    ],
                    'field33' => 'some cool stuff',
                ],
                'field4' => 1,

            ])
        ]);

        $result = $comparator->compare([
            'body' => [
                'field1' => 12,

                'field2' => "data{{ choice: '1', '3', 1 }}",
                'field3' => [
                    'field31' => "{{ choice: true, false }}",
                    'field32' => [
                        'field321' => null,
                        'field322' => [],
                    ],
                    'field33' => "some other data{{ digits }} and that is it",
                ],
                'field4' => "{{ less: 0 }}",
            ],
            'options' => [
                'body' => [
                    'scope' => 'soft'
                ]
            ]
        ], $request);

        Assert::assertTrue($result);
    }

    public function testPartial()
    {
        $interpreter = new Interpreter([], [
            'choice' => new ChoiceMatcher(),
            'less' => new LessMatcher(),
            'digits'  => new DigitsMatcher(),
        ]);

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
                'field2' => "data{{ choice: '1', '3', 1 }}",
                'field3' => [
                    'field32' => [
                        'field321' => null,
                    ],
                ],
                'field4' => "{{ less: 0 }}",
            ],
            'options' => [
                'body' => [
                    'scope' => 'partial'
                ]
            ]
        ], $request);

        Assert::assertTrue($result);

        $result = $comparator->compare([
            'body' => [
                'field2' => "data{{ choice: '1', '3', 1 }}",
                'field3' => [
                    'field32' => [
                        'field321' => null,
                    ],
                ],
                'field4' => "{{ greater: 0 }}",
            ],
            'options' => [
                'body' => [
                    'scope' => 'partial'
                ]
            ]
        ], $request);

        Assert::assertFalse($result);
    }

    public function testOptional()
    {
        $interpreter = new Interpreter([], [
            'choice' => new ChoiceMatcher(),
            'less' => new LessMatcher(),
            'digits'  => new DigitsMatcher(),
        ]);

        $preference = new Preference();
        $preference->setMatchBodyType(Preference::MATCH_BODY_TYPE_JSON);

        $comparator = new BodyComparator($interpreter, $preference);

        $request = new Request([
            'body' => json_encode([
                'field3' => [
                    'field31' => true,
                    'field32' => [
                        'field322' => [],
                    ],
                    'field33' => 'some other data2 and that is it',
                ]
            ])
        ]);


        $result = $comparator->compare([
            'body' => [
                'field1' => 12,

                'field' => "data{{ choice: '1', '3', 1 }}",
                'field3' => [
                    'field31' => "{{ choice: true, false }}",
                    'field32' => [
                        'field321' => null,
                        'field322' => [],
                    ],
                    'field33' => "some other data{{ digits }} and that is it",
                ],
                'field4' => "{{ less: 0 }}",
            ],
            'options' => [
                'body' => [
                    'scope' => 'optional'
                ]
            ]
        ], $request);

        Assert::assertTrue($result);
    }

    public function testCodeInKeys()
    {
        $interpreter = new Interpreter([], [
            'choice' => new ChoiceMatcher(),
            'less' => new LessMatcher(),
            'digits'  => new DigitsMatcher(),
        ]);

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
                'field{{ digits }}' => "data{{ choice: '1', '3', 1 }}",
            ],
            'options' => [
                'body' => [
                    'scope' => 'partial'
                ]
            ]
        ], $request);

        Assert::assertTrue($result);

        $result = $comparator->compare([
            'body' => [
                'field{{ digits }}' => [
                    'field31' => "{{ choice: true, false }}",
                    'field32' => [
                        'field321' => null,
                        'field322' => [],
                    ],
                    'field33' => "some other data{{ digits }} and that is it",
                ],
            ],
            'options' => [
                'body' => [
                    'scope' => 'partial'
                ]
            ]
        ], $request);

        Assert::assertTrue($result);
    }

    public function testCollectionStrict()
    {
        $interpreter = new Interpreter([], [
            'choice' => new ChoiceMatcher(),
            'less' => new LessMatcher(),
            'digits'  => new DigitsMatcher(),
        ]);

        $preference = new Preference();
        $preference->setMatchBodyType(Preference::MATCH_BODY_TYPE_JSON);

        $comparator = new BodyComparator($interpreter, $preference);

        $request = new Request([
            'body' => json_encode([
                'field1' => [
                    [
                        'field12' => 'a',
                    ],
                    [
                        'field12' => 'b',
                    ],
                    [
                        'field12' => 'c',
                    ]
                ]
            ])
        ]);

        $result = $comparator->compare([
            'body' => [
                'field{{ digits }}' => [
                    [
                        'field12' => "{{ choice: 'a', 'x', 'y'}}",
                    ],
                    [
                        'field12' => 'c',
                    ],
                    [
                        'field12' => 'b',
                    ]
                ],
            ],
        ], $request);

        Assert::assertTrue($result);

        $request = new Request([
            'body' => json_encode([
                'field1' => ['a', 'b', 'c']
            ])
        ]);

        $result = $comparator->compare([
            'body' => [
                'field{{ digits }}' => ["{{ choice: 'a', 'x', 'y'}}", 'c', 'b'],
            ],
        ], $request);

        Assert::assertTrue($result);

        $request = new Request([
            'body' => json_encode([
                'field1' => [
                    [
                        'field12' => 'a',
                    ],
                    [
                        'field12' => 'b',
                    ],
                    [
                        'field12' => 'c',
                        'field13' => 2
                    ]
                ]
            ])
        ]);

        $result = $comparator->compare([
            'body' => [
                'field{{ digits }}' => [
                    [
                        'field12' => 'a',
                    ],
                    [
                        'field12' => 'c',
                    ],
                    [
                        'field12' => 'b',
                    ]
                ],
            ],
        ], $request);

        Assert::assertFalse($result);
    }

    public function testCollectionPartial()
    {
        $interpreter = new Interpreter([], [
            'choice' => new ChoiceMatcher(),
            'less' => new LessMatcher(),
            'digits'  => new DigitsMatcher(),
        ]);

        $preference = new Preference();
        $preference->setMatchBodyType(Preference::MATCH_BODY_TYPE_JSON);

        $comparator = new BodyComparator($interpreter, $preference);

        $request = new Request([
            'body' => json_encode([
                'field1' => [
                    [
                        'field12' => 'a',
                    ],
                    [
                        'field12' => 'c',
                    ],
                    [
                        'field12' => [
                            'field121' => true,
                            'field122' => [
                                'field1221' => null,
                            ]
                        ],
                    ]
                ]
            ])
        ]);

        $result = $comparator->compare([
            'body' => [
                'field{{ digits }}' => [
                    [
                        'field12' => "{{ choice: 'a', 'x', 'y'}}",
                    ],
                    [
                        'field{{ digits: 2 }}' => 'c',
                    ]
                ],
            ],
            'options' => [
                'body' => [
                    'scope' => 'partial'
                ]
            ]
        ], $request);

        Assert::assertTrue($result);

        $request = new Request([
            'body' => json_encode(['a', [
                    'field12' => [
                        'field121' => true,
                        'field122' => [
                            'field1221' => null,
                        ]
                    ],
                ], 'b'])
        ]);

        $result = $comparator->compare([
            'body' => ["{{ choice: 'a', 'x', 'y'}}", 'b'],
            'options' => [
                'body' => [
                    'scope' => 'partial'
                ]
            ]
        ], $request);

        Assert::assertTrue($result);
    }

    public function testCollectionSoft()
    {
        $interpreter = new Interpreter([], [
            'choice' => new ChoiceMatcher(),
            'less' => new LessMatcher(),
            'digits'  => new DigitsMatcher(),
        ]);

        $preference = new Preference();
        $preference->setMatchBodyType(Preference::MATCH_BODY_TYPE_JSON);

        $comparator = new BodyComparator($interpreter, $preference);

        $request = new Request([
            'body' => json_encode([
                'field1' => [
                    [
                        'field12' => 'a',
                    ],
                    [
                        'field12' => 'b',
                    ],
                    [
                        'field12' => 'c',
                    ]
                ]
            ])
        ]);

        $result = $comparator->compare([
            'body' => [
                'field1' => [
                    [
                        'field12' => [
                            'field121' => null,
                            'field122' => 'hello world',
                        ],
                    ],
                    [
                        'field12' => 'c',
                        'field13' => 'b'
                    ],
                    [
                        'field12' => true,
                    ]
                ],
                'field3' => 'this is {{ length: 10 }}'
            ],
            'options' => [
                'body' => [
                    'scope' => 'soft'
                ]
            ]
        ], $request);

        Assert::assertTrue($result);
    }


    public function testCollectionOptional()
    {
        $interpreter = new Interpreter([], [
            'choice' => new ChoiceMatcher(),
            'less' => new LessMatcher(),
            'digits'  => new DigitsMatcher(),
        ]);

        $preference = new Preference();
        $preference->setMatchBodyType(Preference::MATCH_BODY_TYPE_JSON);

        $comparator = new BodyComparator($interpreter, $preference);

        $request = new Request([
            'body' => json_encode([
                'field1' => [
                    [
                        'field14' => 'a',
                    ],
                    [
                        'field13' => 'b',
                    ],
                    [
                        'field12' => 'c',
                    ]
                ]
            ])
        ]);

        $result = $comparator->compare([
            'body' => [
                'field1' => [
                    [
                        'field12' => [
                            'field121' => null,
                            'field122' => 'hello world',
                        ],
                    ],
                    [
                        'field12' => 'c',
                        'field13' => 'b',
                        'field14' => 'a',
                    ],
                    [
                        'field12' => true,
                    ]
                ],
                'field3' => 'this is {{ length: 10 }}'
            ],
            'options' => [
                'body' => [
                    'scope' => 'optional'
                ]
            ]
        ], $request);

        Assert::assertTrue($result);
    }
}