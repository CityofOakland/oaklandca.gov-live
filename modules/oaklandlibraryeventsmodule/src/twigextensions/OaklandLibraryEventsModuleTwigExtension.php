<?php
/**
 * Oakland Library Events module for Craft CMS 3.x
 *
 * This module grabs BiblioCommons Library Events and provides Twig Extensions to insert these events into your template queries.
 *
 * @link      https://meetgoat.com
 * @copyright Copyright (c) 2022 Goat Creative Inc.
 */

namespace modules\oaklandlibraryeventsmodule\twigextensions;

use modules\oaklandlibraryeventsmodule\OaklandLibraryEventsModule;

use Craft;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @author    Goat Creative Inc.
 * @package   OaklandLibraryEventsModule
 * @since     1.0.0
 */
class OaklandLibraryEventsModuleTwigExtension extends AbstractExtension
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'OaklandLibraryEventsModule';
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new TwigFilter('insertUpcomingLibraryEventsTotal', [$this, 'insertUpcomingLibraryEventsTotal']),
            new TwigFilter('insertUpcomingLibraryEventsPaginationTotal', [$this, 'insertUpcomingLibraryEventsPaginationTotal']),
            new TwigFilter('insertUpcomingLibraryEvents', [$this, 'insertUpcomingLibraryEvents']),
        ];
    }

    // /**
    //  * @inheritdoc
    //  */
    // public function getFunctions()
    // {
    //     return [
    //         new TwigFunction('someFunction', [$this, 'someInternalFunction']),
    //     ];
    // }

    /**
     * @param null $text
     *
     * @return string
     */
    public function insertUpcomingLibraryEventsTotal($number, $start = null, $end = null)
    {
        $params = array(
            'startDate' => $start ? $start : date('Y-m-d'),
            'limit'     => 1,
        );

        if($end !== null) {
            $params['endDate'] = $end;
        }

        $headers = array(
            'x-api-key:lowBUEGFfy9dX4O06hsMj42jbwoElt7raf6Oxp3C'
        );

        $data = self::curl('https://api2.bibliocommons.com/v1/oaklandlibrary/events', $headers, $params);

        $total = $number;

        if($data) {
            $data = json_decode($data, true);
            // var_dump($data);
            if(isset($data['events'])){
                $api_total = $data['events']['pagination']['count'];

                $total = $total + $api_total;
            }
        }

        // echo $total;

        return $total;
    }

    /**
     * @param null $text
     *
     * @return string
     */
    public function insertUpcomingLibraryEventsPaginationTotal($number, $start = null, $end = null)
    {
        $params = array(
            'startDate' => $start ? $start : date('Y-m-d'),
            'limit'     => 1,
        );

        // if($end !== null) {
        //     $params['endDate'] = $end;
        // }

        $headers = array(
            'x-api-key:lowBUEGFfy9dX4O06hsMj42jbwoElt7raf6Oxp3C'
        );

        $data = self::curl('https://api2.bibliocommons.com/v1/oaklandlibrary/events', $headers, $params);

        $total = $number;

        if($data) {
            $data = json_decode($data, true);
            // var_dump($data);
            if(isset($data['events'])){
                $api_total = $data['events']['pagination']['count'];

                if($api_total > $total) {
                    return $api_total;
                } else {
                    return $total;
                }
            }
        }

        // echo $total;

        return $total;
    }

    /**
     * @param null $text
     *
     * @return string
     */
    public function insertUpcomingLibraryEvents($array = [], $start = null, $end = null, $limit = 10, $currentPage)
    {

        // $array[] = ['type'=>'api'];

        // if(empty($array)) {
            $params = array(
                'startDate' => $start ? $start : date('Y-m-d'),
                'limit'     => $limit,
                'page'      => $currentPage,
            );

            if($end !== null) {
                $params['endDate'] = $end;
            }
        // }

        //  elseif(count($array) < 10) {
        //     $firstElement   = $array[0];

        //     $startDate = $firstElement->start->format('Y-m-d');
        //     // $endDate = $lastElement->end->format('Y-m-d');

        //     $params = array(
        //         'startDate' => $startDate,
        //         // 'endDate'   => $endDate,
        //         'limit'     => $limit - count($array),
        //         'page'      => $currentPage,
        //     );
        // } elseif(count($array) == 10) {
        //     $firstElement   = $array[0];
        //     $lastElement    = $array[count($array) - 1];

        //     $startDate = $firstElement->start->format('Y-m-d');
        //     $endDate = $lastElement->end->format('Y-m-d');

        //     echo $startDate;
        //     echo $endDate;

        //     $params = array(
        //         'startDate' => $startDate,
        //         'endDate'   => $endDate,
        //         'limit'     => $limit,
        //         'page'      => $currentPage,
        //     );
        // }

        $headers = array(
            'x-api-key:lowBUEGFfy9dX4O06hsMj42jbwoElt7raf6Oxp3C'
        );

        $data           = self::curl('https://api2.bibliocommons.com/v1/oaklandlibrary/events', $headers, $params);
        $location_data  = self::curl('https://api2.bibliocommons.com/v1/oaklandlibrary/locations', $headers, array('limit'=>100));

        $events = [];

        if($data && $location_data) {
            $data           = json_decode($data, true);
            $location_data  = json_decode($location_data, true);
            if(isset($data['events']) && isset($location_data['locations'])){
                foreach($data['entities']['events'] as $uid => $event) {
                    $locationId = $event['definition']['branchLocationId'];

                    $event_parsed_object = [
                        'type'      => 'api',
                        'uid'       => $uid,
                        'title'     => $event['definition']['title'],
                        'startDate' => strtotime($event['definition']['start']),
                        'endDate'   => strtotime($event['definition']['end']),
                        'isVirtual' => isset($event['definition']['isVirtual']) ? $event['definition']['isVirtual'] : false,
                    ];

                    if($locationId) {
                        $event_parsed_object['location'] = $location_data['entities']['locations'][$locationId]['name'];
                    }

                    $events[] = $event_parsed_object;
                }
            }
        }

        if(empty($array)){
            $array = $events;
        } else {
            $merged_array = array_merge($array, $events);

            usort($merged_array, function($a, $b){

                $a = is_object($a) ? $a->start->getTimestamp() : $a['startDate'];
                $b = is_object($b) ? $b->start->getTimestamp() : $b['startDate'];

                return $a - $b;
            });

            $array = $merged_array;
        }

        return $array;
    }

    public static function curl($url, $headers=array(), $params=array()) {
        $ch = curl_init();

        if($params != null) {
            $url = $url . '?' . http_build_query($params);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);

        // if($params != null){
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        // }
        if($headers != null){
            // curl_setopt($ch, CURLOPT_VERBOSE, 1);
            // curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $result = curl_exec($ch);

        curl_close($ch);
        return $result;
    }

    // public static function getJson($url, $params=array()) {
    //     return json_decode(self::curl($url), false);
    // }
}
