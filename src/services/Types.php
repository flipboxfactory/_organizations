<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/organization/license
 * @link       https://www.flipboxfactory.com/software/organization/
 */

namespace flipbox\organization\services;

use Craft;
use craft\helpers\ArrayHelper;
use flipbox\ember\helpers\ObjectHelper;
use flipbox\ember\services\traits\records\AccessorByString;
use flipbox\organization\db\TypeQuery;
use flipbox\organization\elements\Organization as OrganizationElement;
use flipbox\organization\Organization as OrganizationPlugin;
use flipbox\organization\records\Type;
use flipbox\organization\records\Type as TypeRecord;
use flipbox\organization\records\TypeAssociation;
use yii\base\Component;
use yii\base\Exception;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method TypeQuery getQuery($config = []): ActiveQuery
 * @method TypeRecord create(array $attributes = [], string $toScenario = null)
 * @method TypeRecord find($identifier, string $toScenario = null)
 * @method TypeRecord get($identifier, string $toScenario = null)
 * @method TypeRecord findByString($identifier, string $toScenario = null)
 * @method TypeRecord getByString($identifier, string $toScenario = null)
 * @method TypeRecord findByCondition($condition = [], string $toScenario = null)
 * @method TypeRecord getByCondition($condition = [], string $toScenario = null)
 * @method TypeRecord findByCriteria($criteria = [], string $toScenario = null)
 * @method TypeRecord getByCriteria($criteria = [], string $toScenario = null)
 * @method TypeRecord[] findAllByCondition($condition = [], string $toScenario = null)
 * @method TypeRecord[] getAllByCondition($condition = [], string $toScenario = null)
 * @method TypeRecord[] findAllByCriteria($criteria = [], string $toScenario = null)
 * @method TypeRecord[] getAllByCriteria($criteria = [], string $toScenario = null)
 */
class Types extends Component
{
    use AccessorByString;

    /**
     * @inheritdoc
     */
    public static function recordClass(): string
    {
        return TypeRecord::class;
    }

    /**
     * @return string
     */
    protected function stringProperty(): string
    {
        return 'handle';
    }

    /*******************************************
     * RESOLVE
     *******************************************/

    /**
     * @param mixed $type
     * @return TypeRecord
     */
    public function resolve($type): TypeRecord
    {
        if ($type = $this->find($type)) {
            return $type;
        }

        $type = ArrayHelper::toArray($type, [], false);

        try {
            $object = $this->create($type);
        } catch (\Exception $e) {
            $object = new TypeRecord();
            ObjectHelper::populate(
                $object,
                $type
            );
        }

        return $object;
    }


    /**
     * @param TypeRecord|null $type
     * @return TypeRecord|null
     */
    public static function resolveFromRequest(TypeRecord $type = null)
    {
        if ($identifier = Craft::$app->getRequest()->getParam('type')) {
            return OrganizationPlugin::getInstance()->getTypes()->get($identifier);
        }

        if ($type instanceof TypeRecord) {
            return $type;
        }

        return null;
    }

    /**
     * @param array $config
     * @return array
     */
    protected function prepareQueryConfig($config = [])
    {
        $config['with'] = ['siteSettingRecords'];
        return $config;
    }

    /*******************************************
     * BEFORE/AFTER SAVE
     *******************************************/

    /**
     * @param TypeRecord $type
     * @return bool
     * @throws Exception
     */
    public function beforeSave(TypeRecord $type): bool
    {
        if (null === ($fieldLayout = $type->getFieldLayout())) {
            return true;
        }

        if (!Craft::$app->getFields()->saveLayout($fieldLayout)) {
            return false;
        }

        return true;
    }

    /**
     * @param TypeRecord $type
     * @throws Exception
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function afterSave(TypeRecord $type)
    {
        if (!OrganizationPlugin::getInstance()->getTypeSettings()->saveByType($type)) {
            throw new Exception("Unable to save site settings");
        };
    }

    /**
     * @param TypeQuery $query
     * @param OrganizationElement $organization
     * @return bool
     * @throws \Exception
     */
    public function saveAssociations(
        TypeQuery $query,
        OrganizationElement $organization
    ): bool {
        if (null === ($models = $query->getCachedResult())) {
            return true;
        }

        $associationService = OrganizationPlugin::getInstance()->getTypeAssociations();

        $query = $associationService->getQuery([
            $associationService::SOURCE_ATTRIBUTE => $organization->getId() ?: false
        ]);

        $query->setCachedResult(
            $this->toAssociations($models, $organization->getId())
        );

        return $associationService->save($query);
    }

    /**
     * @param TypeQuery $query
     * @param OrganizationElement $organization
     * @return bool
     * @throws \Exception
     */
    public function dissociate(
        TypeQuery $query,
        OrganizationElement $organization
    ): bool {
        return $this->associations(
            $query,
            $organization,
            [
                OrganizationPlugin::getInstance()->getTypeAssociations(),
                'dissociate'
            ]
        );
    }

    /**
     * @param TypeQuery $query
     * @param OrganizationElement $organization
     * @return bool
     * @throws \Exception
     */
    public function associate(
        TypeQuery $query,
        OrganizationElement $organization
    ): bool {
        return $this->associations(
            $query,
            $organization,
            [
                OrganizationPlugin::getInstance()->getTypeAssociations(),
                'associate'
            ]
        );
    }

    /**
     * @param TypeQuery $query
     * @param OrganizationElement $organization
     * @param callable $callable
     * @return bool
     */
    protected function associations(TypeQuery $query, OrganizationElement $organization, callable $callable)
    {
        if (null === ($models = $query->getCachedResult())) {
            return true;
        }

        $models = ArrayHelper::index($models, 'id');

        $success = true;
        $ids = [];
        $count = count($models);
        $i = 0;
        foreach ($this->toAssociations($models, $organization->getId()) as $association) {
            if (true === call_user_func_array($callable, [$association, ++$i === $count])) {
                ArrayHelper::remove($models, $association->typeId);
                $ids[] = $association->typeId;
                continue;
            }

            $success = false;
        }

        $query->typeId($ids);

        if ($success === false) {
            $query->setCachedResult($models);
        }

        return $success;
    }

    /**
     * @param Type[] $types
     * @param int $organizationId
     * @return TypeAssociation[]
     */
    private function toAssociations(
        array $types,
        int $organizationId
    ) {
        $associations = [];
        $sortOrder = 1;
        foreach ($types as $type) {
            $associations[] = new TypeAssociation([
                'organizationId' => $organizationId,
                'typeId' => $type->id,
                'sortOrder' => $sortOrder++
            ]);
        }

        return $associations;
    }
}
