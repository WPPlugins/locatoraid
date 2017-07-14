<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Wordpress_Users_View_Layout_HC_MVC extends _HC_MVC
{
	public function after_menubar( $return )
	{
		$return->add(
			'settings',
			$this->make('/html/view/link')
				->to('/conf', array('tab' => 'wordpress-users'))
				->add( $this->make('/html/view/icon')->icon('cog') )
				->add( HCM::__('Settings') )
			);

	// ADD
		if( current_user_can('create_users') ){
			$link = admin_url( 'user-new.php' );
			$return->add(
				'add',
				$this->make('/html/view/link')
					->to($link)
					// ->add_attr('target', '_blank')
					->add( $this->make('/html/view/icon')->icon('plus') )
					->add( HCM::__('Add New') )
				);
		}

		return $return;
	}
}