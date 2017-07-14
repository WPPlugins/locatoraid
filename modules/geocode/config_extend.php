<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$after['/locations/edit/view@render'] = 'locations-zoom/view@after-render';
$before['/locations/edit/controller@route-index'] = 'locations-zoom/controller@before-route-index';