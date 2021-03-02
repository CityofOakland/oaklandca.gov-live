<?php
namespace modules;

use Solspace\Calendar\Elements\Event;
use League\Fractal\TransformerAbstract;
use modules\ScoutModule;

use Craft;

/**
 * Custom module to help parse information sent/received from the Algolia Scout plugin.
 */
class EventTransform extends TransformerAbstract
{
  public function transform(Event $event)
  	{
    	return [
      		'title' => $event->title,
      		'url' => $event->uri,
      		'calendar' => $event->calendar->handle,
      		'date' => $event->startDate->timestamp * 1000,
      		'displayDate' => $event->startDate->format('F j, Y'),
      		'body' => ScoutModule::richTextSplit($event->body),
      		'contact' => $event->eventContact,
      		'eventImage' => $event->eventImage->one()->url ?? null,
      		'boardsCommissions' => ScoutModule::enumEntries("boardsCommissions", $event),
      		'departments' => ScoutModule::enumEntries("departments", $event),
      		'officials' => ScoutModule::enumEntries("officials", $event),
      		'projects' => ScoutModule::enumEntries("projects", $event),
      		'topics' => ScoutModule::enumEntries("topics", $event),
    	];
  	}
}