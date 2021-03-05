<?php
namespace modules;

use craft\elements\Entry;
use League\Fractal\TransformerAbstract;
use modules\ScoutModule;

use Craft;

/**
 * Custom module to help parse information sent/received from the Algolia Scout plugin.
 */
class AllTransform extends TransformerAbstract
{
    public function transform(Entry $entry)
    {
        return [
            'title' => $entry->title,
            'url' => ScoutModule::entryUrl($entry),
            'section' => $entry->section->name,
            'type' => $entry->type->handle,
            'date' => ScoutModule::entryDate($entry),
            'displayDate' => ScoutModule::entryPrettyDate($entry),
            'summary' => ScoutModule::richTextSplit($entry->summary),
            'leadIn' => $entry->leadIn,
            'about' => ScoutModule::richTextSplit($entry->about),
            'bio' => strip_tags($entry->bio),
            'body' => !empty($entry->contentBuilder) ? ScoutModule::contentBuilder($entry->contentBuilder) : ScoutModule::richTextSplit($entry->body),
            'ctaButtonText' => ScoutModule::ctaButtonText($entry),
            'groupHeadBio' => strip_tags($entry->groupHeadBio),
            'groupHeadName' => $entry->groupHeadName,
            'groupHeadTitle' => $entry->groupHeadTitle,
            'mediaContact' => $entry->mediaContact,
            'viewCount' => $entry->viewCount,
        ];
    }
}
