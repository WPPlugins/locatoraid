<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Priority_Setup_LC_HC_Migration extends HC_Migration
{
	public function up()
	{
		if( $this->db->field_exists('priority', 'locations') ){
			return;
		}

		$this->dbforge->add_column(
			'locations',
			array(
				'priority' => array(
					'type' 		=> 'INT',
					'null'		=> FALSE,
					'default'	=> 0
					),
				)
			);
	}

	public function down()
	{
	}
}