<?php

namespace modules;

use craft\base\ElementExporter;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;

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
            $relatedEntries = [];

            if ($element->departments) {
                foreach ($element->departments->all() as $entry) {
                    $relatedEntries[] = $entry->id;
                }
            }

            if ($element->projects) {
                foreach ($element->projects->all() as $entry) {
                    $relatedEntries[] = $entry->id;
                }
            }

            if ($element->topics) {
                foreach ($element->topics->all() as $entry) {
                    $relatedEntries[] = $entry->id;
                }
            }

            $results[] = [
                'ID' => $element->id,
                'Title' => $element->title ?? '',
                'relatedEntries' => $relatedEntries
            ];
        }

        return $results;
    }
}
