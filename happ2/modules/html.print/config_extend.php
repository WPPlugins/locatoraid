<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$before['/html/view/date-nav@render'] = 'view/date-nav@print-view';
$before['/html/view/link@render'] = 'view/link@print-view';
$before['/html/view/select-links@render'] = 'view/select-links@print-view';