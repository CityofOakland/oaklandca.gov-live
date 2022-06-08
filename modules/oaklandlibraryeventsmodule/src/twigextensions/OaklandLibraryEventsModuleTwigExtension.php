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
            new TwigFilter('addEventsTotal', [$this, 'addEventsTotal']),
            new TwigFilter('insertUpcomingLibraryEvents', [$this, 'insertUpcomingLibraryEvents']),
        ];
    }

    /**
     * @param null $text
     *
     * @return string
     */
    public function addEventsTotal($number, $start = null, $end = null, $limit = 10, $currentPage, $type = 'pagination')
    {
        $params = self::buildParams($start, $end, $limit, $currentPage);
        $data   = self::getCacheResult('https://api2.bibliocommons.com/v1/oaklandlibrary/events', $params, 300);

        $total = $number;

        if($data) {
            if(isset($data['events'])){
                $api_total = $data['events']['pagination']['count'];

                $total = $total + $api_total;

                if($type == 'pagination') {
                    if($api_total > $total) {
                        return $api_total;
                    } else {
                        return $total;
                    }
                }
            }
        }

        return $total;
    }

    /**
     * @param null $text
     *
     * @return string
     */
    public function insertUpcomingLibraryEvents($array = [], $start = null, $end = null, $limit = 10, $currentPage)
    {
        $params = self::buildParams($start, $end, $limit, $currentPage);

        $data           = self::getCacheResult('https://api2.bibliocommons.com/v1/oaklandlibrary/events', $params, 300);
        $location_data  = self::getCacheResult('https://api2.bibliocommons.com/v1/oaklandlibrary/locations', array('limit'=>100), 3600);

        $events = [];

        if($data && $location_data) {
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

    public static function buildParams($start = null, $end = null, $limit = 10, $currentPage){
        $params = array(
            'startDate' => $start ? $start : date('Y-m-d'),
            'limit'     => $limit,
            'page'      => $currentPage,
        );

        if($end !== null) {
            $params['endDate'] = $end;
        }

        return $params;
    }

    public static function getCacheResult($url, $params, $time=3600){
        $cache      = Craft::$app->getCache();
        $cache_key  = $url . '?' . http_build_query($params);
        $value      = $cache->get($cache_key);

        if ($value !== null && \is_array($value) && isset($value['time']) && $value['time'] >= time() - $time) {
            return $value['value'];
        } else {
            $data = self::curl($url, $params);
            if($data) {
                $data = json_decode($data, true);
                if(!isset($data['message'])) {
                    $cache->set($cache_key,[
                        'time'  => time(),
                        'value' => $data
                    ]);
                    return $data;
                }
            }
            return false;
        }
    }

    public static function curl($url, $params=array()) {
        if(!getenv('LIBRARY_EVENTS_API_KEY')){
            return false;
        } else {
            $headers = array('x-api-key:'.getenv('LIBRARY_EVENTS_API_KEY'));
        }

        $ch = curl_init();

        if($params != null) {
            $url = $url . '?' . http_build_query($params);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);

        if($headers != null){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $result = curl_exec($ch);

        curl_close($ch);
        return $result;
    }
}
