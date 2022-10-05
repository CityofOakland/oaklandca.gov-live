<?php
/**
 * Oakland Email Notifications plugin for Craft CMS 3.x
 *
 * Generate email notifications for various areas of the site including the Meetings entry type.
 *
 * @link      https://meetgoat.com
 * @copyright Copyright (c) 2022 Goat Creative Inc.
 */

namespace meetgoat\oaklandemailnotifications;

use meetgoat\oaklandemailnotifications\variables\OaklandEmailNotificationsVariable;
use meetgoat\oaklandemailnotifications\models\Settings;
use meetgoat\oaklandemailnotifications\fields\OaklandEmailSubscriptionsField;
use meetgoat\oaklandemailnotifications\fields\OaklandEmailTriggerField;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use craft\events\ElementEvent;
use craft\helpers\ElementHelper;
use craft\services\Elements;

use yii\base\Event;

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
 * @package   OaklandEmailNotifications
 * @since     1.0.0
 *
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class OaklandEmailNotifications extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * OaklandEmailNotifications::$plugin
     *
     * @var OaklandEmailNotifications
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
     * OaklandEmailNotifications::$plugin
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

        // Register our fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = OaklandEmailSubscriptionsField::class;
                $event->types[] = OaklandEmailTriggerField::class;
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('oaklandEmailNotifications', OaklandEmailNotificationsVariable::class);
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

        Craft::$app->elements->on(Elements::EVENT_BEFORE_SAVE_ELEMENT, function(ElementEvent $e) {
            if (ElementHelper::isDraftOrRevision($e->element)) {
                return;
            }

            $element                    = $e->element;
            $email_subscription_field   = null;
            $email_trigger_field        = null;

            if($element->isFresh) {
                return;
            }

            $old_element = Craft::$app->elements->getElementById($element->id);

            // Check if elements have a subscription field and a email trigger field.
            if($element && get_class($element) == 'craft\elements\Entry'){
                if($element->fieldLayout){
                    foreach($element->fieldLayout->customFieldElements as $customField){
                        if(get_class($customField->field) == 'meetgoat\oaklandemailnotifications\fields\OaklandEmailSubscriptionsField'){
                            $email_subscription_field = $customField->field;
                            break;
                        }
                    }

                    foreach($element->fieldLayout->customFieldElements as $customField){
                        if(get_class($customField->field) == 'meetgoat\oaklandemailnotifications\fields\OaklandEmailTriggerField'){
                            $email_trigger_field = $customField->field;
                            break;
                        }
                    }
                }
            }

            if($email_subscription_field && $email_trigger_field) {
                $email_trigger          = $email_trigger_field->handle;
                $email_should_trigger   = false;

                if($element->$email_trigger === true){
                    $settings = $this->getSettings();

                    if(isset($settings['emailTemplates'][$element->type->handle])) {
                        $subject    = $settings['emailSubjects'][$element->type->handle];
                        $template   = $settings['emailTemplates'][$element->type->handle];
                        $body       = '';

                        if(preg_match_all("~\{\{(.*?)\}\}~", $template, $matches_array)){
                            $matches = $matches_array[1];

                            foreach($matches as $attribute_name) {
                                $attribute_name = trim($attribute_name);
                                $value          = $element->$attribute_name;
                                if($element->isAttributeDirty($attribute_name)){
                                    $email_should_trigger = true;
                                    $old_value = $old_element->$attribute_name;
                                }

                                $field = Craft::$app->fields->getFieldByHandle($attribute_name);
                                $class = $field ? get_class($field) : null;

                                if($class) {
                                    switch($class){
                                        case 'craft\\fields\\Time':
                                            $value = $value->format('g:ia');
                                            break;
                                        case 'craft\\fields\\Date':
                                            $value = $value->format('F j, Y');
                                            break;
                                        case 'craft\\fields\\Dropdown':
                                            $value = ucfirst($value);
                                            break;
                                        case 'craft\\fields\\Matrix':
                                            $matrix_string = '<ul>';
                                            foreach($value as $block){
                                                foreach($block->fieldLayout->fields as $field) {
                                                    $block_class        = $field ? get_class($field) : null;
                                                    $block_field_handle = $field->handle;

                                                    switch($block_class){
                                                        case 'craft\\fields\\PlainText':
                                                            $matrix_string .= ("<li>" . $field->name . ": ");
                                                            $matrix_string .= $block->$block_field_handle;
                                                            $matrix_string .= "</li>";
                                                            break;
                                                        case 'craft\\fields\\Assets':
                                                            if(count($block->$block_field_handle->all())){
                                                                $matrix_string .= ("<li>" . $field->name . ": ");
                                                                $matrix_string .="<ul>";
                                                                foreach($block->$block_field_handle->all() as $asset){
                                                                    $matrix_string .="<li><a href='" . $asset->url . "'>". $asset->title ."</a></li>";
                                                                }
                                                                $matrix_string .="</ul>";
                                                                $matrix_string .= "</li>";
                                                            }
                                                            break;
                                                        default:
                                                            break;
                                                    }
                                                }
                                            }
                                            $matrix_string .= "</ul>";
                                            $value = $matrix_string;
                                            break;
                                        default:
                                            break;
                                    }
                                }

                                $subject    = str_replace('{{ '. $attribute_name .' }}', $value, $subject);
                                $template   = str_replace('{{ '. $attribute_name .' }}', $value, $template);
                            }
                        }

                        if($email_should_trigger) {
                            Craft::$app
                                ->getMailer()
                                ->compose()
                                ->setTo('tim.lu@meetgoat.com')
                                ->setSubject($subject)
                                ->setHtmlBody($template)
                                ->send();
                        }
                    }
                }
            }
        });

/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
        Craft::info(
            Craft::t(
                'oakland-email-notifications',
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
            'oakland-email-notifications/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
