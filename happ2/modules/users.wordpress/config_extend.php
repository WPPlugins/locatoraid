<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$alias['/users/model'] = 'model/user';

// user list sidebar
$after['/users/zoom/view@menubar']	= 'view/zoom/menubar@extend-render';

$after['/users/zoom/form/edit@-init']	= 'form@extend';
$after['/users/index/view/layout@menubar'] = 'users/view/layout@after-menubar';

$after['/users/index/view@header'] = 'users/view@after-header';
$after['/users/index/view@row'] = 'users/view@after-row';
