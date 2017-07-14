<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$after['/locations/new/form@-init'] = 'locations/form@after-init';
$after['/locations/edit/form@-init'] = 'locations/form@after-init';

$after['/locations/index/view@prepare-header'] = 'locations/view@after-prepare-header';
$after['/locations/index/view@prepare-row'] = 'locations/view@after-prepare-row';

$after['/locations/presenter@fields'] = 'presenter@extend-fields';

$before['/locations/model@fetch-many'] = 'locations/model@before-fetch-many';
$after['/locations/presenter@present-front'] = 'presenter@after-present-front';

