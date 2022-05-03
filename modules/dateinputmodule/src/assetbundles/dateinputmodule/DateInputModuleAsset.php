<?php
/**
 * Date Input module for Craft CMS 3.x
 *
 * Quick plugin for generating easy date inputs.
 *
 * @link      https://meetgoat.com
 * @copyright Copyright (c) 2022 meetgoat
 */

namespace modules\dateinputmodule\assetbundles\dateinputmodule;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * DateInputModuleAsset AssetBundle
 *
 * AssetBundle represents a collection of asset files, such as CSS, JS, images.
 *
 * Each asset bundle has a unique name that globally identifies it among all asset bundles used in an application.
 * The name is the [fully qualified class name](http://php.net/manual/en/language.namespaces.rules.php)
 * of the class representing it.
 *
 * An asset bundle can depend on other asset bundles. When registering an asset bundle
 * with a view, all its dependent asset bundles will be automatically registered.
 *
 * http://www.yiiframework.com/doc-2.0/guide-structure-assets.html
 *
 * @author    meetgoat
 * @package   DateInputModule
 * @since     1.0.0
 */
class DateInputModuleAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = "@modules/dateinputmodule/assetbundles/dateinputmodule/dist";

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'js/jquery-1.12.1.min.js',
            'js/jquery-ui.min.js',
            'js/MonthPicker.min.js',
            'js/DateInputModule.js',
        ];

        $this->css = [
            'css/jquery-ui.min.css',
            'css/MonthPicker.min.css',
            'css/DateInputModule.css',
        ];

        parent::init();
    }
}
