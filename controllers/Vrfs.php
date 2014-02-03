<?php

/**
 *	phpIPAM API class to work with vrfs
 *
 * @author: gleb.poljakov
 *
 */

class Vrfs
{
	/* variables */
	private $_params;
	
	/* set parameters, provided via post
	 *
	 * available params:
	 *		rd	- vrf rd (array of two integer)
	 *		id	- vrf id (integer)
	 *
	 */
	public function __construct($params)
	{
		// {{{ verify params
			// verify id
			$this->_params['id'] = ( is_numeric($params['id']) ) ? $params['id'] : '';

			// {{{ verify RD
				$rd = explode(':',$params['rd']);
				if ( sizeof($rd)==2 )
				{
					if  (
									filter_var($rd[0],FILTER_VALIDATE_INT) &&
			    						filter_var($rd[1],FILTER_VALIDATE_INT)
					)
						$this->_params['rd'] = $rd;
					else
						throw new Exception('RD in wrong format. Must be [0-9]:[0-9].');
				}
			// }}}

		// }}}
	}

	/** 
	* create new vrf
	*/
	public function createVrfs($_params)
	{
		/* not yet implemented */
		throw new Exception('Action not yet implemented');
	}


	/** 
	* read Vrfs
	*/
	public function readVrfs()
	{
		//check for necessary params
		if (
			!is_numeric($this->_params['id']) &&
			sizeof( $this->_params['rd'] ) != 2
		){
			throw new Exception('Parameters not provided.');
		}

		//init address class
		$vrf = new Vrf();
		
		//set params
		foreach ( $this->_params as $key=>$val )
			$vrf->$key = $val;

		//fetch results
		$res = $vrf->getVrf(); 

		return $res;
	}


	/** 
	* update existing Vrfs
	*/
	public function updateVrfs()
	{
		/* not yet implemented */
		throw new Exception('Action not yet implemented');
	}	
	
	
	/** 
	* delete Vrfs
	*/
	public function deleteVrfs()
	{
		/* not yet implemented */
		throw new Exception('Action not yet implemented');
	}
}

?>