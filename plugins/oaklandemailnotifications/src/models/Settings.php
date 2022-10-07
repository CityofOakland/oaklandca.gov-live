<?php
/**
 * Oakland Email Notifications plugin for Craft CMS 3.x
 *
 * Generate email notifications for various areas of the site including the Meetings entry type.
 *
 * @link      https://meetgoat.com
 * @copyright Copyright (c) 2022 Goat Creative Inc.
 */

namespace meetgoat\oaklandemailnotifications\models;

use meetgoat\oaklandemailnotifications\OaklandEmailNotifications;

use Craft;
use craft\base\Model;

/**
 * OaklandEmailNotifications Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Goat Creative Inc.
 * @package   OaklandEmailNotifications
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Pre-Merge Email Templates
     *
     * @var array
     */
    public $emailTemplates  = [];

    /**
     * Pre-Merge Email Subjects
     *
     * @var array
     */
    public $emailSubjects   = [];

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}