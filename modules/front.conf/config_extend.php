<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$before['/conf/index/controller@form'] = 'controller@before-form';
$after['/app/lib/settings@get'] = 'app/lib/settings@after-get';
