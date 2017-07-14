<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Tiles_HC_MVC extends Html_View_Container_HC_MVC
{
	protected $per_row = 4;

	function set_per_row( $per_row )
	{
		$this->per_row = $per_row;
		return $this;
	}
	function per_row()
	{
		return $this->per_row;
	}

	function render()
	{
		$out = array();
		$items = $this->children();
		$per_row = $this->per_row();
		$number_of_rows = ceil( count($items) / $per_row );

		$row_class = 'row';
		switch( $per_row ){
			case 1:
				$tile_width = 12;
				break;
			case 2:
				$tile_width = 6;
				break;
			case 3:
				$tile_width = 4;
				break;
			case 4:
				$tile_width = 3;
				break;
			case 6:
				$tile_width = 2;
				break;
		}

		for( $ri = 0; $ri < $number_of_rows; $ri++ ){
			$row = $this->make('view/grid')
				;

			for( $ii = ($ri*$per_row); $ii < (($ri+1)*$per_row); $ii++ ){
				if( isset($items[$ii]) ){
					$row->add(
						$items[$ii],
						$tile_width
						);
				}
			}
			$out[] = $row;
		}

		$return = '';
		foreach( $out as $o ){
			$return .= $o;
		}
		return $return;
	}
}