<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$before['/html/view/top-menu@children'] = 'top-menu@before-children';

$after['view/zoom/index@render'] = array(
	'view/zoom/layout@extend-index',
	);
$after['view/new/index@render'] = array(
	'view/new/layout@extend-index',
	);
