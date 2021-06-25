<?php
namespace goat\dashboardconfiguration\console\controllers;

use goat\dashboardconfiguration\Test;

use Craft;

use craft\helpers\Queue;
use craft\queue\jobs\FindAndReplace;

use yii\console\Controller;
use yii\helpers\Console;


class DefaultController extends Controller
{
    public function actionFindAndReplace()
    {
        $data = file(__dir__ . "/url_mapping.txt");

        $total_jobs = 0;

        foreach ($data as $index => $row) {
            $row = explode(",", $row);
            foreach($row as $index => $item){
                $row[$index] = trim($item);
            }
            $url                = $row[0];
            $url_replacement    = $row[1];
            $replacement_type   = $row[2];
            $code               = $row[3];

            if($replacement_type == 'Replacement (Redirect)' || $replacement_type == 'Replacement (Default)') {
                Queue::push(new FindAndReplace([
                    'find' => $url,
                    'replace' => $url_replacement,
                ]));
                $total_jobs = $total_jobs + 1;
            }
        }

        echo 'Total jobs queued: ' . $total_jobs;

        // echo 'lasdfjdsa';
    }
}
