<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/organization/license
 * @link       https://www.flipboxfactory.com/software/organization/
 */

namespace flipbox\organizations\models;

use flipbox\craft\ember\helpers\ModelHelper;
use flipbox\organizations\records\OrganizationType;

/**
 * @property int|null $typeId
 * @property OrganizationType|null $type
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait OrganizationTypeRulesTrait
{
    /**
     * @return array
     */
    protected function typeRules(): array
    {
        return [
            [
                [
                    'typeId'
                ],
                'number',
                'integerOnly' => true
            ],
            [
                [
                    'typeId',
                    'type'
                ],
                'safe',
                'on' => [
                    ModelHelper::SCENARIO_DEFAULT
                ]
            ]
        ];
    }
}
