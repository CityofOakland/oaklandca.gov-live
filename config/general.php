<?php

/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here. You can see a
 * list of the available settings in vendor/craftcms/cms/src/config/GeneralConfig.php.
 *
 * @see craft\config\GeneralConfig
 */

use craft\helpers\App;

return [
	// Global settings
		'*' => [
				'aliases' => [
                    '@algoliaAppId' => App::env('ALGOLIA_APP_ID'),
                    '@algoliaAdminApi' => App::env('ALGOLIA_ADMIN_API'),
                    '@amazonKeyId' => App::env('AMAZON_KEY_ID'),
                    '@amazonSecret' => App::env('AMAZON_SECRET'),
                    'siteUrl' => App::env('SITE_URL') ?: '@web',
                    'webroot' => App::env('WEB_ROOT'),
				],
				'allowAdminChanges' => false,
				'cpTrigger' => 'admin',
				'defaultWeekStartDay' => 0,
				'devMode' => false,
				'disallowRobots' => true,
				'enableCsrfProtection' => true,
				'extraFileKinds' => [
						'excel' => [
								'extensions' => ['csv']
						]
				],
				'maxRevisions' => 10,
				'maxUploadFileSize' => 268435456,
				'omitScriptNameInUrls' => true,
				'securityKey' => App::env('SECURITY_KEY'),
		],

		'dev' => [
				'allowAdminChanges' => true,
				'devMode' => true,
				'enableTemplateCaching' => true,
		],

		'production' => [
				'disallowRobots' => false,
				'defaultTokenDuration' => 432000,
		],
];
