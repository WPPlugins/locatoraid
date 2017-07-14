<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Wordpress_View_Zoom_Menubar_HC_MVC extends _HC_MVC 
{
	public function extend_render( $menubar, $args, $src )
	{
		$model = $src->model();
		$id = $model['id'];

	// EDIT WORDPRESS ACCOUNT
		$link = get_edit_user_link( $id );
		$menubar->add(
			'editwp',
			$this->make('/html/view/link')
				->to($link)
				->add_attr('target', '_blank')
				->add( HCM::__('Edit WordPress User Account') )
			);

		return $menubar;
	}
}