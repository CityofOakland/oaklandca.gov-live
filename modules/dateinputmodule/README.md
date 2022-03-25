# Date Input module for Craft CMS 3.x

Quick plugin for generating more accessible date inputs.

## Requirements

This module requires Craft CMS 3.0.0-RC1 or later.

## Installation

To install the module, follow these instructions.

First, you'll need to add the contents of the `app.php` file to your `config/app.php` (or just copy it there if it does not exist). This ensures that your module will get loaded for each request. The file might look something like this:
```
return [
    'modules' => [
        'date-input-module' => [
            'class' => \modules\dateinputmodule\DateInputModule::class,
        ],
    ],
    'bootstrap' => ['date-input-module'],
];
```
You'll also need to make sure that you add the following to your project's `composer.json` file so that Composer can find your module:

    "autoload": {
        "psr-4": {
          "modules\\dateinputmodule\\": "modules/dateinputmodule/src/"
        }
    },

After you have added this, you will need to do:

    composer dump-autoload
 
 …from the project’s root directory, to rebuild the Composer autoload map. This will happen automatically any time you do a `composer install` or `composer update` as well.

## Date Input Overview

A quick plugin to implement more accessible/customizable date pickers in Craft CMS twig templates instead of the default HTML5 inputs.

Using the Twig Extension will load jQuery, jQuery UI, and an additional [jQuery UI Monthpicker plugin](https://github.com/KidSysco/jquery-ui-month-picker), and render all the HTML required to set up the field.

## Using Date Input

Once the module is installed, add the `renderRangepicker()` and `renderMonthpicker()` function on your twig templates.

The `renderRangepicker()` will create a dropdown that influences your date inputs. Selecting an option will facet your date inputs into the "Upcoming", "Past", or "Both/All" options.

The `renderMonthpicker()` will render a month datepicker. You can also call `renderMonthpicker('end')` which will interact with your other date input by creating min/max month selection ranges.

You can also apply the `applyDateFilter` Twig filter to your entry queries to filter your queries based on any filled form fields. Make sure to pass the name of the field as a variable into the filter function e.g. `entryQuery|applyDateFilter('date')`.

## Date Input Roadmap

Some things to do, and ideas for potential features:

- Allow more flexibility in usage.
- Allow more customizability of labels.

* Release it

Brought to you by [meetgoat](https://meetgoat.com)
