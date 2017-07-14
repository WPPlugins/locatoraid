<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$config['locations_address:_label'] = HCM::__('Address Format');

$config['locations_address:format'] = array(
	'default' 	=> '
{STREET}
{CITY} {STATE} {ZIP}
{COUNTRY}
',
	);
