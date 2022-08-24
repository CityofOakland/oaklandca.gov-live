<?php
/**
 * Oakland Library Events module for Craft CMS 3.x
 *
 * This module grabs BiblioCommons Library Events and provides Twig Extensions to insert these events into your template queries.
 *
 * @link      https://meetgoat.com
 * @copyright Copyright (c) 2022 Goat Creative Inc.
 */

namespace modules\oaklandlibraryeventsmodule\assetbundles\oaklandlibraryeventsmodule;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Goat Creative Inc.
 * @package   OaklandLibraryEventsModule
 * @since     1.0.0
 */
class OaklandLibraryEventsModuleAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@modules/oaklandlibraryeventsmodule/assetbundles/oaklandlibraryeventsmodule/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/OaklandLibraryEventsModule.js',
        ];

        $this->css = [
            'css/OaklandLibraryEventsModule.css',
        ];

        parent::init();
    }
}
