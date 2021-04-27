<?php
/**
 * Dashboard Configuration plugin for Craft CMS 3.x
 *
 * Custom dashboard configuration for Craft CMS. Built for City of Oakland.
 *
 * @link      https://meetgoat.com
 * @copyright Copyright (c) 2021 Goat Creative Inc.
 */

namespace goat\dashboardconfiguration\models;

use goat\dashboardconfiguration\DashboardConfiguration;

use Craft;
use craft\base\Model;

/**
 * DashboardConfiguration Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Goat Creative Inc.
 * @package   DashboardConfiguration
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some field model attribute
     *
     * @var string
     */
    public $documentation = '';

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
        return [
            ['documentation', 'string'],
            ['documentation', 'default', 'value' => ''],
        ];
    }
}
