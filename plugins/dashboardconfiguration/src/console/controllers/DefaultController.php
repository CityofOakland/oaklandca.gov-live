<?php
namespace goat\dashboardconfiguration\console\controllers;

use goat\dashboardconfiguration\Test;

use Craft;

use craft\base\FieldInterface;
use craft\db\Table;
use craft\fields\Matrix;
use craft\helpers\Db;
use craft\helpers\Queue;
use goat\dashboardconfiguration\queue\jobs\CustomFindAndReplace;

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

    public function actionFindAndReplace()
    {
        // 1639761861_detailed_urls_mapped
        // 1639768623_detailed_urls_mapped

        // 1639769985_detailed_urls_mapped
        // 1639774029_detailed_urls_mapped
        // manual_detailed_urls_mapped

        $data = file(__dir__ . "/manual_detailed_urls_mapped.txt");
        $total_jobs = 0;

        foreach ($data as $index => $row) {
            $row = explode(",", $row);
            foreach($row as $index => $item){
                $row[$index] = trim($item);
            }
            $elementId  = $row[0];
            $table      = $row[1];
            $column     = $row[2];
            $find       = $row[3];
            $replace    = $row[4];

            Queue::push(new CustomFindAndReplace([
                'elementId' => $elementId,
                'table'     => $table,
                'column'    => $column,
                'find'      => $find,
                'replace'   => $replace,
            ]));

            // Queue::push(new CustomFindAndReplace([
            //     'elementId' => $elementId,
            //     'table'     => $table,
            //     'column'    => $column,
            //     'find'      => trim($find, '"'),
            //     'replace'   => trim($replace, '"'),
            // ]));

            $total_jobs = $total_jobs + 1;
        }

        echo 'Total jobs queued: ' . $total_jobs;
    }

    public function actionFind()
    {
        $timestamp = time();
        $export_urls_uri            = __dir__ . "/" . $timestamp . "_urls.txt";
        $export_urls_context_uri    = __dir__ . "/" . $timestamp . "_urls_w_context.txt";
        $export_urls_detailed_uri   = __dir__ . "/" . $timestamp . "_detailed_urls.txt";
        file_put_contents($export_urls_uri, "");
        file_put_contents($export_urls_detailed_uri, "");

        $this->_textColumns = [
            [Table::CONTENT, 'title'],
        ];

        foreach (Craft::$app->getFields()->getAllFields() as $field) {
            if ($field instanceof Matrix) {
                $this->_checkMatrixField($field);
            } else {
                $this->_checkField($field, Table::CONTENT, 'field_');
            }
        }

        $count = 0;

        $all_matches = [];

        $this->_textColumns[] = ['stc_26_table', 'field_column1Cell'];
        $this->_textColumns[] = ['stc_26_table', 'field_column2Cell'];
        $this->_textColumns[] = ['stc_26_table', 'field_column3Cell'];
        $this->_textColumns[] = ['stc_26_table', 'field_column4Cell'];

        $this->_textColumns[] = ['stc_27_table', 'field_column1Cell'];
        $this->_textColumns[] = ['stc_27_table', 'field_column2Cell'];
        $this->_textColumns[] = ['stc_27_table', 'field_column3Cell'];

        $this->_textColumns[] = ['stc_30_table', 'field_column1Cell'];
        $this->_textColumns[] = ['stc_30_table', 'field_column2Cell'];

        foreach ($this->_textColumns as $i => [$table, $column]) {

            $rows = (new \yii\db\Query())
                ->select(['elementId',$column])
                ->from($table)
                ->where(['like', $column, 'oaklandnet.com'])
                ->all();

            foreach($rows as $row) {
                $elementId  = $row['elementId'];
                $content    = $row[$column];
                $edit_url   = '';

                $element = Craft::$app->elements->getElementById($elementId);

                // Figure out which entry this is coming from.
                if($element){
                    switch ($element->displayName()) {
                        case 'Entry':
                            $edit_url = $element->getCurrentRevision() ? $element->getCurrentRevision()->getCpEditUrl() : $element->getCpEditUrl();
                            break;
                        case 'Matrix Block':
                            if($matrixblock = Craft::$app->matrix->getBlockById($elementId)){
                                $entry = Craft::$app->elements->getElementById($matrixblock->ownerId);
                                if($entry){
                                    $edit_url   = $entry->getCurrentRevision() ? $entry->getCurrentRevision()->getCpEditUrl() : $entry->getCpEditUrl();
                                }
                            }
                            break;
                        case 'SuperTable Block':
                            if($supertableblock = SuperTableBlockElement::find()->id($elementId)->one()){
                                if($matrixblock = Craft::$app->elements->getElementById($supertableblock->ownerId)){
                                    $entry = Craft::$app->elements->getElementById($matrixblock->ownerId);
                                    if($entry){
                                        $edit_url   = $entry->getCurrentRevision() ? $entry->getCurrentRevision()->getCpEditUrl() : $entry->getCpEditUrl();
                                    }
                                }
                            }
                        default:
                            break;
                    }
                }

                $edit_url = preg_replace('/\?.*/', '', $edit_url);
                echo $edit_url;
                echo PHP_EOL;

                $regex = "/(https?:\/\/www2.oaklandnet\.com(\/[A-Za-z0-9\-\._~:\/\?#\[\]@!$&'\(\)\*\+,;\=]*)?)/";

                preg_match_all($regex, $content, $matches);

                $matched_urls = [
                    'id'        => $elementId,
                    'table'     => $table,
                    'column'    => $column,
                    'edit_url'  => $edit_url,
                    'matches'   => [],
                ];

                foreach($matches[0] as $value) {
                    $count = $count + 1;
                    echo $count;
                    echo PHP_EOL;

                    $matched_urls['matches'][$value] = $value;
                }

                if(count($matches[0])){
                    $all_matches[] = $matched_urls;
                }
            }
        }

        $unique_urls = [];

        foreach($all_matches as $matched_urls){
            foreach($matched_urls['matches'] as $match) {
                file_put_contents($export_urls_detailed_uri, $matched_urls['id'], FILE_APPEND);
                file_put_contents($export_urls_detailed_uri, ", ", FILE_APPEND);
                file_put_contents($export_urls_detailed_uri, $matched_urls['table'], FILE_APPEND);
                file_put_contents($export_urls_detailed_uri, ", ", FILE_APPEND);
                file_put_contents($export_urls_detailed_uri, $matched_urls['column'], FILE_APPEND);
                file_put_contents($export_urls_detailed_uri, ", ", FILE_APPEND);
                file_put_contents($export_urls_detailed_uri, $match, FILE_APPEND);
                file_put_contents($export_urls_detailed_uri, "\n", FILE_APPEND);

                $unique_urls[$match] = [$match, $matched_urls['edit_url']];
            }
        }

        foreach($unique_urls as $unique_url) {
            file_put_contents($export_urls_uri, $unique_url[0], FILE_APPEND);
            file_put_contents($export_urls_uri, "\n", FILE_APPEND);
        }

        foreach($unique_urls as $unique_url) {
            file_put_contents($export_urls_context_uri, $unique_url[0], FILE_APPEND);
            file_put_contents($export_urls_context_uri, ", ", FILE_APPEND);
            file_put_contents($export_urls_context_uri, $unique_url[1], FILE_APPEND);
            file_put_contents($export_urls_context_uri, "\n", FILE_APPEND);
        }

        echo count($unique_urls);
    }

    /**
     * Checks whether the given field is saving data into a textual column, and saves it accordingly.
     *
     * @param FieldInterface $field
     * @param string $table
     * @param string $fieldColumnPrefix
     */
    private function _checkField(FieldInterface $field, string $table, string $fieldColumnPrefix)
    {
        if (!$field::hasContentColumn()) {
            return;
        }

        $columnType = $field->getContentColumnType();
        if (is_array($columnType)) {
            $columnType = reset($columnType);
        }

        if (!preg_match('/^\w+/', $columnType, $matches)) {
            return;
        }

        $columnType = strtolower($matches[0]);

        if (in_array($columnType, [
            'tinytext',
            'mediumtext',
            'longtext',
            'text',
            'varchar',
            'string',
            'char',
        ], true)) {

            if($field->columnSuffix){
                $this->_textColumns[] = [$table, $fieldColumnPrefix . $field->handle . '_' . $field->columnSuffix];
            } else {
                $this->_textColumns[] = [$table, $fieldColumnPrefix . $field->handle];
            }
        }
    }

    /**
     * Registers any textual columns associated with the given Matrix field.
     *
     * @param Matrix $matrixField
     * @throws Exception if the content table can't be determined
     */
    private function _checkMatrixField(Matrix $matrixField)
    {
        $blockTypes = Craft::$app->getMatrix()->getBlockTypesByFieldId($matrixField->id);

        foreach ($blockTypes as $blockType) {
            $fieldColumnPrefix = 'field_' . $blockType->handle . '_';

            foreach ($blockType->getFields() as $field) {
                $this->_checkField($field, $matrixField->contentTable, $fieldColumnPrefix);
            }
        }
    }
}
