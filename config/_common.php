<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$config['app_version'] = '3.1.5';
// $config['dbprefix_version'] = 'v1';

$config['modules'] = array(
	'app',
	// 'api',
	'utf8',
	'http',
	'html',
	'input',
	'form',
	'validate',
	'security',
	'encrypt',
	'session',

	'msgbus',
	'flashdata',
	'layout',
	'root',
	'setup',
	'acl',
	'icons',
	// 'icons-chars',
	// 'icons-simple-line',
	'icons_dashicons',

	'code_snippets',
	'conf',
	'auth',

	'users',
	'print',
	'ormrelations',

	'modelsearch',

	'front',

	'locations',

	'geocode',
	'geocodebulk',

	'maps',
	'maps_google',

	'conf_fields',
	'search',

	'commands',
	);
