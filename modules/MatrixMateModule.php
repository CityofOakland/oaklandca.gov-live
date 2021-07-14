<?php
namespace modules;

use Craft;

/**
 * Custom module to help parse information sent/received from the Algolia Scout plugin.
 */
class MatrixMateModule extends \yii\base\Module
{
    public function init()
    {
        // Set a @modules alias pointed to the modules/ directory
        Craft::setAlias('@modules', __DIR__);

        // Set the controllerNamespace based on whether this is a console or web request
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'modules\\console\\controllers';
        } else {
            $this->controllerNamespace = 'modules\\controllers';
        }

        parent::init();
    }

    /**
     * Generates a MatrixMate group.
     *
     * Creates a field group based on the parameters passed in configuration. 
     * Makes things more readable for controlling permissions on the matrixmate.php configuration.
     *
     * @author  Tim Lu <tim.lu@meetgoat.com>
     *
     * @since 1.0
     *
     * @param string    $label  The label of the grouped dropdown.
     * @param array     $fields List of fields to be in the group, with certain settings
     * @param array     $groups Other groups that can have access to this block.
     * 
     * @return [] The resulting array to be passed into the config.
     */
    function createConfigBlock($label, $fields) {
        $user         = Craft::$app->getUser();
        $final_fields = [];
        foreach($fields as $field_handle => $field) {
            if($field['adminOnly']) {
                if($user->isAdmin) {
                    $final_fields[] = $field_handle;    
                } elseif(isset($field['groups']) && $user->getIdentity()) {
                    $isInGroup = false;
                    foreach($field['groups'] as $group) {
                        if($user->getIdentity()->isInGroup($group)) $isInGroup = true;
                    }
                    if($isInGroup) $final_fields[] = $field_handle;
                }
            } else {
                $final_fields[] = $field_handle;
            }
        }

        return [
            'label' => $label,
            'types' => $final_fields
        ];
    }

    /**
     * Generates a MatrixMate permission configuration.
     *
     * Mostly used to DRY out configuration code, since the general configuration follows the same logic.
     *
     * @author  Tim Lu <tim.lu@meetgoat.com>
     *
     * @since 1.0
     *
     * @param string $fieldGroups   The fields to be included into this 
     * 
     * @return [] The resulting array to be passed into the config.
     */
    function createConfigPermissions($fieldGroups) {
        return [
            'groups' => $fieldGroups,
            'types' => [
                'meetingsTable' => [
                    'maxLimit' => 1
                ],
            ],
            'hideUngroupedTypes' => ($isUserAdmin ? false : true),
        ];
    }
}
