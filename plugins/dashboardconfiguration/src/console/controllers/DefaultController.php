<?php
namespace goat\dashboardconfiguration\console\controllers;

use goat\dashboardconfiguration\Test;

use Craft;

use craft\base\FieldInterface;
use craft\db\Table;
use craft\fields\Matrix;
use craft\helpers\Db;
use craft\helpers\Queue;
use craft\queue\jobs\FindAndReplace;

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
    }

    public function actionFind()
    {
        $timestamp = time();
        $export_file_uri = __dir__ . "/" . $timestamp . "_urls.txt";
        file_put_contents($export_file_uri, "");

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
                                // echo $supertableblock->id;
                                // $matrix = Craft::$app->elements->getElementById($supertableblock->ownerId);
                                if($matrixblock = Craft::$app->elements->getElementById($supertableblock->ownerId)){
                                    $entry = Craft::$app->elements->getElementById($matrixblock->ownerId);
                                    if($entry){
                                        $edit_url   = $entry->getCurrentRevision() ? $entry->getCurrentRevision()->getCpEditUrl() : $entry->getCpEditUrl();
                                    }
                                }
                                // if($entry){
                                //     $edit_url   = $entry->getCurrentRevision() ? $entry->getCurrentRevision()->getCpEditUrl() : $entry->getCpEditUrl();
                                // }
                            }
                        default:
                            break;
                    }
                }

                $edit_url = preg_replace('/\?.*/', '', $edit_url);
                echo $edit_url;
                echo PHP_EOL;

                $regex = "/(https?:\/\/data.oaklandnet\.com(\/[A-Za-z0-9\-\._~:\/\?#\[\]@!$&'\(\)\*\+,;\=]*)?)/";

                preg_match_all($regex, $content, $matches);

                foreach($matches[0] as $value) {
                    $count = $count + 1;
                    echo $count;
                    echo PHP_EOL;
                    $all_matches[$value] = [$value, $element->displayName(), $edit_url];
                }
            }
        }

        foreach($all_matches as [$value, $display_name, $edit_url]){
            file_put_contents($export_file_uri, $value, FILE_APPEND);
            file_put_contents($export_file_uri, ", ", FILE_APPEND);
            file_put_contents($export_file_uri, $display_name, FILE_APPEND);
            file_put_contents($export_file_uri, ", ", FILE_APPEND);
            file_put_contents($export_file_uri, $edit_url, FILE_APPEND);
            file_put_contents($export_file_uri, "\n", FILE_APPEND);
        }
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
