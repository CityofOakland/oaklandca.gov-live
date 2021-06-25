<?php
namespace modules;

use Craft;

use craft\base\Element;
use craft\elements\Entry;
use craft\events\RegisterElementExportersEvent;
use yii\base\Event;

use modules\ExportModule;

class Module extends \yii\base\Module
{
    /**
     * Initializes the module.
     */
    public function init()
    {
        // Set a @modules alias pointed to the modules/ directory
        Craft::setAlias('@modules', __DIR__);

        // Set the controllerNamespace based on whether this is a console or web request
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'modules\\console\\controllers';
        } else {
            $this->controllerNamespace = 'modules\\controllers';
        }

        Event::on(
            Entry::class,
            Element::EVENT_REGISTER_EXPORTERS,
            function(RegisterElementExportersEvent $event) {
                $event->exporters[] = ExportModule::class;
            }
        );

        parent::init();
    }
}
