<?php
namespace modules;

use Craft;

/**
 * Custom module to help parse information sent/received from the Algolia Scout plugin.
 */
class ScoutModule extends \yii\base\Module
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


    // Format specific modules in the content builder field.
    public static function contentBuilder($element)
    {
        $body   = [];
        if (!empty($element)) {
            foreach ($element->all() as $block) {
                $text = '';
                switch ($block->type) {
                    case 'heading':
                    case 'subheading':
                    case 'text':
                        $text = strip_tags($block->text);
                        break;
                    case 'textImageBlock':
                        $text = strip_tags($block->textBlock);
                        break;
                    case 'linksWithDescriptions':
                        $text = strip_tags($block->linkDescription);
                        break;
                    case 'largeEntryLinks':
                    case 'smallEntryLinks':
                        $text = strip_tags($block->entriesDescription);
                        break;
                    default:
                }
                if($text){
                    if(strlen($text) > 1000) {
                        $text = mb_substr($text, 0, 1000) . '...';
                    }
                    $body[] = $text;
                }
            }
        }
        return $body;
    }


    // Return an element's CTA Button text (if it exists).
    public static function ctaButtonText($element)
    {
        return !empty($element->ctaButton->text) ? $element->ctaButton->text : null;
    }


    // Return an entry's postDate timestamp.
    public static function entryDate($entry)
    {
        return $entry->postDate->getTimestamp() * 1000;
    }


    // Return an entry's formatted postDate.
    public static function entryPrettyDate($entry)
    {
        return $entry->postDate->format('F j, Y');
    }


    // Return an entry's URL. Will return a redirected URL if it exists.
    public static function entryUrl($entry)
    {
        return empty($entry->redirectUrl) ? $entry->url : $entry->redirectUrl;
    }


    // Create an array from object property.
    public static function enumEntries($section, $element)
    {
        $sectionArray = [];
        if(!empty($element->$section)) {
            foreach ($element->$section as $value) {
                $sectionArray[] = $value->title;
            }
        }
        return $sectionArray;
    }

    public static function portrait($element)
    {
        if (!empty($element->banner)) {
            if (!empty($element->banner->one())) {
                return $element->banner->one()->url;
            }
        }
        return null;
    }


    // Format rich text field.
    public static function richTextSplit($field)
    {
        if (!empty($field)) {
            $array = [];
            $str = strip_tags($field);
            $ps = preg_split('~((?:\S*?\s){100})~', $str, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            foreach ($ps as $node) {
                $array[] = $node;
            }
            return $array;
        }
    }

    // Format rich text field.
    public static function sectionPriority($name){
        if($name == 'Documents'){
            return 1;
        } else {
            return 0;
        }
    }
}
