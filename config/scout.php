<?php

use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use modules\AllTransform;
use modules\ScoutModule;
use rias\scout\ScoutIndex;


return [
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
	 * By default Scout will index elements related to the element being save (that are in the same index).
	 * Disabling this can improve performance on larger sites that have lots of relations.
	 */
	'indexRelations' => false,

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
		ScoutIndex::create(getenv('ENVIRONMENT') . '_news')
// Scout uses this by default, so this is optional
			->elementType(Entry::class)
// If you don't define a siteId, the primary site is used
			->criteria(function (EntryQuery $query) {
				return $query
					->section(['news'])
					->with(['boardsCommissions', 'departments', 'officials', 'projects', 'resources', 'services', 'topics', 'documentType', 'documents']);
			})
			->splitElementsOn(['summary', 'body'])
			->transformer(function (craft\elements\Entry $entry) {
				return [
					'title' => $entry->title,
					'url' => ScoutModule::entryUrl($entry),
					'date' => ScoutModule::entryDate($entry),
					'displayDate' => ScoutModule::entryPrettyDate($entry),
					'newsImage' => $entry->newsImage->one()->url ?? null,
					'summary' => ScoutModule::richTextSplit($entry->summary),
					'body' => ScoutModule::richTextSplit($entry->body),
					'mediaContact' => $entry->mediaContact,
					'boardsCommissions' => ScoutModule::enumEntries("boardsCommissions", $entry),
					'departments' => ScoutModule::enumEntries("departments", $entry),
					'projects' => ScoutModule::enumEntries("projects", $entry),
					'officials' => ScoutModule::enumEntries("officials", $entry),
					'topics' => ScoutModule::enumEntries("topics", $entry),
				];
			}),

// BEGIN DOCUMENTS INDEX
		ScoutIndex::create(getenv('ENVIRONMENT') . '_documents')
			->criteria(function (EntryQuery $query) {
				return $query
					->section(['documents', 'documentPackets'])
					->with(['boardsCommissions', 'departments', 'officials', 'projects', 'resources', 'services', 'topics', 'documentType', 'documents']);
			})
			->splitElementsOn(['summary', 'body'])
			->transformer(function (craft\elements\Entry $entry) {
				$summary = ScoutModule::richTextSplit($entry->summary);
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
					'url' => ScoutModule::entryUrl($entry),
					'date' => ScoutModule::entryDate($entry),
					'displayDate' => ScoutModule::entryPrettyDate($entry),
					'leadIn' => $entry->leadIn,
					'summary' => $summary,
					'categories' => $types,
					'boardsCommissions' => ScoutModule::enumEntries("boardsCommissions", $entry),
					'departments' => ScoutModule::enumEntries("departments", $entry),
					'officials' => ScoutModule::enumEntries("officials", $entry),
					'projects' => ScoutModule::enumEntries("projects", $entry),
					'resources' => ScoutModule::enumEntries("resources", $entry),
					'services' => ScoutModule::enumEntries("services", $entry),
					'documents' => ScoutModule::enumEntries("documents", $entry),
					'topics' => ScoutModule::enumEntries("topics", $entry),
					'viewCount' => $entry->viewCount,
				];
			}),

// BEGIN STAFF INDEX
		ScoutIndex::create(getenv('ENVIRONMENT') . '_staff')
			->criteria(function (EntryQuery $query) {
				return $query
					->section('staff')
					->with(['department']);
			})
			->transformer(function (craft\elements\Entry $entry) {
				return [
					'title' => $entry->title,
					'url' => ScoutModule::entryUrl($entry),
					'date' => ScoutModule::entryDate($entry),
					'displayDate' => ScoutModule::entryPrettyDate($entry),
					'portrait' => ScoutModule::portrait($entry),
					'jobTitle' => !empty($entry->jobTitle) ? $entry->jobTitle : $entry->staffImportJobTitle,
					'bio' => strip_tags($entry->bio),
					'phone' => !empty($entry->phoneNumber) ? $entry->phoneNumber : $entry->staffImportPhoneNumber,
					'email' => !empty($entry->emailAddress) ? $entry->emailAddress : str_replace("@oaklandnet.com", "@oaklandca.gov", $entry->staffImportEmail),
					'department' => !empty($entry->departments[0]) ? $entry->departments[0]->title : $entry->staffImportOrganization ?? $entry->staffImportDepartment,
					'employmentType' => $entry->employmentType,
					'viewCount' => $entry->viewCount,
				];
			}),

// BEGIN VOLUNTEERS INDEX
		ScoutIndex::create(getenv('ENVIRONMENT') . '_volunteers')
			->criteria(function (EntryQuery $query) {
				return $query
					->section('volunteers')
					->with(['portrait', 'department']);
			})
			->transformer(function (craft\elements\Entry $entry) {
				return [
					'title' => $entry->title,
					'url' => ScoutModule::entryUrl($entry),
					'date' => ScoutModule::entryDate($entry),
					'displayDate' => ScoutModule::entryPrettyDate($entry),
					'jobTitle' => !empty($entry->jobTitle) ? $entry->jobTitle : null,
					'bio' => $entry->bio,
					'email' => !empty($entry->emailAddress) ? $entry->emailAddress : null,
					'department' => !empty($entry->departmentOfficialBoardCommission->one()) ? $entry->departmentOfficialBoardCommission->one()->title : null,
					'viewCount' => $entry->viewCount,
				];
			}),

// BEGIN TEAMS INDEX
		ScoutIndex::create(getenv('ENVIRONMENT') . '_teams')
			->criteria(function (EntryQuery $query) {
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
					'url' => ScoutModule::entryUrl($entry),
					'date' => ScoutModule::entryDate($entry),
					'displayDate' => ScoutModule::entryPrettyDate($entry),
					'teamMembers' => $teamMembers,
					'viewCount' => $entry->viewCount,
				];
			}),

// BEGIN ALL INDEX
		ScoutIndex::create(getenv('ENVIRONMENT') . '_all')
			->elementType(Entry::class)
			->criteria(function (EntryQuery $query) {
				return $query
					->section([
						'boardsCommissions',
						'departments',
						'documents',
						'documentPackets',
						'events',
						'meetings',
						'news',
						'officials',
						'processes',
						'projects',
						'resources',
						'services',
						'staff',
						'teams',
						'topics',
						'volunteers'
					]);
			})
			->splitElementsOn(['summary', 'body', 'about'])
			->transformer(new AllTransform())
	]
];
