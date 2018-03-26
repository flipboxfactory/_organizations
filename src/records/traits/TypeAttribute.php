<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipboxfactory/craft-ember/blob/master/LICENSE
 * @link       https://github.com/flipboxfactory/craft-ember
 */

namespace flipbox\organization\records\traits;

use flipbox\ember\records\traits\ActiveRecord;
use flipbox\organization\Organization as OrganizationPlugin;
use flipbox\organization\records\Type as TypeRecord;
use flipbox\organization\traits\TypeMutator;
use flipbox\organization\traits\TypeRules;
use yii\db\ActiveQueryInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method TypeRecord parentResolveType()
 */
trait TypeAttribute
{
    use ActiveRecord,
        TypeRules,
        TypeMutator {
        resolveType as parentResolveType;
    }

    /**
     * Get associated typeId
     *
     * @return int|null
     */
    public function getTypeId()
    {
        $id = $this->getAttribute('typeId');
        if (null === $id && null !== $this->type) {
            $id = $this->type->id;
            $this->setAttribute('typeId', $id);
        }

        return $id !== false ? $id : null;
    }

    /**
     * @return TypeRecord|null
     */
    protected function resolveType()
    {
        if ($type = $this->resolveTypeFromRelation()) {
            return $type;
        }

        return $this->parentResolveType();
    }

    /**
     * @return TypeRecord|null
     */
    private function resolveTypeFromRelation()
    {
        if (false === $this->isRelationPopulated('typeRecord')) {
            return null;
        }

        return OrganizationPlugin::getInstance()->getTypes()->resolve($this->getRelation('typeRecord'));
    }

    /**
     * Get the associated Type
     *
     * @return ActiveQueryInterface
     */
    protected function getTypeRecord()
    {
        return $this->hasOne(
            TypeRecord::class,
            ['typeId' => 'id']
        );
    }
}
