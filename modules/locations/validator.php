<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Validator_LC_HC_MVC extends _HC_Validator
{
	public function prepare( $values )
	{
		$return = array();
		$id = isset($values['id']) ? $values['id'] : NULL;

		$return['name'] = array(
			'required'	=> array( $this->make('/validate/required') ),
			'maxlen'	=> array( $this->make('/validate/maxlen'), 250 ),
			// 'unique'	=> array( $this->make('/validate/unique'), 'locations', 'name', $id ),
			);

		return $return;
	}
}