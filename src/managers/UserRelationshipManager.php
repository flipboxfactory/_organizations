<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/organization/license
 * @link       https://www.flipboxfactory.com/software/organization/
 */

namespace flipbox\organizations\managers;

use Craft;
use craft\elements\User;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use flipbox\craft\ember\helpers\QueryHelper;
use flipbox\organizations\behaviors\OrganizationsAssociatedToUserBehavior;
use flipbox\organizations\elements\Organization;
use flipbox\organizations\Organizations;
use flipbox\organizations\queries\UserAssociationQuery;
use flipbox\organizations\records\UserAssociation;

/**
 * Manages Users associated to Organizations
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.00
 *
 * @property UserAssociation[] $associations
 *
 * @method UserAssociation findOrCreate($object)
 * @method UserAssociation findOne($object = null)
 * @method UserAssociation findOrFail($object)
 */
class UserRelationshipManager implements RelationshipManagerInterface
{
    use RelationshipManagerTrait;

    /**
     * @var Organization
     */
    private $organization;

    /**
     * @param Organization $organization
     */
    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * @param array $criteria
     * @return UserAssociationQuery
     */
    protected function query(array $criteria = []): UserAssociationQuery
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $query = UserAssociation::find()
            ->setOrganizationId($this->organization->getId() ?: false)
            ->orderBy([
                'userOrder' => SORT_ASC
            ]);

        if (!empty($criteria)) {
            QueryHelper::configure(
                $query,
                $criteria
            );
        }

        return $query;
    }

    /**
     * @param $object
     * @return UserAssociation
     */
    protected function create($object): UserAssociation
    {
        return (new UserAssociation())
            ->setOrganization($this->organization)
            ->setUser($this->resolveUser($object));
    }

    /**
     * @inheritDoc
     *
     * @param bool $addToUser
     */
    public function addOne($object, array $attributes = [], bool $addToUser = false)
    {
        if (null === ($association = $this->findOne($object))) {
            $association = $this->create($object);
            $this->addToCache($association);
        }

        if (!empty($attributes)) {
            Craft::configure(
                $association,
                $attributes
            );
        }

        // Add user to user as well?
        if ($addToUser && null !== ($use = $association->getUser())) {
            /** @var OrganizationsAssociatedToUserBehavior $use */
            $use->getOrganizationManager()->addOne($this->organization, [], false);
        }

        return $this;
    }


    /*******************************************
     * SAVE
     *******************************************/

    /**
     * @inheritDoc
     */
    protected function associationDelta(): array
    {
        $existingAssociations = $this->query()
            ->indexBy('userId')
            ->all();

        $associations = [];
        $order = 1;
        foreach ($this->findAll() as $newAssociation) {
            if (null === ($association = ArrayHelper::remove(
                $existingAssociations,
                $newAssociation->getUserId()
            ))) {
                $association = $newAssociation;
            }

            $association->userOrder = $order++;
            $association->organizationOrder = $newAssociation->organizationOrder;

            $associations[] = $association;
        }

        return [$associations, $existingAssociations];
    }

    /**
     * @inheritDoc
     */
    protected function handleAssociationError()
    {
        $this->organization->addError('users', 'Unable to save user organizations.');
    }


    /*******************************************
     * UTILS
     *******************************************/

    /**
     * @param UserAssociation|User|int|array|null $object
     * @return int|null
     */
    protected function findKey($object = null)
    {
        if (null === ($element = $this->resolveUser($object))) {
            Organizations::info(sprintf(
                "Unable to resolve user: %s",
                (string)Json::encode($object)
            ));
            return null;
        }

        foreach ($this->findAll() as $key => $association) {
            if (null !== $association->getUser() && $association->getUser()->email == $element->email) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @param UserAssociation|User|int|array|null $user
     * @return User|null
     */
    protected function resolveUser($user = null)
    {
        if (null === $user) {
            return null;
        }

        if ($user instanceof UserAssociation) {
            return $user->getUser();
        }

        if ($user instanceof User) {
            return $user;
        }

        if (is_array($user) &&
            null !== ($id = ArrayHelper::getValue($user, 'id'))
        ) {
            $user = ['id' => $id];
        }

        return User::findOne($user);
    }
}