<?php
namespace modules;

use craft\elements\Entry;
use League\Fractal\TransformerAbstract;
use modules\ScoutModule;

use Craft;

/**
 * Custom module to help parse information sent/received from the Algolia Scout plugin.
 *
 * This module is used as a generic template for all entry types.
 * 
 */

class AllTransform extends TransformerAbstract
{
    public function transform(Entry $entry)
    {

        if($entry->isDraft){
            return [];
        }

        return [
            'about'             => ScoutModule::richTextSplit($entry->about),
            'bio'               => strip_tags($entry->bio),
            'body'              => !empty($entry->contentBuilder) ? ScoutModule::contentBuilder($entry->contentBuilder) : ScoutModule::richTextSplit($entry->body),
            'ctaButtonText'     => ScoutModule::ctaButtonText($entry),
            'date'              => ScoutModule::entryDate($entry),
            'dateUpdated'       => $entry->dateUpdated->format('F j, Y'),
            'displayDate'       => ScoutModule::entryPrettyDate($entry),
            'fileType'          => isset($entry->documentFile) && $entry->documentFile->one() ? $entry->documentFile->one()->url : null,
            'groupHeadBio'      => strip_tags($entry->groupHeadBio),
            'groupHeadName'     => $entry->groupHeadName,
            'groupHeadTitle'    => $entry->groupHeadTitle,
            'leadIn'            => $entry->leadIn,
            'mediaContact'      => $entry->mediaContact,
            'priority'          => isset($entry->priority) ? ($entry->priority ? $entry->priority : 100) : 100,
            'section'           => $entry->section->name,
            'sectionPriority'   => ScoutModule::sectionPriority($entry->section->handle),
            'summary'           => ScoutModule::richTextSplit($entry->summary),
            'title'             => $entry->title,
            'type'              => $entry->type->handle,
            'url'               => ScoutModule::entryUrl($entry),
            'viewCount'         => $entry->viewCount,
        ];
    }
}
