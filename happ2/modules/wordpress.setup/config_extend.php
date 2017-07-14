<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$alias['/setup/form/setup']			= 'form';
$alias['/setup/view/form']			= 'view/form';
$alias['/setup/validator/setup']	= 'validator';

$after['/setup/controller/run@do-setup'] = 'controller@extend-do-setup';