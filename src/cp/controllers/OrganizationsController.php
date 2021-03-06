<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/organization/license
 * @link       https://www.flipboxfactory.com/software/organization/
 */

namespace flipbox\organizations\cp\controllers;

use Craft;
use craft\helpers\ArrayHelper;
use flipbox\organizations\actions\organizations\CreateOrganization;
use flipbox\organizations\actions\organizations\DeleteOrganization;
use flipbox\organizations\actions\organizations\UpdateOrganization;
use flipbox\organizations\cp\actions\organization\SwitchType;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class OrganizationsController extends AbstractController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'error' => [
                    'default' => 'organization'
                ],
                'redirect' => [
                    'only' => ['create', 'update', 'delete'],
                    'actions' => [
                        'create' => [201],
                        'update' => [200],
                        'delete' => [204],
                    ]
                ],
                'flash' => [
                    'actions' => [
                        'create' => [
                            201 => Craft::t('organizations', "Organization successfully created."),
                            401 => Craft::t('organizations', "Failed to create organization.")
                        ],
                        'update' => [
                            200 => Craft::t('organizations', "Organization successfully updated."),
                            401 => Craft::t('organizations', "Failed to update organization.")
                        ],
                        'delete' => [
                            204 => Craft::t('organizations', "Organization successfully deleted."),
                            401 => Craft::t('organizations', "Failed to delete organization.")
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @return array
     */
    protected function verbs(): array
    {
        return [
            'switch-type' => ['post'],
            'create' => ['post'],
            'update' => ['post', 'put'],
            'delete' => ['post', 'delete']
        ];
    }

    /**
     * @param null $organization
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSwitchType($organization = null)
    {
        if (null === $organization) {
            $organization = Craft::$app->getRequest()->getBodyParam('organization');
        }

        /** @var SwitchType $action */
        $action = Craft::createObject([
            'class' => SwitchType::class
        ], [
            'switchType',
            $this
        ]);

        return $action->runWithParams([
            'organization' => $organization
        ]);
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        /** @var CreateOrganization $action */
        $action = Craft::createObject([
            'class' => CreateOrganization::class
        ], [
            'create',
            $this
        ]);

        return $action->runWithParams([]);
    }

    /**
     * @param string|int|null $organization
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($organization = null)
    {
        if (null === $organization) {
            $organization = Craft::$app->getRequest()->getBodyParam('organization');
        }

        /** @var UpdateOrganization $action */
        $action = Craft::createObject([
            'class' => UpdateOrganization::class
        ], [
            'update',
            $this
        ]);

        return $action->runWithParams([
            'organization' => $organization
        ]);
    }

    /**
     * @param string|int|null $organization
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDelete($organization = null)
    {
        if (null === $organization) {
            $organization = Craft::$app->getRequest()->getBodyParam('organization');
        }

        /** @var DeleteOrganization $action */
        $action = Craft::createObject([
            'class' => DeleteOrganization::class
        ], [
            'delete',
            $this
        ]);

        return $action->runWithParams([
            'organization' => $organization
        ]);
    }
}
