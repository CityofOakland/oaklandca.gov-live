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
				'aliases' => [
						'@algoliaAppId' => getenv('ALGOLIA_APP_ID'),
						'@algoliaAdminApi' => getenv('ALGOLIA_ADMIN_API'),
						'@amazonKeyId' => getenv('AMAZON_KEY_ID'),
						'@amazonSecret' => getenv('AMAZON_SECRET'),
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
				'securityKey' => getenv('SECURITY_KEY'),
				'siteUrl' => getenv('SITE_URL'),
				'useProjectConfigFile' => true,
		],

		'dev' => [
				'allowAdminChanges' => true,
				'devMode' => true,
				'enableTemplateCaching' => true,
		],

		'production' => [
				'siteUrl' => 'https://www.oaklandca.gov',
				'disallowRobots' => false,
				'defaultTokenDuration' => 432000,
		],
];
