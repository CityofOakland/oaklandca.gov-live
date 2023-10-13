<?php

return [
	'enabled'       => getenv('SENTRY_DSN') ?: false,
	'anonymous'     => true,
	'clientDsn'     => getenv('SENTRY_DSN') ?: null,
	'excludedCodes' => ['400', '404', '429'],
	'release'       => getenv('SENTRY_RELEASE') ?: null,
];