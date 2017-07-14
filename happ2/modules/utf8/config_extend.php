<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$after['/input/lib/input@get']					= 'lib@clean-input';
$after['/input/lib/input@post']					= 'lib@clean-input';
$after['/input/lib/input@get-post']				= 'lib@clean-input';
$after['input/lib/input@cookie']				= 'lib@clean-input';
$after['/input/lib/input@server']				= 'lib@clean-input';
$after['/input/lib/input@user-agent']			= 'lib@clean-input';
$after['/input/lib/input@request-headers']		= 'lib@clean-input';
$after['/input/lib/input@get-request-header']	= 'lib@clean-input';
