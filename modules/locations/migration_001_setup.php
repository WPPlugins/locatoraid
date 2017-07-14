<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Setup_LC_HC_Migration extends HC_Migration
{
	public function up()
	{
		if( $this->db->table_exists('locations') ){
			return;
		}

		$this->dbforge->add_field(
			array(
				'id' => array(
					'type' => 'INT',
					'null' => FALSE,
					'unsigned' => TRUE,
					'auto_increment' => TRUE
					),
				'name' => array(
					'type' => 'VARCHAR(100)',
					'null' => FALSE,
					),
				'street1' => array(
					'type' => 'VARCHAR(255)',
					),
				'street2' => array(
					'type' => 'VARCHAR(255)',
					),
				'city' => array(
					'type' => 'VARCHAR(50)',
					'null' => TRUE,
					),
				'state' => array(
					'type' => 'VARCHAR(20)',
					'null' => TRUE,
					),
				'zip' => array(
					'type' => 'VARCHAR(20)',
					'null' => TRUE,
					),
				'country' => array(
					'type' => 'VARCHAR(50)',
					'null' => TRUE,
					),

				'phone' => array(
					'type' => 'VARCHAR(30)',
					'null' => TRUE,
					),
				'website' => array(
					'type' => 'VARCHAR(100)',
					'null' => TRUE,
					),
				)
			);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('locations');
	}

	public function down()
	{
		if( $this->db->table_exists('locations') ){
			$this->dbforge->drop_table('locations');
		}
	}
}