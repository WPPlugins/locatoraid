<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Conf_Setup_HC_Migration extends HC_Migration
{
	public function up()
	{
		if( $this->db->table_exists('conf') ){
			return;
		}

		// conf
		$this->dbforge->add_field(
			array(
				'id' => array(
					'type' => 'INT',
					'null' => FALSE,
					'unsigned' => TRUE,
					'auto_increment' => TRUE
					),
				'name' => array(
					'type' => 'VARCHAR(255)',
					'null' => FALSE,
					),
				'value' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),
				)
			);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('conf');
	}

	public function down()
	{
	}
}