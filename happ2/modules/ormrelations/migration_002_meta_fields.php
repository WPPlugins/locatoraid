<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class ORMRelations_Meta_Fields_HC_Migration extends HC_Migration
{
	public function up()
	{
		if( $this->db->field_exists('meta1', 'relations') ){
			return;
		}

		for( $ii = 1; $ii <= 3; $ii++ ){
			$this->dbforge->add_column(
				'relations',
				array(
					'meta' . $ii => array(
						'type'	=> 'VARCHAR(64)',
						'null'	=> TRUE,
						),
					)
				);
		}
	}

	public function down()
	{
	}
}