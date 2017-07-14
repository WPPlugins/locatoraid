<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$alias['/auth/lib']	= 'lib/auth';

// add username field for users list
$after['/users/view/index@prepare-header']	= 'view/index@extend-prepare-header';
$after['/users/view/index@prepare-row']		= 'view/index@extend-prepare-row';

