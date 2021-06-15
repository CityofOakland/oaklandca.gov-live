<?php

namespace modules;

use Craft;
use craft\base\EagerLoadingFieldInterface;
use craft\base\Element;
use craft\base\ElementExporter;
use craft\base\Field;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\ArrayHelper;

// Title
// Primary Unit
// Service Type
// Departments
// Officials
// Topics
// URL

class ExportModule extends ElementExporter
{
    public static function displayName(): string
    {
        return 'Custom Oakland Exporter';
    }

    public function export(ElementQueryInterface $query): array
    {
        $results = [];

        // Eager-load the entries related via the relatedEntries field
        /** @var ElementQuery $query */
        $query->with(['relatedEntries']);

        foreach ($query->each() as $element) {

            $primaryUnitArray = [];

            if($element->primaryUnit){
                foreach($element->primaryUnit->all() as $entry){
                    $primaryUnitArray[] = $entry->title;
                }
            }

            $departmentsArray = [];

            if($element->departments){
                foreach($element->departments->all() as $entry){
                    $departmentsArray[] = $entry->title;
                }
            }

            $officialsArray = [];

            if($element->officials){
                foreach($element->officials->all() as $entry){
                    $officialsArray[] = $entry->title;
                }
            }

            $topicsArray = [];

            if($element->topics){
                foreach($element->topics->all() as $entry){
                    $topicsArray[] = $entry->title;
                }
            }

            $topicsArray = [];

            if($element->topics){
                foreach($element->topics->all() as $entry){
                    $topicsArray[] = $entry->title;
                }
            }

            $serviceTypesArray = [];

            if($element->serviceTypes){
                foreach($element->serviceTypes->all() as $entry){
                    $serviceTypesArray[] = $entry->title;
                }
            }

            $results[] = [
                'Title' => $element->title ?? '',
                'Primary Unit' => $primaryUnitArray,
                'Entry Type' => $element->type->name,
                'Service Types' => $serviceTypesArray,
                'Departments' => $departmentsArray,
                'Officials' => $officialsArray,
                'Topics' => $topicsArray,
                'URL' => $element->url,
            ];
        }

        return $results;
    }
}