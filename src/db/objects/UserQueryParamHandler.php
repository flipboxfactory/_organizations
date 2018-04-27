<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/organization/license
 * @link       https://www.flipboxfactory.com/software/organization/
 */

namespace flipbox\organizations\db\objects;

use craft\elements\db\UserQuery;
use craft\helpers\Db;
use flipbox\organizations\db\behaviors\OrganizationAttributesToUserQueryBehavior;
use flipbox\organizations\db\traits\OrganizationAttribute;
use flipbox\organizations\db\traits\TypeAttribute;
use flipbox\organizations\db\traits\UserTypeAttribute;
use flipbox\organizations\records\UserAssociation as OrganizationUsersRecord;
use flipbox\organizations\records\UserTypeAssociation as UserCollectionUsersRecord;
use yii\base\BaseObject;
use yii\db\Query;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class UserQueryParamHandler extends BaseObject
{
    use OrganizationAttribute,
        TypeAttribute,
        UserTypeAttribute {
        setOrganizationType as parentSetOrganizationType;
        setUserType as parentSetUserType;
    }

    /**
     * @var OrganizationAttributesToUserQueryBehavior
     */
    private $owner;

    /**
     * @inheritdoc
     * @param OrganizationAttributesToUserQueryBehavior $owner
     */
    public function __construct(OrganizationAttributesToUserQueryBehavior $owner, array $config = [])
    {
        $this->owner = $owner;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function setUserType($value): UserQuery
    {
        $this->parentSetUserType($value);
        return $this->owner->owner;
    }

    /**
     * @inheritdoc
     */
    public function setOrganizationType($value): UserQuery
    {
        $this->parentSetOrganizationType($value);
        return $this->owner->owner;
    }

    /**
     * @param UserQuery $query
     */
    public function applyParams(UserQuery $query)
    {
        if ($query->subQuery === null ||
            (
                $this->organization === null &&
                $this->type === null &&
                $this->userType === null
            )
        ) {
            return;
        }

        $alias = $this->joinOrganizationUserTable($query->subQuery);

        $this->applyOrganizationParam(
            $query->subQuery,
            $this->organization,
            $alias
        );

        $this->applyUserTypeParam(
            $query->subQuery,
            $this->userType
        );

        // todo - implement types
    }

    /************************************************************
     * JOIN TABLES
     ************************************************************/

    /**
     * @inheritdoc
     */
    protected function joinOrganizationUserTable(Query $query): string
    {
        $alias = OrganizationUsersRecord::tableAlias();

        $query->leftJoin(
            OrganizationUsersRecord::tableName() . ' ' . $alias,
            '[[' . $alias . '.userId]] = [[elements.id]]'
        );

        return $alias;
    }

    /**
     * @inheritdoc
     */
    protected function joinOrganizationUserTypeTable(Query $query): string
    {
        $alias = UserCollectionUsersRecord::tableAlias();
        $orgAlias = OrganizationUsersRecord::tableAlias();
        $query->leftJoin(
            UserCollectionUsersRecord::tableName() . ' ' . $alias,
            '[[' . $alias . '.userId]] = [[' . $orgAlias . '.id]]'
        );

        return $alias;
    }


    /************************************************************
     * ORGANIZATION
     ************************************************************/

    /**
     * @param Query $query
     * @param $organization
     * @param string $alias
     */
    protected function applyOrganizationParam(Query $query, $organization, string $alias)
    {
        if (empty($organization)) {
            return;
        }

        $query->andWhere(
            Db::parseParam($alias . '.organizationId', $this->parseOrganizationValue($organization))
        );
    }


    /************************************************************
     * USER CATEGORY
     ************************************************************/

    /**
     * @param Query $query
     * @param $type
     */
    protected function applyUserTypeParam(Query $query, $type)
    {
        if (empty($type)) {
            return;
        }

        $alias = $this->joinOrganizationUserTypeTable($query);
        $query->andWhere(
            Db::parseParam($alias . '.typeId', $this->parseUserTypeValue($type))
        );

        $query->distinct(true);
    }
}
