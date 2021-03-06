<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/organization/license
 * @link       https://www.flipboxfactory.com/software/organization/
 */

namespace flipbox\organizations\relationships;

use craft\elements\db\ElementQueryInterface;
use craft\elements\db\UserQuery;
use craft\elements\User;
use craft\helpers\ArrayHelper;
use flipbox\organizations\elements\Organization;
use flipbox\organizations\Organizations;
use flipbox\organizations\queries\OrganizationQuery;
use flipbox\organizations\queries\UserAssociationQuery;
use flipbox\organizations\records\UserAssociation;
use Tightenco\Collect\Support\Collection;

/**
 * Manages Users associated to Organizations
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 *
 * @method UserAssociation findOrCreate($object)
 * @method UserAssociation findOne($object = null)
 * @method UserAssociation findOrFail($object)
 */
class UserRelationship implements ElementRelationshipInterface
{
    use RelationshipTrait;

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


    /************************************************************
     * COLLECTION
     ************************************************************/

    /**
     * Get a collection of associated users
     *
     * @return User[]|Collection
     */
    public function getCollection(): Collection
    {
        if (null === $this->relations) {
            return new Collection(
                $this->getQuery()
                    ->anyStatus()
                    ->limit(null)
                    ->all()
            );
        }

        return $this->createCollectionFromRelations();
    }

    /**
     * @return Collection
     */
    protected function createCollectionFromRelations()
    {
        $ids = $this->getRelationships()->pluck('userId')->all();
        if (empty($ids)) {
            return $this->getRelationships()->pluck('user');
        }

        // 'eager' load where we'll pre-populate all of the elements
        $elements = $this->getQuery()
            ->id($ids)
            ->indexBy('id')
            ->anyStatus()
            ->limit(null)
            ->all();

        return $this->getRelationships()
            ->transform(function (UserAssociation $association) use ($elements) {
                if (!$association->isUserSet() && isset($elements[$association->getUserId()])) {
                    $association->setUser($elements[$association->getUserId()]);
                }

                $association->setOrganization($this->organization);

                return $association;
            })
            ->pluck('user');
    }

    /**
     * @return Collection
     */
    protected function existingRelationships(): Collection
    {
        $relationships = $this->associationQuery()
            ->with('types')
            ->all();

        return $this->createRelations($relationships);
    }

    /************************************************************
     * QUERY
     ************************************************************/

    /**
     * @inheritDoc
     * @return UserQuery
     */
    public function getQuery(): ElementQueryInterface
    {
        return User::find()
            ->organizationId($this->organization->getId() ?: false);
    }

    /**
     * @return UserAssociationQuery
     */
    private function associationQuery(): UserAssociationQuery
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return UserAssociation::find()
            ->setOrganizationId($this->organization->getId() ?: false)
            ->orderBy([
                'organizationOrder' => SORT_ASC
            ])
            ->limit(null);
    }

    /************************************************************
     * CREATE
     ************************************************************/

    /**
     * @param $object
     * @return UserAssociation
     */
    protected function create($object): UserAssociation
    {
        if ($object instanceof UserAssociation) {
            return $object;
        }

        return (new UserAssociation())
            ->setOrganization($this->organization)
            ->setUser($this->resolveObject($object));
    }


    /*******************************************
     * DELTA
     *******************************************/

    /**
     * @inheritDoc
     */
    protected function delta(): array
    {
        $existingAssociations = $this->associationQuery()
            ->indexBy('userId')
            ->all();

        $associations = [];
        $order = 1;

        /** @var UserAssociation $newAssociation */
        foreach ($this->getRelationships() as $newAssociation) {
            $association = ArrayHelper::remove(
                $existingAssociations,
                $newAssociation->getUserId()
            );

            $newAssociation->userOrder = $order++;

            /** @var UserAssociation $association */
            $association = $association ?: $newAssociation;

            // Has anything changed?
            if (!$association->getIsNewRecord() && !$this->hasChanged($newAssociation, $association)) {
                continue;
            }

            $associations[] = $this->sync($association, $newAssociation);
        }

        return [$associations, $existingAssociations];
    }

    /**
     * @param UserAssociation $new
     * @param UserAssociation $existing
     * @return bool
     */
    private function hasChanged(UserAssociation $new, UserAssociation $existing): bool
    {
        return (Organizations::getInstance()->getSettings()->getEnforceUserSortOrder() &&
                $new->userOrder != $existing->userOrder
            ) ||
            $new->state != $existing->state ||
            $new->getTypes()->isMutated();
    }

    /**
     * @param UserAssociation $from
     * @param UserAssociation $to
     *
     * @return UserAssociation
     */
    private function sync(UserAssociation $to, UserAssociation $from): UserAssociation
    {
        $to->userOrder = $from->userOrder;
        $to->state = $from->state;

        if ($from->getTypes()->isMutated()) {
            $to->getTypes()->clear()->add(
                $from->getTypes()->getCollection()
            );
        }

        $to->ignoreSortOrder();

        return $to;
    }


    /*******************************************
     * COLLECTION UTILS
     *******************************************/

    /**
     * @inheritDoc
     */
    protected function insertCollection(Collection $collection, UserAssociation $association)
    {
        if (Organizations::getInstance()->getSettings()->getEnforceUserSortOrder() && $association->userOrder > 0) {
            $collection->splice($association->userOrder - 1, 0, [$association]);
            return;
        }

        $collection->push($association);
    }

    /**
     * @inheritDoc
     */
    protected function updateCollection(Collection $collection, UserAssociation $association)
    {
        if (!Organizations::getInstance()->getSettings()->getEnforceUserSortOrder()) {
            return;
        }

        if (null !== ($key = $this->findKey($association))) {
            $collection->offsetUnset($key);
        }

        $this->insertCollection($collection, $association);
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
        if ($object instanceof UserAssociation) {
            if (!$object->getUser()) {
                var_dump("NOT FOUND");
                return null;
            }

            return $this->findRelationshipKey($object->getUser()->email);
        }

        if (null === ($element = $this->resolveObject($object))) {
            return null;
        }

        return $this->findRelationshipKey($element->email);
    }

    /**
     * @param $identifier
     * @return int|string|null
     */
    private function findRelationshipKey($identifier)
    {
        if (null === $identifier) {
            return null;
        }

        /** @var UserAssociation $association */
        foreach ($this->getRelationships()->all() as $key => $association) {
            if (null !== $association->getUser() && $association->getUser()->email == $identifier) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @param UserAssociation|User|int|array|null $user
     * @return User|null
     */
    protected function resolveObjectInternal($user)
    {
        if ($user instanceof UserAssociation) {
            return $user->getUser();
        }

        if ($user instanceof User) {
            return $user;
        }

        if (is_numeric($user)) {
            return \Craft::$app->getUsers()->getUserById($user);
        }

        if (is_string($user)) {
            return \Craft::$app->getUsers()->getUserByUsernameOrEmail($user);
        }

        return User::findOne($user);
    }
}
