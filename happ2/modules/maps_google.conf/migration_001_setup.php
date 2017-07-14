<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Maps_Google_Conf_Setup_HC_Migration extends HC_Migration
{
	public function up()
	{
		if( ! $this->db->table_exists('conf') ){
			return;
		}

		$q = $this->db
			->where('name', 'mapsapi')
			->get('conf')
			;

		$insert = array();
		foreach( $q->result() as $row ){
			if( $row && $row->value ){
				$q2 = $this->db
					->where('name', 'maps_google:api_key')
					->get('conf')
					;
				if( ! $q2->result() ){
					$insert = array(
						'name'	=> 'maps_google:api_key',
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