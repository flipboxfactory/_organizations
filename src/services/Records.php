<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/organization/license
 * @link       https://www.flipboxfactory.com/software/organization/
 */

namespace flipbox\organization\services;

use Craft;
use flipbox\ember\services\traits\records\Accessor;
use flipbox\organization\events\ChangeStateEvent;
use flipbox\organization\records\Organization as OrganizationRecord;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method OrganizationRecord create(array $attributes = [], string $toScenario = null)
 * @method OrganizationRecord find($identifier, string $toScenario = null)
 * @method OrganizationRecord get($identifier, string $toScenario = null)
 * @method OrganizationRecord findByCondition($condition = [], string $toScenario = null)
 * @method OrganizationRecord getByCondition($condition = [], string $toScenario = null)
 * @method OrganizationRecord findByCriteria($criteria = [], string $toScenario = null)
 * @method OrganizationRecord getByCriteria($criteria = [], string $toScenario = null)
 * @method OrganizationRecord[] findAllByCondition($condition = [], string $toScenario = null)
 * @method OrganizationRecord[] getAllByCondition($condition = [], string $toScenario = null)
 * @method OrganizationRecord[] findAllByCriteria($criteria = [], string $toScenario = null)
 * @method OrganizationRecord[] getAllByCriteria($criteria = [], string $toScenario = null)
 */
class Records extends Component
{
    use Accessor;

    /**
     * @event ChangeStateEvent The event that is triggered before a organization has a custom status change.
     *
     * You may set [[ChangeStateEvent::isValid]] to `false` to prevent the organization changing the status.
     */
    const EVENT_BEFORE_STATE_CHANGE = 'beforeStateChange';

    /**
     * @event ChangeStateEvent The event that is triggered after a organization has a custom status change.
     *
     * You may set [[ChangeStateEvent::isValid]] to `false` to prevent the organization changing the status.
     */
    const EVENT_AFTER_STATE_CHANGE = 'afterStateChange';

    /**
     * @inheritdoc
     */
    public static function recordClass(): string
    {
        return OrganizationRecord::class;
    }

    /**
     * @param OrganizationRecord $record
     * @return bool
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function save(OrganizationRecord $record): bool
    {
        // Change state w/ events (see below)
        $changedState = $this->hasStateChanged($record);

        $transaction = Craft::$app->getDb()->beginTransaction();
        try {
            if (!$record->save()) {
                $transaction->rollBack();
                return false;
            }

            if (false !== $changedState &&
                true !== $this->changeState($record, $changedState)
            ) {
                $transaction->rollBack();
                return false;
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        $transaction->commit();
        return true;
    }

    /**
     * @param OrganizationRecord $record
     * @return false|string|null
     */
    private function hasStateChanged(OrganizationRecord $record)
    {
        if ($record->getIsNewRecord()) {
            return false;
        }

        if (!$record->isAttributeChanged('state', false)) {
            return false;
        }

        $oldState = $record->getOldAttribute('state');
        $newState = $record->getAttribute('state');

        // Revert record to old
        $record->setAttribute('state', $oldState);

        return $newState;
    }


    /**
     * @param OrganizationRecord $record
     * @param string|null $state
     * @return bool
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function changeState(
        OrganizationRecord $record,
        string $state = null
    ): bool {
        $event = new ChangeStateEvent([
            'organization' => $record,
            'to' => $state
        ]);

        $this->trigger(
            static::EVENT_BEFORE_STATE_CHANGE,
            $event
        );

        if (!$event->isValid) {
            return false;
        }

        $transaction = Craft::$app->getDb()->beginTransaction();
        try {
            $record->state = $event->to;

            // Organization Record (status only)
            if (!$record->save(true, ['state'])) {
                $transaction->rollBack();
                return false;
            }

            // Trigger event
            $this->trigger(
                static::EVENT_AFTER_STATE_CHANGE,
                $event
            );
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        $transaction->commit();
        return true;
    }
}
