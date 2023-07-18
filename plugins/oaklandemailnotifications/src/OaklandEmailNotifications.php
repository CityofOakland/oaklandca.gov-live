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
        // Event::on(
        //     Plugins::class,
        //     Plugins::EVENT_AFTER_INSTALL_PLUGIN,
        //     function (PluginEvent $event) {
        //         if ($event->plugin === $this) {
        //             // We were just installed
        //         }
        //     }
        // );

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

                            foreach($matches as $field_handle) {
                                $field_handle   = trim($field_handle);
                                $value          = $element->$field_handle;
                                if($element->isFieldDirty($field_handle)){
                                    $email_should_trigger = true;
                                }

                                $field = Craft::$app->fields->getFieldByHandle($field_handle);
                                $class = $field ? get_class($field) : null;

                                if($class) {
                                    $value = $this->formatFieldIntoString($field, $element);

                                    if($class != 'craft\\fields\\Matrix'){
                                        if($element->isFieldDirty($field_handle)){
                                            $value  = '<strong>' . $this->formatFieldIntoString($field, $old_element);
                                            $value .= ' => '. $value;
                                            $value .= '</strong>';
                                        }
                                    }
                                }

                                $subject    = str_replace('{{ '. $field_handle .' }}', $value, $subject);
                                $template   = str_replace('{{ '. $field_handle .' }}', $value, $template);
                            }
                        }

                        // if($email_should_trigger) {
                            $subscription_field_handle = $email_subscription_field->handle;
                            $emails = $element->$subscription_field_handle;
                            foreach($emails as $email=>$name){
                                Craft::$app
                                    ->getMailer()
                                    ->compose()
                                    ->setTo($email)
                                    ->setSubject($subject)
                                    ->setHtmlBody($template)
                                    ->send();
                            }
                        // }
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


    // I've only formatted the fields that are currently used in the current template implementation.
    // The others should probably be pretty easy to slot in, but unfortunately without live examples
    // It's hard to get them QC'd in time.
    public function formatFieldIntoString($field, $element)
    {
        $class  = get_class($field);
        $handle = $field->handle;
        $value  = $element->$handle;

        switch($class){
            case 'craft\\fields\\Assets':
                $html = '';
                if(count($value->all())){
                    $html .="<ul>";
                    foreach($value->all() as $asset){
                        $html .="<li><a href='" . $asset->url . "'>". $asset->title ."</a></li>";
                    }
                    $html .="</ul>";
                }
                return $html;
                break;
            case 'craft\\fields\\Date':
                return $value->format('F j, Y');
                break;
            case 'craft\\fields\\Matrix':
                $html = '<ul>';
                foreach($value as $block){
                    foreach($block->fieldLayout->fields as $field) {
                        $inner_html = $this->formatFieldIntoString($field, $block);
                        if($inner_html){
                            $html .= ("<li>" . $field->name . ": ");
                            $html .= $inner_html;
                            $html .= "</li>";
                        }
                    }
                }
                $html .= "</ul>";
                return $html;
                break;
            case 'craft\\fields\\PlainText':
                return $value;
                break;
            case 'craft\\fields\\RadioButtons':
                $label = ucfirst($value);
                return $label;
                break;
            case 'craft\\fields\\Time':
                return $value->format('g:ia');
                break;
            default:
                return '';
                break;
        }
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
