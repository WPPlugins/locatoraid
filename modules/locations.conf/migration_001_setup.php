<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Conf_Setup_LC_HC_Migration extends HC_Migration
{
	public function up()
	{
		if( ! $this->db->table_exists('conf') ){
			return;
		}

		$q = $this->db
			->where('name', 'address_format')
			->get('conf')
			;

		$insert = array();
		foreach( $q->result() as $row ){
			if( $row && $row->value ){
				$q2 = $this->db
					->where('name', 'locations_address:format')
					->get('conf')
					;
				if( ! $q2->result() ){
					$insert = array(
						'name'	=> 'locations_address:format',
						'value'	=> $row->value,
						);
				}
				break;
			}
		}

		if( $insert ){
			$this->db->insert('conf', $insert);
		}
	}

	public function down()
	{
	}
}