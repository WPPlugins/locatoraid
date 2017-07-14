<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Model_LC_HC_MVC extends _HC_ORM
{
	protected $table = 'locations';
	protected $default_order_by = array(
		'name'	=> 'ASC',
		);
	protected $search_in = array('name', 'street1', 'street2', 'city', 'state', 'zip', 'country');

	public function fetch_many_()
	{
		if( isset($this->where['lat']) && isset($this->where['lng']) ){
			$mylat = $this->where['lat'][0][1];
			$mylng = $this->where['lng'][0][1];

			$app_settings = $this->make('/app/lib/settings');
			$measure = $app_settings->run('get', 'core:measure');

		/* miles */
			if( $measure == 'mi' ){
				$nau2measure = 1.1508;
				$per_grad = 69;
			}
		/* km */
			else {
				$nau2measure = 1.852; 
				$per_grad = 111.04;
			}

			$fetch_fields = $this->get_fetch_fields();
			if( ! is_array($fetch_fields) ){
				$fetch_fields = array( $fetch_fields );
			}

			$add_fetch_fields = array("
				DEGREES(
				ACOS(
					SIN(RADIANS(latitude)) * SIN(RADIANS($mylat))
				+	COS(RADIANS(latitude)) * COS(RADIANS($mylat))
				*	COS(RADIANS(longitude - ($mylng)))
				) * 60 * $nau2measure
				) AS computed_distance
				");

			$fetch_fields = array_merge( $fetch_fields, $add_fetch_fields );
			$this->fetch_fields( $fetch_fields );

			$order_by = array(
				'computed_distance'	=> 'ASC'
				);
			$this->order_by = array_merge( $order_by, $this->order_by );

			unset($this->where['lat']);
			unset($this->where['lng']);

			// $this->where('1', 'OR', '1', FALSE); 
		}

		return parent::fetch_many();
	}

	public function get_places()
	{
		$this
			->distinct()
			->fetch_fields( 'CONCAT_WS(":", TRIM(country), TRIM(state), TRIM(city) ) AS location' )
			->order_by( 'location', 'ASC' )
			;
		return $this->fetch_array();
	}

	public function get_notgeocoded()
	{
		$this
			->where( 'latitude', '=', NULL )
			;
		return $this->fetch_array();
	}
}