<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/organization/license
 * @link       https://www.flipboxfactory.com/software/organization/
 */

namespace flipbox\organizations\events;

use flipbox\organizations\elements\Organization;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class RegisterOrganizationActionsEvent extends Event
{
    /**
     * @var Organization
     */
    public $organization;

    /**
     * @var array
     */
    public $destructiveActions = [];

    /**
     * @var array
     */
    public $miscActions = [];
}
