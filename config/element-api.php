<?php

use craft\elements\Entry;
use craft\helpers\UrlHelper;

function cbEntries($section, $element)
{
  $sectionArray = [];
  if (!empty($element->$section)) {
    foreach ($element->$section as $value) {
      $sectionArray[] = $value->id;
    }
  }
  return $sectionArray;
}

return [
  'endpoints' => [
    'events.json' => function () {
      return [
	      'elementType' => Entry::class,
		      'criteria' => [
			      'section' => 'events',
		      ],
		      'transformer' => function (Entry $entry) {
						// create an array of dates by iterating over each calendarize->getOccurrences
						// and appending the date to the array
						$dates = [];
						foreach ($entry->calendarize->getOccurrences(null) as $occurrence) {
							$dates[] = $occurrence->next();
						}

			      return [
            'title' => $entry->title,
            'id' => $entry->id,
			      'dates' => $dates,
          ];
        },
      ];
    },
  ]
];
