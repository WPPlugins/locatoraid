<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Zoom_View_Layout_HC_MVC extends _HC_MVC
{
	public function header( $model )
	{
		$presenter = $this->make('presenter')
			->set_data($model)
			;
		$return = $presenter->run('present-title');
		return $return;
	}

	public function menubar( $model )
	{
		$menubar = $this->make('/html/view/container');
		return $menubar;
	}

	public function render( $content, $model )
	{
		$menubar = $this->run('menubar', $model);
		$header = $this->run('header', $model);

		$out = $this->make('/layout/view/content-header-menubar')
			->set_content( $content )
			->set_header( $header )
			->set_menubar( $menubar )
			;

		return $out;
	}
}