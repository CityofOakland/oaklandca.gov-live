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
        // With this setting enabled, Blitz will begin caching pages according to the included/excluded URI patterns.
        // Disable this setting to prevent Blitz from caching any new pages.
        'cachingEnabled' => getenv('BLITZ_CACHING_ENABLE'),
        // The value to send in the cache control header for cached pages.
        'cacheControlHeader' => 'public, s-maxage=31536000, max-age=0',
        // The value to send in the cache control header for expired pages.
        'cacheControlHeaderExpired' => 'public, s-maxage=5, max-age=0',
	]
];
