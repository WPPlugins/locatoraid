<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Coordinates_LC_HC_Migration extends HC_Migration
{
	public function up()
	{
		if( $this->db->field_exists('latitude', 'locations') ){
			return;
		}

		$this->dbforge->add_column(
			'locations',
			array(
				'latitude' => array(
					'type' 	=> 'DOUBLE',
					'null'	=> TRUE,
					),
				'longitude' => array(
					'type' 	=> 'DOUBLE',
					'null'	=> TRUE,
					),
				)
			);
	}

	public function down()
	{
	}
}