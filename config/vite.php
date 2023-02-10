<?php

use craft\helpers\App;

return [
		'checkDevServer' => true,
		'devServerInternal' => 'http://localhost:3000',
		'devServerPublic' => App::env('VITE_URL'),
		'serverPublic' => '/dist/',
		'useDevServer' => App::env('ENVIRONMENT') === 'dev',
		'manifestPath' => '@webroot/dist/manifest.json',
		'errorEntry' => 'src/js/app.js',
		'cacheKeySuffix' => '',
		'includeReactRefreshShim' => false,
		'includeModulePreloadShim' => true,
		'criticalPath' => '@webroot/dist/criticalcss',
		'criticalSuffix' => '_critical.min.css',
];