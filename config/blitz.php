<?php

/**
 * @copyright Copyright (c) PutYourLightsOn
 */

/**
 * Blitz config.php
 *
 * This file exists only as a template for the Blitz settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'blitz.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

return [
  '*' => [
    // With this setting enabled, Blitz will begin caching pages according to the included/excluded URI patterns. Disable this setting to prevent Blitz from caching any new pages.
    'cachingEnabled' => true,

    // The URI patterns to include in caching. Set `siteId` to a blank string to indicate all sites.
    'includedUriPatterns' => [
      ['siteId' => 1, 'uriPattern' => '.*',],
    ],

    // The URI patterns to include in caching. Set `siteId` to a blank string to indicate all sites.
    'excludedUriPatterns' => [
      ['siteId' => 1, 'uriPattern' => '/author'],
      ['siteId' => 1, 'uriPattern' => '/meetings'],
      ['siteId' => 1, 'uriPattern' => '/boards-commissions/*/meetings'],
      ['siteId' => 1, 'uriPattern' => '/events'],
      ['siteId' => 1, 'uriPattern' => '/departments/*/events'],
      ['siteId' => 1, 'uriPattern' => '/topics/*/events'],
    ],

    // Whether the cache should automatically be warmed after clearing.
    'warmCacheAutomatically' => false,
  ],

  'dev' => [
    'cachingEnabled' => false,
  ],

  'staging' => [
    'cachingEnabled' => true,
  ],
];
