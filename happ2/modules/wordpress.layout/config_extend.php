<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$after['/layout/view/body@render'] = 'layout/view/body@after-render';
$after['/layout/view/content-header-menubar@render'] = 'layout/view/content-header-menubar@after-render';

$after['/app/enqueuer@register-script']	= 'app/enqueuer@after-register-script';
$after['/app/enqueuer@enqueue-script']	= 'app/enqueuer@after-enqueue-script';
$after['/app/enqueuer@register-style']	= 'app/enqueuer@after-register-style';
$after['/app/enqueuer@enqueue-style']	= 'app/enqueuer@after-enqueue-style';

$after['/app/enqueuer@localize-script']	= 'app/enqueuer@after-localize-script';
