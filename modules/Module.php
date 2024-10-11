<?php
namespace modules;

use Algolia\AlgoliaSearch\SearchClient;
use Craft;
use craft\base\Element;
use craft\elements\Entry;
use craft\events\ModelEvent;
use craft\events\RegisterElementExportersEvent;
use craft\helpers\App;
use craft\helpers\ElementHelper;
use craft\helpers\StringHelper;
use Solspace\Freeform\Events\Fields\CompileFieldAttributesEvent;
use Solspace\Freeform\Fields\FieldInterface;
use Solspace\Freeform\Fields\Parameters\Parameters;
use yii\base\Event;

class Module extends \yii\base\Module
{
    /**
     * Initializes the module.
     */
    public function init(): void
    {
        // Set a @modules alias pointed to the modules/ directory
        Craft::setAlias('@modules', __DIR__);

        // Set the controllerNamespace based on whether this is a console or web request
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'modules\\console\\controllers';
        } else {
            $this->controllerNamespace = 'modules\\controllers';
        }

        parent::init();

        // Toggle Entry indexing on Algolia
        Event::on(
            Entry::class,
            Element::EVENT_AFTER_SAVE,
            function (ModelEvent $e) {
                /** @var Entry $entry */
                $entry = $e->sender;

                // Ignore if Entry is New, Draft, Revision, or if the enabled property is not being updated.
                // New Entry indexing is handled by the Scout plugin.
                if ($e->isNew || ElementHelper::isDraftOrRevision($entry) || !$entry->isAttributeDirty('enabled')) {
                    return;
                }

                $index = Craft::$container->get(SearchClient::class)
                    ->initIndex(App::env('CRAFT_ENVIRONMENT') . '_' . $entry->getSection()->handle);

                if ($entry->getStatus() === Element::STATUS_DISABLED) {
                    $index->deleteObject($entry->id);
                } else {
                    $index->saveObject(['objectID' => $entry->id]);
                }
            }
        );

        // Make Feedback forms IDs unique and accessible for screen readers
        Event::on(
            FieldInterface::class,
            FieldInterface::EVENT_COMPILE_ATTRIBUTES,
            function (CompileFieldAttributesEvent $event) {
                $field = $event->getField();
                $handle = StringHelper::dasherize($field->getForm()->getHandle()) . '-' . $field->getHandle();
                if ($field->getHandle() === 'message') {
                    $field->setParameters(['parameters' => (new Parameters())->add('id', $handle)]);
                }
            }
        );

        // Remove "All Entries" source
        Event::on(
            Entry::class,
            Element::EVENT_REGISTER_SOURCES,
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
    }
}
