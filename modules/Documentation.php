<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace modules;

use Craft;
use craft\base\Widget;
use craft\elements\Entry;
use craft\models\Section;
use craft\web\View;

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
    public $mainContent = '';

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
        $view = Craft::$app->getView();

        return $view->renderTemplate(
            '_components/widgets/Documentation/body',
            ['widget' => $this],
            View::TEMPLATE_MODE_SITE);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('_components/widgets/Documentation/settings',
            [
                'widget' => $this,
            ],
            View::TEMPLATE_MODE_SITE);
    }
}
