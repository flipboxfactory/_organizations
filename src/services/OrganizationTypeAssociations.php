<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/organization/license
 * @link       https://www.flipboxfactory.com/software/organization/
 */

namespace flipbox\organizations\services;

use flipbox\craft\sortable\associations\db\SortableAssociationQueryInterface;
use flipbox\craft\sortable\associations\records\SortableAssociationInterface;
use flipbox\craft\sortable\associations\services\SortableAssociations;
use flipbox\ember\services\traits\records\Accessor;
use flipbox\organizations\db\OrganizationTypeAssociationQuery;
use flipbox\organizations\records\OrganizationTypeAssociation;
use flipbox\organizations\records\OrganizationTypeAssociation as TypeAssociationRecord;
use yii\db\ActiveQuery;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method OrganizationTypeAssociationQuery parentGetQuery($config = [])
 * @method OrganizationTypeAssociation create(array $attributes = [])
 * @method OrganizationTypeAssociation find($identifier)
 * @method OrganizationTypeAssociation get($identifier)
 * @method OrganizationTypeAssociation findByCondition($condition = [])
 * @method OrganizationTypeAssociation getByCondition($condition = [])
 * @method OrganizationTypeAssociation findByCriteria($criteria = [])
 * @method OrganizationTypeAssociation getByCriteria($criteria = [])
 * @method OrganizationTypeAssociation[] findAllByCondition($condition = [])
 * @method OrganizationTypeAssociation[] getAllByCondition($condition = [])
 * @method OrganizationTypeAssociation[] findAllByCriteria($criteria = [])
 * @method OrganizationTypeAssociation[] getAllByCriteria($criteria = [])
 */
class OrganizationTypeAssociations extends SortableAssociations
{
    use Accessor {
        getQuery as parentGetQuery;
    }

    /**
     * @return string
     */
    const SOURCE_ATTRIBUTE = OrganizationTypeAssociation::SOURCE_ATTRIBUTE;

    /**
     * @return string
     */
    const TARGET_ATTRIBUTE = OrganizationTypeAssociation::TARGET_ATTRIBUTE;

    /**
     * @inheritdoc
     */
    protected static function tableAlias(): string
    {
        return OrganizationTypeAssociation::tableAlias();
    }

    /**
     * @param array $config
     * @return SortableAssociationQueryInterface|ActiveQuery
     */
    public function getQuery($config = []): SortableAssociationQueryInterface
    {
        return $this->parentGetQuery($config);
    }

    /**
     * @inheritdoc
     */
    public static function recordClass(): string
    {
        return OrganizationTypeAssociation::class;
    }

    /**
     * @param SortableAssociationInterface|TypeAssociationRecord $record
     * @return SortableAssociationQueryInterface|OrganizationTypeAssociationQuery
     */
    protected function associationQuery(
        SortableAssociationInterface $record
    ): SortableAssociationQueryInterface {
        return $this->query(
            $record->{static::SOURCE_ATTRIBUTE}
        );
    }

    /**
     * @param $source
     * @return SortableAssociationQueryInterface|OrganizationTypeAssociationQuery
     */
    private function query(
        $source
    ): SortableAssociationQueryInterface {
        /** @var OrganizationTypeAssociationQuery $query */
        $query = $this->getQuery();
        return $query->where([
            static::SOURCE_ATTRIBUTE => $source
        ])
            ->orderBy(['sortOrder' => SORT_ASC]);
    }

    /**
     * @param SortableAssociationQueryInterface|OrganizationTypeAssociationQuery $query
     * @return array
     */
    protected function existingAssociations(
        SortableAssociationQueryInterface $query
    ): array {
        $source = $this->resolveStringAttribute($query, static::SOURCE_ATTRIBUTE);

        if ($source === null) {
            return [];
        }

        return $this->associations($source);
    }

    /**
     * @param $source
     * @return array
     */
    private function associations(
        $source
    ): array {
        /** @var OrganizationTypeAssociationQuery $query */
        $query = $this->getQuery($source);
        return $query->indexBy(static::TARGET_ATTRIBUTE)
            ->all();
    }
}