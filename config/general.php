<?php

/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here. You can see a
 * list of the available settings in vendor/craftcms/cms/src/config/GeneralConfig.php.
 *
 * @see craft\config\GeneralConfig
 */

return [
	// Global settings
		'*' => [
				'siteUrl' => getenv('SITE_URL'),
				'defaultWeekStartDay' => 0,
				'enableCsrfProtection' => true,
				'omitScriptNameInUrls' => true,
				'cpTrigger' => 'admin',
				'maxUploadFileSize' => 268435456,
				'useProjectConfigFile' => true,
				'securityKey' => getenv('SECURITY_KEY'),
				'maxRevisions' => 10,
				'devMode' => false,
				'allowAdminChanges' => false,
				'disallowRobots' => true,
				'extraFileKinds' => [
						'excel' => [
								'extensions' => ['csv']
						]
				],
				'aliases' => [
						'@algoliaAppId' => getenv('ALGOLIA_APP_ID'),
						'@algoliaAdminApi' => getenv('ALGOLIA_ADMIN_API'),
						'@amazonKeyId' => getenv('AMAZON_KEY_ID'),
						'@amazonSecret' => getenv('AMAZON_SECRET'),
				],
		],

		'dev' => [
				'devMode' => true,
				'allowAdminChanges' => true,
		],

		'production' => [
			// Base site URL
				'siteUrl' => 'https://www.oaklandca.gov',
			// Changes the default preview duration from 24 hours to 5 days
				'defaultTokenDuration' => 432000,
				'disallowRobots' => false
		],
];
