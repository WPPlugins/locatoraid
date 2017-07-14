<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Commands_Manager_HC_MVC extends _HC_MVC
{
	public function single_instance()
	{
	}

	public function get( $id )
	{
		$return = NULL;

		$config_loader = $this->make('/app/lib/config-loader');
		$config = $config_loader->get('commands');

		if( array_key_exists($id, $config) ){
			$return = $this->make($config[$id]);
		}
		else {
			echo "COMMAND '$id' IS NOT AVAILABLE<br>";
		}
		return $return;
	}

	public function all( $class, $model )
	{
		$commands = $this->commands;
		$compare = $this->make('/app/lib/compare');

		$return = array();
		reset( $commands );
		foreach( $commands as $k => $command )
		{
			list( $command_class, $command_command ) = explode('/', $k, 2);
			if( $command_class != $class ){
				continue;
			}

			$which = array();
			if( method_exists($command, 'which') ){
				$which = call_user_func( array($command, 'which') );
			}

			$on = $compare->is_valid( $model, $which );
			if( $on ){
				$return[$k] = $command;
			}
		}

		return $return;
	}
}