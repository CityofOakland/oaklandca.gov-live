<?php
/**
 * Oakland Library Events module for Craft CMS 3.x
 *
 * This module grabs BiblioCommons Library Events and provides Twig Extensions to insert these events into your template queries.
 *
 * @link      https://meetgoat.com
 * @copyright Copyright (c) 2022 Goat Creative Inc.
 */

namespace modules\oaklandlibraryeventsmodule;

use modules\oaklandlibraryeventsmodule\assetbundles\oaklandlibraryeventsmodule\OaklandLibraryEventsModuleAsset;
use modules\oaklandlibraryeventsmodule\twigextensions\OaklandLibraryEventsModuleTwigExtension;

use Craft;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\TemplateEvent;
use craft\i18n\PhpMessageSource;
use craft\web\View;

use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\Module;

/**
 * Class OaklandLibraryEventsModule
 *
 * @author    Goat Creative Inc.
 * @package   OaklandLibraryEventsModule
 * @since     1.0.0
 *
 */
class OaklandLibraryEventsModule extends Module
{
    // Static Properties
    // =========================================================================

    /**
     * @var OaklandLibraryEventsModule
     */
    public static $instance;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        Craft::setAlias('@modules/oaklandlibraryeventsmodule', $this->getBasePath());
        $this->controllerNamespace = 'modules\oaklandlibraryeventsmodule\controllers';

        // Translation category
        $i18n = Craft::$app->getI18n();
        /** @noinspection UnSafeIsSetOverArrayInspection */
        if (!isset($i18n->translations[$id]) && !isset($i18n->translations[$id.'*'])) {
            $i18n->translations[$id] = [
                'class' => PhpMessageSource::class,
                'sourceLanguage' => 'en-US',
                'basePath' => '@modules/oaklandlibraryeventsmodule/translations',
                'forceTranslation' => true,
                'allowOverrides' => true,
            ];
        }

        // Base template directory
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $e) {
            if (is_dir($baseDir = $this->getBasePath().DIRECTORY_SEPARATOR.'templates')) {
                $e->roots[$this->id] = $baseDir;
            }
        });

        // Set this as the global instance of this module class
        static::setInstance($this);

        parent::__construct($id, $parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$instance = $this;

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE,
                function (TemplateEvent $event) {
                    try {
                        Craft::$app->getView()->registerAssetBundle(OaklandLibraryEventsModuleAsset::class);
                    } catch (InvalidConfigException $e) {
                        Craft::error(
                            'Error registering AssetBundle - '.$e->getMessage(),
                            __METHOD__
                        );
                    }
                }
            );
        }

        Craft::$app->view->registerTwigExtension(new OaklandLibraryEventsModuleTwigExtension());

        Craft::info(
            Craft::t(
                'oakland-library-events-module',
                '{name} module loaded',
                ['name' => 'Oakland Library Events']
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================
}
