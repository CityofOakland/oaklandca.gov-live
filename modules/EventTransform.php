<?php
namespace modules;

use League\Fractal\TransformerAbstract;
use Solspace\Calendar\Elements\Event;

/**
 * Custom module to help parse information sent/received from the Algolia Scout plugin.
 */
class EventTransform extends TransformerAbstract
{
  public function transform(Event $event): array
  {
    	return [
            'url' => $event->uri,
            'title' => $event->title,
            'contact' => $event->eventContact,
            'calendar' => $event->calendar->handle,
            'date' => $event->startDate->timestamp * 1000,
            'body' => ScoutModule::richTextSplit($event->body),
            'displayDate' => $event->startDate->format('F j, Y'),
            'eventImage' => $event->eventImage->one()->url ?? null,
            'topics' => ScoutModule::enumEntries('topics', $event),
            'projects' => ScoutModule::enumEntries('projects', $event),
            'officials' => ScoutModule::enumEntries('officials', $event),
            'departments' => ScoutModule::enumEntries('departments', $event),
            'boardsCommissions' => ScoutModule::enumEntries('boardsCommissions', $event),
        ];
  	}
}
