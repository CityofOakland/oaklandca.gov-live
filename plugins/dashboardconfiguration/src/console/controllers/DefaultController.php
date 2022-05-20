<?php
namespace goat\dashboardconfiguration\console\controllers;

use goat\dashboardconfiguration\Test;

use Craft;

use craft\base\FieldInterface;
use craft\db\Table;
use craft\fields\Matrix;
use craft\helpers\Db;
use craft\helpers\Queue;
use goat\dashboardconfiguration\queue\jobs\EventsCalendarizeUpdate;

use yii\console\Controller;
use yii\helpers\Console;

use verbb\supertable\elements\SuperTableBlockElement;


class DefaultController extends Controller
{   
    private $_textColumns;


    /**
     * Queues up a bunch of find and replace jobs in Craft CMS.
     *
     * This function is dependant on another file being included in this file's directory, called /url_mapping.txt
     * The file is in CSV format, and it's formatted in the following order:
     * URL to Find, URL to Replace, Type of Replacement (Meta), Status Code of URL to Replace
     * 
     * This function is run by using ./craft dashboard-configuration/default/find-and-replace in terminal.
     *
     * @author  Tim Lu <tim.lu@meetgoat.com>
     *
     * @since 1.0
     */

    // TODO: Move this functionality to another plugin.

    public function actionEventsImport()
    {
        $data = file(__dir__ . "/events.csv");
        $total_jobs = 0;

        foreach ($data as $index => $row) {
            $row = explode(",", $row);
            foreach($row as $index => $item){
                $row[$index] = trim($item);
            }
            $elementId  = $row[0];
            $startDate  = $row[1];
            $endDate    = $row[2];

            Queue::push(new EventsCalendarizeUpdate([
                'elementId'     => $elementId,
                'startDate'     => $startDate,
                'endDate'       => $endDate,
            ]));

            echo $elementId;
            echo PHP_EOL;

            $total_jobs = $total_jobs + 1;
        }


        echo 'Total jobs queued: ' . $total_jobs;
    }
}
