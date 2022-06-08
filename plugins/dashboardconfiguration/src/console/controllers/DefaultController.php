<?php
namespace goat\dashboardconfiguration\console\controllers;

use Craft;

use craft\helpers\Queue;
use goat\dashboardconfiguration\queue\jobs\EventsCalendarizeUpdate;

use yii\console\Controller;
use DateInterval;


class DefaultController extends Controller
{   

    // TODO: Move this functionality to another plugin.
    public function actionEventsImport()
    {
        $total_jobs = 0;

        $entries = \craft\elements\Entry::find()
            ->section('events')
            ->all();

        foreach ($entries as $key => $entry) {
            if($entry->eventId){
                $total_jobs = $total_jobs + 1;

                $startDate  = null;
                $endDate    = null;
                $elementId  = $entry->id;

                $hours = 7;

                if($entry->startDate){
                    echo $entry->startDate->format('Y-m-d H:i:s');
                    echo PHP_EOL;
                    $startDate = $entry->startDate;
                    $startDate = $startDate->add(new DateInterval("PT{$hours}H"))->format('Y-m-d H:i:s');

                    if($entry->endDate){
                        echo $entry->endDate->format('Y-m-d H:i:s');
                        echo PHP_EOL;
                        $endDate = $entry->endDate;
                        $endDate = $endDate->add(new DateInterval("PT{$hours}H"))->format('Y-m-d H:i:s');
                    }
                }
                if($startDate && $elementId){
                    Queue::push(new EventsCalendarizeUpdate([
                        'startDate'     => $startDate,
                        'endDate'       => $endDate,
                        'elementId'     => $elementId,
                    ]));
                }
            }
        }

        echo 'Total jobs queued: ' . $total_jobs;
        echo PHP_EOL;
    }
}
