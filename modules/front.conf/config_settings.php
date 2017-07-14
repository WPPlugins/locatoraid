<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$config['core:_label'] = HCM::__('Configuration');
$config['core:measure'] = array(
	'label'		=> HCM::__('Measure Units'),
	'default' 	=> 'mi',
	'type'		=> 'radio',
	'options'	=> array(
		'mi'	=> HCM::__('Miles'),
		'km'	=> HCM::__('Km'),
		)
	);

$config['fields:_label'] = HCM::__('Locations Details');

$config['fields:name:use'] = TRUE;
$config['fields:name:label'] = '';
$config['fields:address:use'] = TRUE;
$config['fields:address:label'] = '';
$config['fields:distance:use'] = 1;
$config['fields:distance:label'] = '';
$config['fields:website:label'] = '';
$config['fields:website:use'] = 1;
$config['fields:phone:label'] = HCM::__('Phone');
$config['fields:phone:use'] = 1;

$config['front_map:_label'] = HCM::__('Details On Map');
$config['front_map:advanced'] = 0;
$config['front_map:template'] = '';

$config['front_map:name:show'] = 1;
$config['front_map:name:w_label'] = FALSE;
$config['front_map:address:show'] = 1;
$config['front_map:address:w_label'] = FALSE;
$config['front_map:distance:show'] = 1;
$config['front_map:distance:w_label'] = 0;
$config['front_map:website:show'] = 1;
$config['front_map:website:w_label'] = FALSE;
$config['front_map:phone:show'] = 1;
$config['front_map:phone:w_label'] = 1;

$config['front_list:_label'] = HCM::__('Details In List');
$config['front_list:advanced'] = 0;
$config['front_list:template'] = '';

$config['front_list:name:show'] = 1;
$config['front_list:name:w_label'] = FALSE;
$config['front_list:address:show'] = 1;
$config['front_list:address:w_label'] = FALSE;
$config['front_list:distance:show'] = 1;
$config['front_list:distance:w_label'] = 0;
$config['front_list:website:show'] = 1;
$config['front_list:website:w_label'] = FALSE;
$config['front_list:phone:show'] = 1;
$config['front_list:phone:w_label'] = 1;
