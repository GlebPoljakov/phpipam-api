<?php

/**
 *	phpIPAM Vlan class
 */

class Vlan
{	
	// {{{ properties:
	var $vlanid = 0;		//integer, record id in db table
	var $number = 0;		//integer, vlan id number
	// }}}

	// {{{ Get vlan details
	public function getVlan() {
		// {{{ Validate params
		if (
			! filter_var($this->vlanid, FILTER_VALIDATE_INT) &&
			! filter_var($this->number, FILTER_VALIDATE_INT)
		) {
			throw new Exception('Invalid parameter.');
		}
		// }}}

		// {{{ get vlan details
			//if ByID:
			if ( $this->vlanid > 0 ){
				$res = getVLANById ( $this->vlanid );
			}
			//if ByNumber:
			elseif ( $this->number > 0 ) {
				$res = getVLANByNumber ( $this->number );
			}
			//throw new exception if not existing
			if( ! $res )
				 throw new Exception('Vlan not existed');

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
