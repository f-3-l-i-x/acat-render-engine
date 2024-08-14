<?php

namespace Tests\Render;

use JetBrains\PhpStorm\ArrayShape;

class RecordStructureTest extends AbstractRenderTest
{

    /**
     * @return array
     */
    #[ArrayShape(['word/document.xml' => "array", 'word/header2.xml' => "array"])]
    private function getRecordStructureValues() : array
    {
        return [
            'word/document.xml' => [
                'views'      => [],
                'fields'     => [
                    0  => 1757,
                    1  => 1744,
                    2  => 1752,
                    3  => 1745,
                    4  => 1747,
                    5  => 1748,
                    6  => 1749,
                    7  => 1750,
                    8  => 1858,
                    9  => 1860,
                    10 => 1862,
                    11 => 1863,
                    12 => 1760,
                    13 => 1761,
                    14 => 1762,
                    15 => 1741,
                    16 => 1742,
                    17 => 1676,
                    18 => 1768,
                    19 => 1765,
                    23 => 68,
                    24 => 1771,
                    26 => 1918,
                    27 => 1769,
                    28 => 1770,
                    29 => 1772,
                ],
                'blocks'     => [
                    0 => [
                        'fields'     => [
                            0 => 1909,
                            1 => 1910,
                            2 => 1914,
                            3 => 1916,
                        ],
                        'conditions' => [
                            0 => [
                                'field'    => 1910,
                                'operator' => '=',
                                'action'   => '3'
                            ]
                        ],
                        'views'      => [
                            'V_FOO',
                            'V_BAR'
                        ],
                    ],
                    1 => [
                        'fields'     => [
                            0 => 1774,
                            1 => 1779,
                            2 => 1794,
                        ],
                        'conditions' => [],
                    ],
                    2 => [
                        'fields'     => [
                            0 => 1787,
                            1 => 1786
                        ],
                        'conditions' => []
                    ],
                ],
                'conditions' => [
                    0  => [
                        'field'    => 1760,
                        'operator' => '<>',
                        'action'   => 0
                    ],
                    1  => [
                        'field'    => 1757,
                        'operator' => '<>',
                        'action'   => 0
                    ],
                    2  => [
                        'field'    => 1760,
                        'operator' => '<>',
                        'action'   => 0
                    ],
                    3  => [
                        'field'    => 1744,
                        'operator' => '=',
                        'action'   => 0
                    ],
                    4  => [
                        'field'    => 1760,
                        'operator' => '<>',
                        'action'   => 0
                    ],
                    5  => [
                        'field'    => 1752,
                        'operator' => '<>',
                        'action'   => 3
                    ],
                    6  => [
                        'field'    => 1760,
                        'operator' => '<>',
                        'action'   => 0
                    ],
                    7  => [
                        'field'    => 1760,
                        'operator' => '<>',
                        'action'   => 0
                    ],
                    8  => [
                        'field'    => 1858,
                        'operator' => '=',
                        'action'   => 0
                    ],
                    9  => [
                        'field'    => 1860,
                        'operator' => '=',
                        'action'   => 0
                    ],
                    10 => [
                        'field'    => 1862,
                        'operator' => '=',
                        'action'   => 0
                    ],
                    11 => [
                        'field'    => 1760,
                        'operator' => '=',
                        'action'   => 0
                    ],
                    12 => [
                        'field'    => 1761,
                        'operator' => '=',
                        'action'   => 0
                    ],
                    13 => [
                        'field'    => 1918,
                        'operator' => '=',
                        'action'   => 2
                    ],
                    14 => [
                        'field'    => 1918,
                        'operator' => '=',
                        'action'   => 2
                    ],
                ],
            ],
            'word/header2.xml'  => [
                'views'      => [],
                'fields'     => [
                    0 => 499,
                    2 => 478,
                    3 => 479,
                    4 => 501,
                    5 => 503,
                    6 => 489,
                    7 => 495,
                    8 => 481,
                ],
                'blocks'     => [],
                'conditions' => []
            ]
        ];
    }

}