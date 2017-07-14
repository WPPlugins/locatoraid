<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
// profile info or login link
$after['/layout/view/body@top-header']	= 'controller@extend-top-menu';