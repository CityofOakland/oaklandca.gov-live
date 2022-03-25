<?php
/**
 * Date Input module for Craft CMS 3.x
 *
 * Quick plugin for generating easy date inputs.
 *
 * @link      https://meetgoat.com
 * @copyright Copyright (c) 2022 meetgoat
 */

namespace modules\dateinputmodule\twigextensions;

use modules\dateinputmodule\DateInputModule;
use modules\dateinputmodule\assetbundles\dateinputmodule\DateInputModuleAsset;

use Craft;

use Exception;
use DateTime;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig can be extended in many ways; you can add extra tags, filters, tests, operators,
 * global variables, and functions. You can even extend the parser itself with
 * node visitors.
 *
 * http://twig.sensiolabs.org/doc/advanced.html
 *
 * @author    meetgoat
 * @package   DateInputModule
 * @since     1.0.0
 */
class DateInputModuleTwigExtension extends AbstractExtension
{
    // Public Methods
    // =========================================================================

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'DateInputModule';
    }

    /**
     * Returns an array of Twig filters, used in Twig templates via:
     *
     *      {{ 'something' | someFilter }}
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new TwigFilter('applyDateFilter', [$this, 'applyDateFilter'])
        ];
    }

    /**
     * Returns an array of Twig functions, used in Twig templates via:
     *
     *      {% set this = someFunction('something') %}
     *
    * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('renderMonthpicker', [$this, 'renderMonthpicker']),
            new TwigFunction('renderRangepicker', [$this, 'renderRangepicker']),
        ];
    }

    /**
     * Our function called via Twig; it can do anything you want
     *
     * @param object $query
     *
     * @return string
     */
    public function applyDateFilter(craft\elements\db\EntryQuery $query, $field_name)
    {
        $request    = Craft::$app->getRequest();
        $date_query = ['and'];

        $start_date = $request->getQueryParam('start_date');
        $end_date   = $request->getQueryParam('end_date');
        $type       = $request->getQueryParam('type');

        if($start_date !== null and $end_date !== null) {
            if($start_date !== ''){
                $date_query = array_merge($date_query, ['>= '.($start_date.'-01')]);
            }

            if($end_date !== ''){
                $date       = new DateTime($end_date."-01");
                $last_day   = $date->modify("+1 month -1 day");
                $date_query = array_merge($date_query, ['<= '.($last_day->format("Y-m-d"))]);
            }
        }

        switch($type){
            case 2:
                $query->order('date desc');
                break;
            case 3:
                $query->order('date desc');
                $date_query = array_merge($date_query, ['< '.date('Y-m-d')]);
                break;
            default:
                $query->order('date asc');
                $date_query = array_merge($date_query, ['>= '.date('Y-m-d')]);
        }

        try {
            return $query->{$field_name}($date_query);
        } catch (Exception $e) {
            Craft::warning('Tried to apply a date filter to a non date filter field.', 'date-input-module');
            return $query;
        }
    }

    /**
     * Our function called via Twig; it can do anything you want
     *
     * @param string $position
     * @param string $type
     *
     * @return string
     */
    public function renderMonthpicker($position = 'start', $type = 'upcoming')
    {
        $request    = Craft::$app->getRequest();
        $view       = Craft::$app->getView();
        $view->registerAssetBundle(DateInputModuleAsset::class);

        $positions = [
            'start' => 'start_date',
            'end'   => 'end_date',
        ];

        if(!array_key_exists($position, $positions)) {
            throw new Exception('The $position variable can only be set as `start` or `end`');
        }

        $name       = $positions[$position];
        $value      = $request->getQueryParam($name);

        $result="
        <div class='monthpicker-container'>
            <input
                id='$name'
                type='month'
                name='$name'
                value='$value'
            />
        </div>";
        return $result;
    }

    /**
     * Our function called via Twig; it can do anything you want
     *
     * @param string $upcoming_label
     * @param string $both_label
     * @param string $past_label
     *
     * @return string
     */
    public function renderRangepicker($upcoming_label = 'Upcoming Meetings Only', $both_label = 'Past and Upcoming Meetings', $past_label = 'Past Meetings Only')
    {
        $request = Craft::$app->getRequest();

        return '<label for="type">
            Date Range
            <select name="type">
                <option value="1" '.($request->getQueryParam('type') == 1 ? 'selected' : '').'>'.$upcoming_label.'</option>
                <option value="2" '.($request->getQueryParam('type') == 2 ? 'selected' : '').'>'.$both_label.'</option>
                <option value="3" '.($request->getQueryParam('type') == 3 ? 'selected' : '').'>'.$past_label.'</option>
            </select>
        </label>';
    }
}
