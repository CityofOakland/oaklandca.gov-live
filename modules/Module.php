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

        // Remove "All Entries" source
        Event::on(
            Entry::class,
            Entry::EVENT_REGISTER_SOURCES,
            function (Event $event) {
                array_shift($event->sources);
            }
        );

        Event::on(
            Entry::class,
            Element::EVENT_REGISTER_EXPORTERS,
            function(RegisterElementExportersEvent $event) {
                $event->exporters[] = ExportModule::class;
            }
        );

        Craft::$app->view->hook('cp.entries.edit.details', function(&$context) {
          $entry = $context['entry'];
          if($entry->section->handle == 'services'){
            $user = Craft::$app->getUser();
            if(!$user->isAdmin) {
                return "<style>
                    #entryType > option[value='72'],
                    #entryType > option[value='92'] {
                        display: none;
                    }
                </style>";
            }
          }
        });

        Craft::$app->view->hook('cp.entries.edit.details', function(&$context) {
          $entry = $context['entry'];
          if($entry->section->handle == 'meetings'){
            $user = Craft::$app->getUser();
            if(!$user->isAdmin) {
                return "<style>
                    #fields-accessibilityText-field {
                      display: none;
                    }
                </style>";
            }
          }
        });

        Craft::$app->view->hook('cp.entries.edit.details', function(&$context) {
          $entry = $context['entry'];
          if($entry->section->handle == 'boardsCommissions'){
            $user = Craft::$app->getUser();
            if(!$user->isAdmin) {
                return "<style>
                    #fields-indexToggle-field {
                        display: none !important;
                    }
                </style>";
            }
          }
        });


        Craft::$app->view->hook('cp.entries.edit.content', function(&$context) {
          $entry = $context['entry'];
          if($entry->section->handle == 'meetings'){
            return "<style>
              #fields-meetingId-field {
                display: none;
              }

              #fields-process .matrix-field .btn:nth-child(2) {
                border-top-left-radius: 5px;
                border-bottom-left-radius: 5px;
              }

              #fields-process .matrix-field .btn:nth-child(3) {
                border-right: 1px dashed rgba(81, 95, 108, 0.25);
                border-top-right-radius: 5px;
                border-bottom-right-radius: 5px;
              }

              #fields-process .matrix-field .btn:nth-child(1),
              #fields-process .matrix-field .btn:nth-child(4) {
                display: none;
              }
            </style>";
          }
        });

        Craft::$app->view->hook('cp.entries.edit.content', function(&$context) {
          $entry = $context['entry'];
          if($entry->section->handle == 'meetings'){
            $user = Craft::$app->getUser();
            return "<style>
              #tab-relationships,
              #tab-tab-relationships {
                display: none;
              }
            </style>";
          }
        });

        Craft::$app->view->hook('cp.entries.edit.content', function(&$context) {
          $entry = $context['entry'];
          if($entry->section->handle == 'events'){
            $user = Craft::$app->getUser();
            if(!$user->isAdmin) {
              return "<style>
                #tab-hidden,
                #tab-tab-hidden {
                  display: none;
                }
              </style>";
            }
          }
        });

        parent::init();
    }
}
