<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Flashdata_Http_Controller_HC_MVC extends _HC_MVC
{
	// inserts a flash message from msgbus
	public function before_render( $args, $src )
	{
		$redirect = $src->redirect();
		if( ! $redirect ){
			return;
		}

		$msgbus = $this->make('/msgbus/lib');
		$session = $this->make('/session/lib');

		$msg = $msgbus->get('message');
		if( $msg ){
			$session->set_flashdata('message', $msg);
		}
		$error = $msgbus->get('error');
		if( $error ){
			$session->set_flashdata('error', $error);
		}
		$warning = $msgbus->get('warning');
		if( $warning ){
			$session->set_flashdata('warning', $warning);
		}
		$debug = $msgbus->get('debug');
		if( $debug ){
			$session->set_flashdata('debug', $debug);
		}
	}

	public function extend_render( $return )
	{
		$msgbus = $this->make('/msgbus/lib');
		$session = $this->make('/session/lib');

		$msg = $msgbus->get('message');
		if( $msg ){
			$session->set_flashdata('message', $msg);
		}
		$error = $msgbus->get('error');
		if( $error ){
			$session->set_flashdata('error', $error);
		}
		$warning = $msgbus->get('warning');
		if( $warning ){
			$session->set_flashdata('warning', $warning);
		}
		$debug = $msgbus->get('debug');
		if( $debug ){
			$session->set_flashdata('debug', $debug);
		}

		return $return;
	}
}