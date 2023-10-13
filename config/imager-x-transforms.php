<?php

return [
	'heroImage' => [
		'displayName' => 'Hero Image',
		'transforms' => [
			['width' => 320],
			['width' => 640],
			['width' => 768],
			['width' => 1024],
			['width' => 1280],
			['width' => 1536],
			['width' => 1800],
		],
		'defaults' => [
			'ratio' => 16 / 9,
			'jpegQuality' => 80,
		],
		'configOverrides' => [
			'fillTransforms' => true,
			'fillInterval' => 300,
		],
	],
	'srcImg' => [
		'displayName' => '"src" Image',
		'transforms' => [
			['width' => 320]
		],
		'defaults' => [
			'ratio' => 16 / 9,
			'jpegQuality' => 60,
		]
	],
];
