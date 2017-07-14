<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class WordPress_Layout_Layout_View_Body_HC_MVC extends _HC_MVC
{
	public function after_render( $return )
	{
		$enqueuer = $this->make('/app/enqueuer');
		return $return;
	}
}