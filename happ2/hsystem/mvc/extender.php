<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class MVC_Extender_HC_System
{
	private $extend = array();
	private $use_re = 0;
	private $extend_cache = array();

	public function __construct( $extend = array() )
	{
		if( $extend ){
			$this->extend = $this->_prepare_extend( $extend );
		}
	}

	private function _prepare_extend( $extend )
	{
		if( ! $this->use_re ){
			return $extend;
		}

		$convert_keys_to_re = array('before', 'after');
		foreach( $convert_keys_to_re as $k ){
			$keys = array_keys( $extend[$k] );

			foreach( $keys as $k2 ){
				if( count($extend[$k][$k2]) > 1 ){
					ksort( $extend[$k][$k2] );
				}
				if( strpos($k2, '*') !== FALSE ){
					$new_k = HC_Lib2::asterisk_to_re($k2);
					$extend[$k][$new_k] = $extend[$k][$k2];
					unset($extend[$k][$k2]);
				}
			}
		// sort by key length
			// array_multisort( array_map('strlen', array_keys($new)), SORT_DESC, $new );
		}
// _print_r( $extend['after'] );
		return $extend;
	}

	public function use_re()
	{
		return $this->use_re;
	}

	public function get( $slug, $slug2 = NULL )
	{
		$return = array();
		if( $return_before = $this->_get($slug, 'before', $slug2) ){
			$return['before'] = $return_before;
		}
		if( $return_after = $this->_get($slug, 'after', $slug2) ){
			$return['after'] = $return_after;
		}
		if( $return_alias = $this->_get($slug, 'alias', $slug2) ){
			$return['alias'] = $return_alias;
		}
		return $return;
	}

	private function _get( $slug, $when, $slug2 = NULL ) // before or after
	{
		if( $slug2 ){
			$slug = $slug . '/--' . $slug2;
		}

		// if( isset($extend_cache[$when][$slug]) ){
		if( array_key_exists($when, $this->extend_cache) &&  array_key_exists($slug, $this->extend_cache[$when]) ){
			$return = $this->extend_cache[$when][$slug];
		}
		else {
// echo "GET EXTEND FOR '$slug': $when<br>";
			if( defined('NTS_PROFILER') ){
				global $_NTS_PROFILER;
				if( ! isset($_NTS_PROFILER['SEARCH_FILTER_CALLABLES']) ){
					$_NTS_PROFILER['SEARCH_FILTER_CALLABLES'] = 0;
				}
				$_NTS_PROFILER['SEARCH_FILTER_CALLABLES']++;
			}

			$return = array();

			$extend = $this->extend[$when];
			$use_re = $this->use_re();

			if( $use_re ){
				foreach( $extend as $fk => $this_callables ){
					$take_this = FALSE;
				// is re?
					if( substr($fk, -2) == '/i' ){
						if( preg_match($fk, $slug) ){
							$take_this = TRUE;
						}
					}
					else {
						if( $fk == $slug ){
							$take_this = TRUE;
						}
					}

					if( $take_this ){
						$return = array_merge( $return, $this_callables );
					}
				}
			}
			else {
				if( isset($extend[$slug]) ){
					$return = $extend[$slug];
				}

				$also_slug = substr($slug, strpos($slug, '@'));
				if( $dash_pos = strrpos($also_slug, '-') ){
					$also_slug = substr($also_slug, 0, $dash_pos + 1 ) . '*';
					if( isset($extend[$also_slug]) ){
						$return = array_merge( $return, $extend[$also_slug] );
					}
				}
			}

			if( count($return) > 1 ){
				ksort( $return );
			}
			$this->extend_cache[$when][$slug] = $return;
		}
		return $return;
	}

	public function extend()
	{
		return $this->extend;
	}

	public function add( $when, $k, $what ) // when = after|before
	{
		if( ! isset($this->extend[$when][$k]) ){
			$this->extend[$when][$k] = array();
		}
		$this->extend[$when][$k][] = $what;
		return $this;
	}
}