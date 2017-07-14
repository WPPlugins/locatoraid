<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Coordinates_Locations_Model_LC_HC_MVC extends _HC_MVC
{
	public function before_fetch_many( $args, $src )
	{
		if( isset($src->where['lat']) && isset($src->where['lng']) ){
			$mylat = $src->where['lat'][0][1];
			$mylng = $src->where['lng'][0][1];

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

			$fetch_fields = $src->get_fetch_fields();
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
			$src->fetch_fields( $fetch_fields );

			$src_order_by = $src->get_order_by();
			$order_by = array(
				'computed_distance'	=> 'ASC'
				);

			$order_by = array_merge( $order_by, $src_order_by );
			$src->set_order_by( $order_by );

			unset($src->where['lat']);
			unset($src->where['lng']);

			// $this->where('1', 'OR', '1', FALSE); 
		}
	}

	public function before_count( $args, $src )
	{
		if( isset($src->where['lat']) && isset($src->where['lng']) && isset($src->having['computed_distance']) ){
			$mylat = $src->where['lat'][0][1];
			$mylng = $src->where['lng'][0][1];

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

			$key = "
				DEGREES(
				ACOS(
					SIN(RADIANS(latitude)) * SIN(RADIANS($mylat))
				+	COS(RADIANS(latitude)) * COS(RADIANS($mylat))
				*	COS(RADIANS(longitude - ($mylng)))
				) * 60 * $nau2measure
				)
				";

			$src->where[$key] = $src->having['computed_distance'];

			unset($src->where['osearch']);
			unset($src->where['lat']);
			unset($src->where['lng']);
			unset($src->having['computed_distance']);

			// $this->where('1', 'OR', '1', FALSE); 
		}
	}
}