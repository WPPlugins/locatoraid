<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
// $after['/layout/view/body@render'] = 'view/body@extend-render';
$before['/layout/view/body@top-header'] = 'view/body@remove-top-header';