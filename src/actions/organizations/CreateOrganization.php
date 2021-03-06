<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/organization/license
 * @link       https://www.flipboxfactory.com/software/organization/
 */

namespace flipbox\organizations\actions\organizations;

use craft\base\ElementInterface;
use flipbox\craft\ember\actions\elements\CreateElement;
use flipbox\organizations\elements\Organization;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CreateOrganization extends CreateElement
{
    use PopulateOrganizationTrait;

    /**
     * @inheritdoc
     * @return Organization|ElementInterface
     */
    public function newElement(array $config = []): ElementInterface
    {
        $organization = new Organization();

        $organization->setScenario(Organization::SCENARIO_LIVE);
        $organization->setAttributes($config);

        return $organization;
    }

    /**
     * @inheritdoc
     * @param Organization $object
     * @return Organization
     * @throws \Exception
     */
    public function populate(BaseObject $object): BaseObject
    {
        parent::populate($object);
        $this->populateFromRequest($object);

        return $object;
    }
}
