<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace goat\dashboardconfiguration\widgets;

use Craft;
use craft\base\Widget;
use craft\elements\Entry;
use craft\models\Section;
use craft\web\View;

use goat\dashboardconfiguration\DashboardConfiguration;
use goat\dashboardconfiguration\assetbundles\dashboardconfigurationwidgetwidget\DashboardConfigurationWidgetWidgetAsset;

/**
 * RecentEntries represents a Recent Entries dashboard widget.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0.0
 */
class Documentation extends Widget
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('app', 'Documentation & Resources');
    }

    /**
     * @inheritdoc
     */
    public static function icon()
    {
        return Craft::getAlias('@appicons/clock.svg');
    }

    /**
     * @var int|null The string content that we want this widget to display.
     */
    public $mainContent = 'Default';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['mainContent'], 'required'];
        $rules[] = [['mainContent'], 'string'];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return Craft::t('app', 'Documentation & Resources');
    }

    /**
     * @inheritdoc
     */
    public function getBodyHtml()
    {
        $html = \goat\dashboardconfiguration\DashboardConfiguration::getInstance()->settings->documentation;

        return Craft::$app->getView()->renderTemplate(
            'dashboard-configuration/_components/widgets/Documentation_body',
            [
                'html'   => $html
            ]
        );
    }
}
