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
    protected $allowAnonymous = ['submit'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/oakland-email-notifications/default
     *
     * @return mixed
     */
    public function actionSubmit()
    {
        $request                    = Craft::$app->request;
        $id                         = $request->getParam('elementId');
        $email_notification_field   = null;
        $element                    = Craft::$app->elements->getElementById($id);

        if($element && get_class($element) == 'craft\elements\Entry'){
            if($element->fieldLayout){
                foreach($element->fieldLayout->customFieldElements as $customField){
                    if(get_class($customField->field) == 'meetgoat\oaklandemailnotifications\fields\OaklandEmailNotificationsField'){
                        $email_notification_field = $customField->field;
                        break;
                    }
                }
            }
        }

        if($email_notification_field) {
            $element[$email_notification_field->handle] = '1234';

            Craft::$app->elements->saveElement($element);
        }

        $this->redirectToPostedUrl();
    }
}
