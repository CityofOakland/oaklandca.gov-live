<?php
/**
 * Oakland Email Notifications plugin for Craft CMS 3.x
 *
 * Generate email notifications for various areas of the site including the Meetings entry type.
 *
 * @link      https://meetgoat.com
 * @copyright Copyright (c) 2022 Goat Creative Inc.
 */

namespace meetgoat\oaklandemailnotifications\variables;

use meetgoat\oaklandemailnotifications\OaklandEmailNotifications;

use Craft;

/**
 * Oakland Email Notifications Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.oaklandEmailNotifications }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Goat Creative Inc.
 * @package   OaklandEmailNotifications
 * @since     1.0.0
 */
class OaklandEmailNotificationsVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.oaklandEmailNotifications.renderForm }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.oaklandEmailNotifications.renderForm(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function renderForm($optional = null)
    {
        // This form assume currently uses Tailwind styles.
        // For reference: https://tailwindcss.com/

        $element                    = Craft::$app->getUrlManager()->getMatchedElement();
        $email_notification_field   = null;
        $html                       = '';

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

        if($email_notification_field) {
            $token = Craft::$app->request->getCsrfToken();

            $handle = $email_notification_field->handle;

            echo var_dump($element->$handle);

            $html .= '
                <form action="" method="POST">
                    <input type="hidden" name="CRAFT_CSRF_TOKEN" value="'.$token.'">
                    <input type="hidden" name="element_id" value="'.$element->id.'"/>
                    <input type="hidden" name="action" value="oakland-email-notifications/default/submit">
                    <label class="w-6/12">
                        <span>Full Name</span>
                        <input id="full_name" type="text" name="full_name" placeholder="Type your name"/>
                    </label>
                    <label class="w-6/12">
                        <span>Email</span>
                        <input id="email" type="text" name="email" placeholder="Type your email"/>
                    </label>

                    <h4 class="pt-0 mb-2">Which email updates would you like to subscribe to?</h4>
                    <label>
                        <input name="updates_meeting" type="checkbox" value="1"/>
                        <span>Email updates to this meeting’s date, time and agenda items</span>
                    </label>
                    <label class="-mt-4">
                        <input name="updates_public_body" type="checkbox" value="1"/>
                        <span>Email updates about future meetings from the Bicycle and Pedestrian Committee</span>
                    </label>
                    <button type="submit" class="button mt-4">Submit</button>
                </form>';
        } else {
            $html = '<p>This form requires a Email Notification Field Type to be set first.</p>';
        }

        return $html;
    }
}
