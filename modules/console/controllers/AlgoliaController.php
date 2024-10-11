<?php

namespace modules\console\controllers;

use Algolia\AlgoliaSearch\SearchClient;
use Craft;
use craft\console\Controller;
use Illuminate\Support\Collection;
use rias\scout\Scout;
use yii\base\InvalidConfigException;
use yii\console\ExitCode;
use yii\di\NotInstantiableException;

class AlgoliaController extends Controller
{
    /**
     * @throws InvalidConfigException|NotInstantiableException
     */
    public function actionUpdateViewCount(string $indexName): int
    {
        $scoutSettings = Scout::$plugin->getSettings();
        $scoutIndex = $scoutSettings->getIndices()->firstWhere('indexName', $indexName);
        $index = Craft::$container->get(SearchClient::class)->initIndex($indexName);

        // $ids = $scoutIndex->criteria->ids();
        $updates = Collection::make($scoutIndex->criteria->all())
            ->map(fn ($entry) => ['objectID' => $entry->id, 'viewCount' => $entry->viewCount]);

        $batches = Collection::make($index->partialUpdateObjects($updates->all()));
        $total = Collection::make($batches)->reduce(fn ($carry, $item) => $carry + count($item['objectIDs']), 0);

        $this->stdout("$total element(s) updated in {$batches->count()} batch(es).\n");

        return ExitCode::OK;
    }
}
