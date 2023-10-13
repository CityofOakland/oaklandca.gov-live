<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

/**
 * Blitz config.php
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

return [
	'*' => [
// With this setting enabled, Blitz will log detailed messages to `storage/logs/blitz.log`.
		'debug' => true,
// With this setting enabled, Blitz will begin caching pages according to the included/excluded URI patterns. Disable this setting to prevent Blitz from caching any new pages.
		'cachingEnabled' => true,
	],
	'dev' => [
		'cachingEnabled' => false,
	]
];