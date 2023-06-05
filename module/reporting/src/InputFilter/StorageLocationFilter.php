<?php

declare(strict_types=1);

namespace Reporting\InputFilter;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator\StringLength;

final class StorageLocationFilter extends InputFilter
{
    public function __construct()
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            input: [
                'name'       => 'name',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 255,
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            input: [
                'name'       => 'connectionString',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 2000,
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            input: [
                'name'       => 'excelFolder',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 255,
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            input: [
                'name'       => 'parquetFolder',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 255,
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            input: [
                'name'     => 'oAuth2Service',
                'required' => false,
            ]
        );

        $this->add(input: $inputFilter, name: 'reporting_entity_storagelocation');
    }
}
