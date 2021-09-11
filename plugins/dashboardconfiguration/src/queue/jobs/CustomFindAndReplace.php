<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace goat\dashboardconfiguration\queue\jobs;

use Craft;
use craft\base\FieldInterface;
use craft\db\Table;
use craft\fields\Matrix;
use craft\helpers\Db;
use craft\helpers\ElementHelper;
use craft\queue\BaseJob;
use yii\base\Exception;

/**
 * FindAndReplace job
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0.0
 */
class CustomFindAndReplace extends BaseJob
{

    public $elementId;
    public $table;
    public $column;
    public $find;
    public $replace;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        Db::replace($this->table, $this->column, $this->find, $this->replace, 'elementId='.$this->elementId);
    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return Craft::t('app', 'Replacing “{find}” with “{replace}”', [
            'find' => $this->find,
            'replace' => $this->replace,
        ]);
    }
}