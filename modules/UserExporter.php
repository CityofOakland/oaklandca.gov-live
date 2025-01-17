<?php

namespace modules;

use Craft;
use craft\elements\db\ElementQueryInterface;
use craft\elements\Entry;
use craft\elements\exporters\Expanded;

class UserExporter extends Expanded
{
    public static function displayName(): string
    {
        return Craft::t('app', 'Expanded (with Primary Unit)');
    }

    public function export(ElementQueryInterface $query): array
    {
        $data = parent::export($query->with(['primaryUnit']));

        return array_map(
            function (array $entry) {
                if ($primaryUnitId = $entry['primaryUnit'][0] ?? null) {
                    $primaryUnit = Entry::find()->id($primaryUnitId)->all();
                    $entry['primaryUnit'] = array_column($primaryUnit, 'title');
                }
                return $entry;
            },
            $data,
        );
    }
}
