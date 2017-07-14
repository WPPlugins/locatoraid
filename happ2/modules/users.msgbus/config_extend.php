<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$after['/users/model@save']	= 'controller/message@extend-message';
$after['/users/model@delete']	= 'controller/message@extend-message';
