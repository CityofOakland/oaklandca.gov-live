<?php
namespace goat\dashboardconfiguration\services;

use Craft;
use craft\base\Component;
use craft\base\WidgetInterface;
use craft\db\Query;
use craft\db\Table;

use craft\helpers\Component as ComponentHelper;
use craft\records\Widget as WidgetRecord;
use craft\widgets\MissingWidget;

use goat\dashboardconfiguration\widgets\WebsiteUpdates as WebsiteUpdatesWidget;
use goat\dashboardconfiguration\widgets\Documentation as DocumentationWidget;

use yii\web\UserEvent;

class Service extends Component
{
    // Public Methods
    // =========================================================================

    public function visitDashboard()
    {
        $dashboard = Craft::$app->dashboard;

        $user = Craft::$app->getUser()->getIdentity();

        if (!$user) {
            throw new Exception('No logged-in user');
        }

        if (!$user->hasDashboard) {
            return false;
        }

        $results = (new Query())->select([
            'id',
            'dateCreated',
            'dateUpdated',
            'colspan',
            'type',
            'settings',
        ])
        ->from([Table::WIDGETS])
        ->where(['userId' => $user->id])
        ->orderBy(['sortOrder' => SORT_ASC])
        ->all();


        foreach ($results as $result) {
            if($widgetId = $result['id']){
                $transaction = Craft::$app->getDb()->beginTransaction();
                try {
                    $widgetRecord = WidgetRecord::findOne([
                        'id' => $widgetId,
                        'userId' => $user->id,
                    ]);
                    $widgetRecord->delete();
                    $transaction->commit();
                } catch (\Throwable $e) {
                    $transaction->rollBack();

                    throw $e;
                }
            }
        }

        $documentationWidget    = $this->createWidget(DocumentationWidget::class);
        $websiteUpdatesWidget   = $this->createWidget(WebsiteUpdatesWidget::class);

        $dashboard->saveWidget($documentationWidget);
        $dashboard->saveWidget($websiteUpdatesWidget);
    }

    public function createWidget($config): WidgetInterface
    {
        if (is_string($config)) {
            $config = ['type' => $config];
        }

        try {
            $widget = ComponentHelper::createComponent($config, WidgetInterface::class);
        } catch (MissingComponentException $e) {
            $config['errorMessage'] = $e->getMessage();
            $config['expectedType'] = $config['type'];
            unset($config['type']);

            $widget = new MissingWidget($config);
        }

        return $widget;
    }
}