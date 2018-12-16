<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/organization/license
 * @link       https://www.flipboxfactory.com/software/organization/
 */

namespace flipbox\organizations\records;

use Craft;
use flipbox\craft\ember\helpers\ModelHelper;
use flipbox\craft\ember\records\ActiveRecord;
use flipbox\craft\ember\records\IdAttributeTrait;
use flipbox\craft\ember\records\SortableTrait;
use flipbox\craft\ember\records\UserAttributeTrait;
use flipbox\organizations\queries\UserAssociationQuery;
use yii\db\ActiveQueryInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property int $organizationId
 * @property int $organizationOrder The order which an organization lists its users
 * @property int $userOrder The order which a user lists its organizations
 * @property Organization $organization
 * @property UserType[] $types
 */
class UserAssociation extends ActiveRecord
{
    use SortableTrait,
        UserAttributeTrait,
        IdAttributeTrait,
        OrganizationAttributeTrait;

    /**
     * The table name
     */
    const TABLE_ALIAS = Organization::TABLE_ALIAS . '_user_associations';

    /**
     * @inheritdoc
     */
    protected $getterPriorityAttributes = ['userId', 'organizationId'];

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @inheritdoc
     * @return UserAssociationQuery
     */
    public static function find()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Craft::createObject(UserAssociationQuery::class, [get_called_class()]);
    }

    /**
     * @inheritdoc
     *
     * @deprecated
     */
    public function associate(): bool
    {
        return $this->save();
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     *
     * @deprecated
     */
    public function dissociate(): bool
    {
        return $this->delete();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            $this->idRules(),
            $this->userRules(),
            $this->organizationRules(),
            [
                [
                    [
                        'userId',
                        'organizationId'
                    ],
                    'required'
                ],
                [
                    [
                        'userOrder',
                        'organizationOrder'
                    ],
                    'number',
                    'integerOnly' => true
                ],
                [
                    [
                        'userId',
                        'organizationId'
                    ],
                    'safe',
                    'on' => [
                        ModelHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }

    /**
     * @return ActiveQueryInterface
     */
    public function getTypes(): ActiveQueryInterface
    {
        // Todo - order this by the sortOrder
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->hasMany(UserType::class, ['id' => 'typeId'])
            ->viaTable(
                UserTypeAssociation::tableName(),
                ['userId' => 'id']
            );
    }
}
