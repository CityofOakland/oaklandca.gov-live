<?php
/**
 * Dashboard Configuration plugin for Craft CMS 3.x
 *
 * Custom dashboard configuration for Craft CMS. Built for City of Oakland.
 *
 * @link      https://meetgoat.com
 * @copyright Copyright (c) 2021 Goat Creative Inc.
 */

namespace goat\dashboardconfiguration;

use goat\dashboardconfiguration\models\Settings;
use goat\dashboardconfiguration\widgets\WebsiteUpdates as WebsiteUpdatesWidget;
use goat\dashboardconfiguration\widgets\Documentation as DocumentationWidget;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\ConfigEvent;
use craft\events\PluginEvent;
use craft\services\Dashboard;
use craft\services\ProjectConfig;
use craft\events\RebuildConfigEvent;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;
use yii\base\Controller;
use yii\base\ActionEvent;
use yii\web\User;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Goat Creative Inc.
 * @package   DashboardConfiguration
 * @since     1.0.0
 *
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class DashboardConfiguration extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * DashboardConfiguration::$plugin
     *
     * @var DashboardConfiguration
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * DashboardConfiguration::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register our widgets
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = WebsiteUpdatesWidget::class;
                $event->types[] = DocumentationWidget::class;
            }
        );

        Event::on(
            Controller::class,
            Controller::EVENT_BEFORE_ACTION,
            function(ActionEvent $event){
                if($event->action->actionMethod == 'actionIndex' && $event->action->controller instanceof \craft\controllers\DashboardController){
                    $service = new \goat\dashboardconfiguration\services\Service();
                    $service->visitDashboard();
                }
            }
        );

        Craft::info(
            Craft::t(
                'dashboard-configuration',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'dashboard-configuration/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
