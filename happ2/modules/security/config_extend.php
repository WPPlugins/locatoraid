<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$after['/html/view/form@render'] = 'view/form@extend-csrf';

// $filter['/input/lib/-init'][]				= 'lib@csrf-verify';
// $filter['/input/lib/get'][]					= 'lib@clean-input';
// $filter['/input/lib/post'][]				= 'lib@clean-input';
// $filter['/input/lib/get-post'][]				= 'lib@clean-input';
// $filter['/input/lib/cookie'][]				= 'lib@clean-input';
// $filter['/input/lib/server'][]				= 'lib@clean-input';
// $filter['/input/lib/user-agent'][]			= 'lib@clean-input';
// $filter['/input/lib/request-headers'][]		= 'lib@clean-input';
// $filter['/input/lib/get-request-header'][]	= 'lib@clean-input';
