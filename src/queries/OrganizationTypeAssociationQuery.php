<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/organization/license
 * @link       https://www.flipboxfactory.com/software/organization/
 */

namespace flipbox\organizations\queries;

use craft\helpers\Db;
use flipbox\craft\ember\queries\ActiveQuery;
use flipbox\craft\ember\queries\AuditAttributesTrait;
use flipbox\organizations\records\OrganizationTypeAssociation;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method OrganizationTypeAssociation one($db = null)
 * @method OrganizationTypeAssociation[] all($db = null)
 * @method OrganizationTypeAssociation[] getCachedResult($db = null)
 */
class OrganizationTypeAssociationQuery extends ActiveQuery
{
    use OrganizationTypeAttributeTrait,
        OrganizationAttributeTrait,
        AuditAttributesTrait;

    /**
     * @var int|null
     */
    public $sortOrder;

    /**
     * @param $sortOrder
     * @return $this
     */
    public function sortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    /**
     * @param $sortOrder
     * @return OrganizationTypeAssociationQuery
     */
    public function setSortOrder($sortOrder)
    {
        return $this->sortOrder($sortOrder);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->from([
            OrganizationTypeAssociation::tableName() . ' ' . OrganizationTypeAssociation::tableAlias()
        ]);

        parent::init();
    }

    /**
     * @param $value
     * @see $this->>organizationType()
     * @return static
     */
    public function type($value)
    {
        return $this->organizationType($value);
    }

    /**
     * @param $value
     * @see $this->>organizationType()
     * @return static
     */
    public function typeId($value)
    {
        return $this->organizationType($value);
    }

    /**
     * @inheritdoc
     */
    public function prepare($builder)
    {
        $attributes = ['sortOrder'];

        foreach ($attributes as $attribute) {
            if (null !== ($value = $this->{$attribute})) {
                $this->andWhere(
                    Db::parseParam($attribute, $value)
                );
            }
        }

        if ($this->organizationType !== null) {
            $this->andWhere(
                Db::parseParam('typeId', $this->parseOrganizationTypeValue($this->organizationType))
            );
        }

        if ($this->organization !== null) {
            $this->andWhere(
                Db::parseParam('organizationId', $this->parseOrganizationValue($this->organization))
            );
        }

        $this->applyAuditAttributeConditions();

        return parent::prepare($builder);
    }
}
