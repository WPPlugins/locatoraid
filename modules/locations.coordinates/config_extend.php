<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$after['/locations/index/view@prepare-header'] = 'locations/view@after-prepare-header';
$after['/locations/index/view@prepare-row'] = 'locations/view@after-prepare-row';

$after['/locations/edit/view/layout@menubar'] = 'locations-zoom/view/layout@after-menubar';
$after['/locations/edit/view@render'] = 'locations-zoom/view@after-render';

$before['/locations/edit/controller@route-index'] = 'locations-zoom/controller@before-route-index';

$before['/locations/model@fetch-many'] = 'locations/model@before-fetch-many';
$before['/locations/model@count'] = 'locations/model@before-count';
