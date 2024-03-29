<?php

/**
 * Environment Label configuration
 *
 * This file is provided as a template.
 * If you want to customize these plugin settings for your project,
 * copy this `environment-label.php` file into your `config` directory,
 * and un-comment the values you want to customize.
 *
 * I suggest using PHP environment variables, rather than hard-coded values,
 * to make your configuration more consistently maintainable.
 * (c.f. http://php.net/manual/en/function.getenv.php)
 *
 * I also suggest using the PHP dot-env package to make it easy to set and maintain
 * environment variables across your installations. (The default Craft project ships with dot-env included.)
 * (c.f. https://github.com/vlucas/phpdotenv)
 *
 * Some code examples are provided here for your reference...
 */

return [
	'showLabel' => getenv('ENV_LABEL_SHOW'),
	'labelText' => getenv('ENV_LABEL_TEXT'),
	'prefixText' => getenv('ENV_LABEL_PREFIX'),
	'suffixText' => getenv('ENV_LABEL_SUFFIX'),
	'labelColor' => getenv('ENV_LABEL_BGCOLOR'),
	'textColor' => getenv('ENV_LABEL_COLOR'),
];
