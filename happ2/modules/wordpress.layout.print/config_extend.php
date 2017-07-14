<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$after['/layout/view/body@render'] = array(
	'view/full@extend-body'
	);

$after['/app/enqueuer@get-scripts']		= 'app/enqueuer@after-get-scripts';
$after['/app/enqueuer@get-styles']		= 'app/enqueuer@after-get-styles';
