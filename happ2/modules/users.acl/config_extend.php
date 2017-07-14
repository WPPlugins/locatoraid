<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
// $after['/acl/manager@can']			= 'manager@after-can';
// $after['/acl/manager@which']		= 'manager@after-which';
// $after['/acl/manager@link-check']	= 'manager@after-link-check';

// $after['/users/api@-init']	= 'api@after-init';

$after['/root/controller@link-check']		= 'controller/acl@extend-link-check';
