<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$after['/locations/model@save'] 	= 'controller/message@extend-message';
$after['/locations/model@delete']	= 'controller/message@extend-message';
