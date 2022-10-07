<?php
/**
 * Oakland Email Notifications plugin for Craft CMS 3.x
 *
 * Generate email notifications for various areas of the site including the Meetings entry type.
 *
 * @link      https://meetgoat.com
 * @copyright Copyright (c) 2022 Goat Creative Inc.
 */

namespace meetgoat\oaklandemailnotifications\controllers;

use meetgoat\oaklandemailnotifications\OaklandEmailNotifications;

use Craft;
use craft\web\Controller;

/**
 * Default Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Goat Creative Inc.
 * @package   OaklandEmailNotifications
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['submit', 'unsubscribe'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's submit action URL,
     * This takes form submissions from the current entry and appends the email information into an array.
     * 
     * e.g.: actions/oakland-email-notifications/default/submit
     *
     * @return mixed
     */
    public function actionSubmit()
    {
        $this->requirePostRequest();

        $request                    = Craft::$app->request;
        $id                         = $request->getParam('element_id')  ? filter_var($request->getParam('element_id'), FILTER_SANITIZE_NUMBER_INT) : null;
        $full_name                  = $request->getParam('full_name')   ? filter_var($request->getParam('full_name'), FILTER_SANITIZE_STRING) : null;
        $email                      = $request->getParam('email')       ? filter_var($request->getParam('email'), FILTER_SANITIZE_EMAIL) : null;

        $updates_meeting            = $request->getParam('updates_meeting')     ? 1 : 0;
        $updates_public_body        = $request->getParam('updates_public_body') ? 1 : 0;

        $email_notification_field   = null;
        $element                    = Craft::$app->elements->getElementById($id);

        if($element && get_class($element) == 'craft\elements\Entry'){
            if($element->fieldLayout){
                foreach($element->fieldLayout->customFieldElements as $customField){
                    if(get_class($customField->field) == 'meetgoat\oaklandemailnotifications\fields\OaklandEmailSubscriptionsField'){
                        $email_notification_field = $customField->field;
                        break;
                    }
                }
            }
        }

        if($email_notification_field && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $handle     = $email_notification_field->handle;
            $email_list = $element->$handle;

            if(!is_array($email_list)){
                $email_list = [];
            }

            $email_list[$email] = $full_name;

            $element->setFieldValue($handle, $email_list);
            Craft::$app->elements->saveElement($element);
        }

        $this->redirectToPostedUrl();
    }

    /**
     * Handle a request going to our plugin's unsubscribe action URL,
     * This takes route removes specific emails from an entry's subscription list.
     * 
     * e.g.: actions/oakland-email-notifications/default/unsubscribe
     *
     * @return mixed
     */
    public function actionUnsubscribe()
    {
        $request    = Craft::$app->request;
        $id         = $request->getParam('elementId')  ? filter_var($request->getParam('elementId'), FILTER_SANITIZE_NUMBER_INT) : null;
        $email      = $request->getParam('email')      ? filter_var($request->getParam('email'), FILTER_SANITIZE_EMAIL) : null;
        
        $email_notification_field   = null;
        $element                    = Craft::$app->elements->getElementById($id);

        if($element && get_class($element) == 'craft\elements\Entry'){
            if($element->fieldLayout){
                foreach($element->fieldLayout->customFieldElements as $customField){
                    if(get_class($customField->field) == 'meetgoat\oaklandemailnotifications\fields\OaklandEmailSubscriptionsField'){
                        $email_notification_field = $customField->field;
                        break;
                    }
                }
            }
        }

        if($email_notification_field && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $handle     = $email_notification_field->handle;
            $email_list = $element->$handle;

            if(is_array($email_list)){
                unset($email_list[$email]);
                $element->setFieldValue($handle, $email_list);
                Craft::$app->elements->saveElement($element);
            }
        }

        if($element) {
            return $this->redirect($element->getCpEditUrl());
        }
    }
}
