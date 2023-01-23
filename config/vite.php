<?php

use craft\helpers\App;

return [
		'checkDevServer' => true,
		'devServerInternal' => 'http://localhost:3000',
		'devServerPublic' => App::env('CRAFT_VITE_URL') . ':3000',
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