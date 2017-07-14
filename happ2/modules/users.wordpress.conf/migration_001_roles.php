<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_WordPress_Conf_Roles_HC_Migration extends HC_Migration
{
	public function up()
	{
		if( ! $this->db->table_exists('conf') ){
			return;
		}

		$q = $this->db
			->select('*')
			->get('conf')
			;
		$current_conf = array();
		foreach( $q->result() as $row ){
			if( substr($row->name, 0, strlen('wordpress_users:role_')) != 'wordpress_users:role_' ){
				continue;
			}
			$current_conf[ $row->name ] = $row->value;
		}

		$change_to = array();

		reset( $current_conf );
		foreach( $current_conf as $k => $v ){
			switch( $v ){
				case 'none':
					$change_to[$k] = 0;
					break;
				case 'admin':
					$change_to[$k] = 1;
					break;
			}
		}

		reset( $change_to );
		foreach( $change_to as $k => $v ){
			$item = array(
				'value'	=> $v
				);
			$this->db->where('name', $k);
			$this->db->update('conf', $item);
		}
	}

	public function down()
	{
	}
}