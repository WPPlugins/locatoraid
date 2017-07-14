<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Promo_Wordpress_Bootstrap_LC_HC_MVC extends _HC_MVC
{
	public function run()
	{
		add_action( 'admin_notices', array($this, 'notices') );

	// link 
		// $label = HCM::__('Pro');
		// $app_title = isset($this->app->app_config['nts_app_title']) ? $this->app->app_config['nts_app_title'] : 'Store Locator';
		// $app_title_pro = $app_title . 'Locatoraid Pro';
		// $label = 'Locatoraid Pro';

		// $to = 'http://www.locatoraid.com/order/';
		// $link = $this->make('/html/view/link')
			// ->to($to)
			// ->add( $this->make('/html/view/icon')->icon('star') )
			// ->add( $label )
			// ->new_window( TRUE )
			// ;

		// $top_menu = $this->make('/html/view/top-menu')
			// ->add( 'promo2', $link )
			// ->set_child_order( 'promo2', 200 )
			// ;
	}

	public function notices()
	{
		$is_me = $this->make('/app/lib')
			->run('is-me')
			;
		if( ! $is_me ){
			return;
		}

		$out = $this->make('/html/view/element')->tag('div')
			->add_attr('class', 'notice' )
			// ->add_attr('class', 'notice-success' )
			->add_attr('class', 'hc-p2')
			;

		ob_start();
		require( dirname(__FILE__) . '/view.html.php' );
		$view = ob_get_contents();
		ob_end_clean();

		$out->add( $view );

		echo $out;
	}
}