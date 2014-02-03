<?php

/**
 *	phpIPAM Vrf class
 */

class Vrf
{	
	// {{{ properties:
	var $id = 0;			//integer
	var $rd = array();		//array of integer
	// }}}

	// {{{ Get vrf details
	public function getVrf() {
		// {{{ Validate params
		if (
			! filter_var($this->id, FILTER_VALIDATE_INT) &&
			( 
			    ( sizeof($this->rd)==2 )
				?
					!(
						filter_var($this->rd[0], FILTER_VALIDATE_INT) &&
						filter_var($this->rd[1], FILTER_VALIDATE_INT)
					)
				:
					false
			)
		) {
			throw new Exception('Invalid parameter.');
		}
		// }}}

		// {{{ get vrf details
			//if ByID:
			if ( $this->id > 0 ){
				$res = getVRFDetailsById($this->id);
			}
			//if ByRD:
			elseif ( sizeof($this->rd)==2 ) {
				$res = getVRFDetailsByRD($this->rd[0].':'.$this->rd[1]);
			}

			//throw new exception if not existing
			if( ! $res )
				 throw new Exception('Vrf not exist.');

			//create object from results
			foreach($res as $key=>$line)
				$this->$key = $line;
		// }}}

		//return result
		return $res;
	}
	// }}}

	/**
	* function to return multidimensional array
	*/
	public function toArray($obj, $format)
	{
		//if object create array
		if(is_object($obj)) $obj = (array) $obj;
		if(is_array($obj)) {
			$arr = array();
			foreach($obj as $key => $val) {
				// output format
				$arr[$key] = $this->toArray($val, $format);
			}
		}
		else { 
			$arr = $obj;
		}
		//return an array of items
		return $arr;
	}
}
