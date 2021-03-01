<?php

use craft\elements\Entry;
use craft\helpers\UrlHelper;
use Solspace\Calendar\Elements\Event;
use fruitstudios\linkit\Linkit;
use League\Fractal\TransformerAbstract;
use nystudio107\imageoptimize\imagetransforms\ImageTransform;
use Solspace\Calendar\Elements\Db\EventQuery;

function enumEntries($section, $element)
{
  $sectionArray = [];
  if (!empty($element->$section)) {
    foreach ($element->$section as $value) {
      $sectionArray[] = $value->title;
    }
  }
  return $sectionArray;
}

function entryUrl($entry)
{
  return empty($entry->redirectUrl) ? $entry->url : $entry->redirectUrl;
}

function entryDate($entry)
{
  return $entry->postDate->getTimestamp() * 1000;
}

function entryPrettyDate($entry)
{
  return $entry->postDate->format('F j, Y');
}

function ctaButtonText($element)
{
  return !empty($element->ctaButton->text) ? $element->ctaButton->text : null;
}

function banner($element)
{
  if (!empty($element->banner)) {
    if (!empty($element->banner->one())) {
      return $element->banner->one()->url;
    }
  }
  return null;
}

function portrait($element)
{
  if (!empty($element->banner)) {
    if (!empty($element->banner->one())) {
      return $element->banner->one()->url;
    }
  }
  return null;
}

function contentBuilder($element)
{
  $body = [];
  if (!empty($element)) {
    foreach ($element->all() as $block) {
      switch ($block->type) {
        case 'heading':
        case 'subheading':
        case 'text':
          $body[] = strip_tags($block->text);
          break;
        case 'textImageBlock':
          $body[] = strip_tags($block->textBlock);
          break;
        case 'linksWithDescriptions':
          $body[] = strip_tags($block->linkDescription);
          break;
        case 'largeEntryLinks':
        case 'smallEntryLinks':
          $body[] = strip_tags($block->entriesDescription);
          break;
        default:
      }
    }
  }
  return $body;
}

function richTextSplit($field)
{
  if (!empty($field)) {
    $array = [];
    $str = strip_tags($field);
    $ps = preg_split('~((?:\S*?\s){100})~', $str, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    foreach ($ps as $node) {
      $array[] = $node;
    }
    return $array;
  }
}

class eventsTransform extends TransformerAbstract
{
  public function transform(Solspace\Calendar\Elements\Event $event)
  {
    return [
      'title' => $event->title,
      'url' => $event->uri,
      'calendar' => $event->calendar->handle,
      'date' => $event->startDate->timestamp * 1000,
      'displayDate' => $event->startDate->format('F j, Y'),
      'body' => richTextSplit($event->body),
      'contact' => $event->eventContact,
      'eventImage' => $event->eventImage->one()->url ?? null,
      'boardsCommissions' => enumEntries("boardsCommissions", $event),
      'departments' => enumEntries("departments", $event),
      'officials' => enumEntries("officials", $event),
      'projects' => enumEntries("projects", $event),
      'topics' => enumEntries("topics", $event),
    ];
  }
}

class allTransform extends TransformerAbstract
{
  public function transform(Entry $entry)
  {
    return [
      'title' => $entry->title,
      'url' => entryUrl($entry),
      'section' => $entry->section->name,
      'type' => $entry->type->handle,
      'date' => entryDate($entry),
      'displayDate' => entryPrettyDate($entry),
      'summary' => richTextSplit($entry->summary),
      'leadIn' => $entry->leadIn,
      'about' => richTextSplit($entry->about),
      'bio' => strip_tags($entry->bio),
      'body' => !empty($entry->contentBuilder) ? contentBuilder($entry->contentBuilder) : richTextSplit($entry->body),
      'ctaButtonText' => ctaButtonText($entry),
      'groupHeadBio' => strip_tags($entry->groupHeadBio),
      'groupHeadName' => $entry->groupHeadName,
      'groupHeadTitle' => $entry->groupHeadTitle,
      'mediaContact' => $entry->mediaContact,
      'viewCount' => $entry->viewCount,
    ];
  }
}

return [
  "sync" => true,
  "application_id" => getenv('ALGOLIA_APP_ID'),
  "admin_api_key" => getenv('ALGOLIA_ADMIN_API'),
  /*
     * Scout listens to numerous Element events to keep them updated in
     * their respective indices. You can disable these and update
     * your indices manually using the commands.
     */
  'sync' => true,

  /*
     * By default Scout handles all indexing in a queued job, you can disable
     * this so the indices are updated as soon as the elements are updated
     */
  'queue' => true,

  /*
     * The connection timeout (in seconds), increase this only if necessary
     */
  'connect_timeout' => 1,

  /*
     * The batch size Scout uses when importing a large amount of elements
     */
  'batch_size' => 1000,

  /*
     * The Algolia Application ID, this id can be found in your Algolia Account
     * https://www.algolia.com/api-keys. This id is used to update records.
     */
  'application_id' => getenv('ALGOLIA_APP_ID'),

  /*
     * The Algolia Admin API key, this key can be found in your Algolia Account
     * https://www.algolia.com/api-keys. This key is used to update records.
     */
  'admin_api_key'  => getenv('ALGOLIA_ADMIN_API'),

  /*
     * The Algolia search API key, this key can be found in your Algolia Account
     * https://www.algolia.com/api-keys. This search key is not used in Scout
     * but can be used through the Scout variable in your template files.
     */
  'search_api_key' => getenv('ALGOLIA_SEARCH_API'), //optional

  /*
     * A collection of indices that Scout should sync to, these can be configured
     * by using the \rias\scout\ScoutIndex::create('IndexName') command. Each
     * index should define an ElementType, criteria and a transformer.
     */
  'indices' => [

    // BEGIN NEWS INDEX
    \rias\scout\ScoutIndex::create(getenv('ENVIRONMENT') . '_news')
      // Scout uses this by default, so this is optional
      ->elementType(\craft\elements\Entry::class)
      // If you don't define a siteId, the primary site is used
      ->criteria(function (\craft\elements\db\EntryQuery $query) {
        return $query
          ->section(['news'])
          ->with(['boardsCommissions', 'departments', 'officials', 'projects', 'resources', 'services', 'topics', 'documentType', 'documents']);
      })
      ->splitElementsOn(['summary', 'body'])
      ->transformer(function (craft\elements\Entry $entry) {
        return [
          'title' => $entry->title,
          'url' => entryUrl($entry),
          'date' => entryDate($entry),
          'displayDate' => entryPrettyDate($entry),
          'newsImage' => $entry->newsImage->one()->url ?? null,
          'summary' => richTextSplit($entry->summary),
          'body' => richTextSplit($entry->body),
          'mediaContact' => $entry->mediaContact,
          'boardsCommissions' => enumEntries("boardsCommissions", $entry),
          'departments' => enumEntries("departments", $entry),
          'projects' => enumEntries("projects", $entry),
          'officials' => enumEntries("officials", $entry),
          'topics' => enumEntries("topics", $entry),
        ];
      }),

    // BEGIN DOCUMENTS INDEX
    \rias\scout\ScoutIndex::create(getenv('ENVIRONMENT') . '_documents')
      ->criteria(function (\craft\elements\db\EntryQuery $query) {
        return $query
          ->section(['documents', 'documentPackets'])
          ->with(['boardsCommissions', 'departments', 'officials', 'projects', 'resources', 'services', 'topics', 'documentType', 'documents']);
      })
      ->splitElementsOn(['summary', 'body'])
      ->transformer(function (craft\elements\Entry $entry) {
        $summary = richTextSplit($entry->summary);
        $types = [];
        if (!empty($entry->documentType)) {
          foreach ($entry->documentType as $value) {
            $types[] = $value->title;
          }
        }
        return [
          'title' => $entry->title,
          'section' => $entry->section->name,
          'type' => $entry->type->handle,
          'url' => entryUrl($entry),
          'date' => entryDate($entry),
          'displayDate' => entryPrettyDate($entry),
          'leadIn' => $entry->leadIn,
          'summary' => $summary,
          'categories' => $types,
          'boardsCommissions' => enumEntries("boardsCommissions", $entry),
          'departments' => enumEntries("departments", $entry),
          'officials' => enumEntries("officials", $entry),
          'projects' => enumEntries("projects", $entry),
          'resources' => enumEntries("resources", $entry),
          'services' => enumEntries("services", $entry),
          'documents' => enumEntries("documents", $entry),
          'topics' => enumEntries("topics", $entry),
          'viewCount' => $entry->viewCount,
        ];
      }),

    // BEGIN STAFF INDEX
    \rias\scout\ScoutIndex::create(getenv('ENVIRONMENT') . '_staff')
      ->criteria(function (\craft\elements\db\EntryQuery $query) {
        return $query
          ->section('staff')
          ->with(['portrait', 'department']);
      })
      ->transformer(function (craft\elements\Entry $entry) {
        return [
          'title' => $entry->title,
          'url' => entryUrl($entry),
          'date' => entryDate($entry),
          'displayDate' => entryPrettyDate($entry),
          'portrait' => portrait($entry),
          'jobTitle' => !empty($entry->jobTitle) ? $entry->jobTitle : $entry->staffImportJobTitle,
          'bio' => strip_tags($entry->bio),
          'phone' => !empty($entry->phoneNumber) ? $entry->phoneNumber : $entry->staffImportPhoneNumber,
          'email' => !empty($entry->emailAddress) ? $entry->emailAddress : str_replace("@oaklandnet.com", "@oaklandca.gov", $entry->staffImportEmail),
          'department' => !empty($entry->departments[0]) ? $entry->departments[0]->title : $entry->staffImportOrganization ?? $entry->staffImportDepartment,
          'employmentType' => $entry->employmentType->label,
          'viewCount' => $entry->viewCount,
        ];
      }),

    // BEGIN VOLUNTEERS INDEX
    \rias\scout\ScoutIndex::create(getenv('ENVIRONMENT') . '_volunteers')
      ->criteria(function (\craft\elements\db\EntryQuery $query) {
        return $query
          ->section('volunteers')
          ->with(['portrait', 'department']);
      })
      ->transformer(function (craft\elements\Entry $entry) {
        return [
          'title' => $entry->title,
          'url' => entryUrl($entry),
          'date' => entryDate($entry),
          'displayDate' => entryPrettyDate($entry),
          'portrait' => portrait($entry),
          'jobTitle' => !empty($entry->jobTitle) ? $entry->jobTitle : null,
          'bio' => $entry->bio,
          'email' => !empty($entry->emailAddress) ? $entry->emailAddress : null,
          'department' => !empty($entry->departmentOfficialBoardCommission->one()) ? $entry->departmentOfficialBoardCommission->one()->title : null,
          'viewCount' => $entry->viewCount,
        ];
      }),

    // BEGIN TEAMS INDEX
    \rias\scout\ScoutIndex::create(getenv('ENVIRONMENT') . '_teams')
      ->criteria(function (\craft\elements\db\EntryQuery $query) {
        return $query
          ->section('teams')
          ->with(['teamMembers']);
      })
      ->transformer(function (craft\elements\Entry $entry) {
        $teamMembers = [];
        foreach ($entry->teamMembers as $value)
          foreach ($value->staff as $teamMember)
            $teamMembers[] = $teamMember->title;
        return [
          'title' => $entry->title,
          'url' => entryUrl($entry),
          'date' => entryDate($entry),
          'displayDate' => entryPrettyDate($entry),
          'teamMembers' => $teamMembers,
          'viewCount' => $entry->viewCount,
        ];
      }),

    // BEGIN CALENDAR INDEX
    \rias\scout\ScoutIndex::create(getenv('ENVIRONMENT') . '_calendars')
      ->elementType(\Solspace\Calendar\Elements\Event::class)
      ->criteria(function (Solspace\Calendar\Elements\Db\EventQuery $query) {
        return $query
          ->with(['boardsCommissions', 'departments', 'officials', 'projects', 'topics']);
      })
      ->splitElementsOn(['summary', 'body'])
      ->transformer(new eventsTransform()),

    // BEGIN EVENTS INDEX
    \rias\scout\ScoutIndex::create(getenv('ENVIRONMENT') . '_events')
      ->elementType(\Solspace\Calendar\Elements\Event::class)
      ->criteria(function (Solspace\Calendar\Elements\Db\EventQuery $query) {
        return $query
          ->setCalendar('events')
          ->with(['boardsCommissions', 'departments', 'officials', 'projects', 'topics']);
      })
      ->splitElementsOn(['summary', 'body'])
      ->transformer(new eventsTransform()),

    // BEGIN MEETINGS INDEX
    \rias\scout\ScoutIndex::create(getenv('ENVIRONMENT') . '_meetings')
      ->elementType(\Solspace\Calendar\Elements\Event::class)
      ->criteria(function (Solspace\Calendar\Elements\Db\EventQuery $query) {
        return $query
          ->setCalendar('meetings')
          ->with(['boardsCommissions', 'departments', 'officials', 'projects', 'topics']);
      })
      ->splitElementsOn(['summary', 'body'])
      ->transformer(new eventsTransform()),

    // BEGIN ALL INDEX
    \rias\scout\ScoutIndex::create(getenv('ENVIRONMENT') . '_all')
      ->elementType(\craft\elements\Entry::class)
      ->criteria(function (\craft\elements\db\EntryQuery $query) {
        return $query
          ->section(['boardsCommissions', 'departments', 'news',  'officials', 'processes', 'projects', 'resources', 'services', 'staff', 'teams', 'topics', 'volunteers', 'documents']);
      })
      ->splitElementsOn(['summary', 'body', 'about'])
      ->transformer(new allTransform())
  ]
];
