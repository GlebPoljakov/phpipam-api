<?php

/**
 *	phpIPAM API class to work with Vlans
 *
 * @author: gleb.poljakov
 *
 */

class Vlans
{
	/* variables */
	private $_params;
	
	/* set parameters, provided via post
	 *
	 * available params:
	 *		number	- vlan id number, integer
	 *		vlanid	- id of record in db, integer
	 *
	 */
	public function __construct($params)
	{
		// verify vlanid
		$this->_params['vlanid'] = ( is_numeric($params['vlanid']) ) ? $params['vlanid'] : '';

		// verify number
		$this->_params['number'] = ( is_numeric($params['number']) ) ? $params['number'] : '';
	}

	/** 
	* create new vlans
	*/
	public function createVlans($_params)
	{
		/* not yet implemented */
		throw new Exception('Action not yet implemented');
	}

	/** 
	* read Vlans
	*/
	public function readVlans()
	{
		//checking for necessary params
		if (
			!is_numeric( $this->_params['vlanid'] ) &&
			!is_numeric( $this->_params['number'] )
		){
			throw new Exception('Parameters not provided.');
		}

		//init address class
		$vlan = new Vlan();
		
		//set params
		foreach ( $this->_params as $key=>$val )
			$vlan->$key = $val;

		//fetch results
		$res = $vlan->getVlan(); 

		return $res;
	}


	/** 
	* update existing Vlans
	*/
	public function updateVlans()
	{
		/* not yet implemented */
		throw new Exception('Action not yet implemented');
	}	
	
	
	/** 
	* delete Vlans
	*/
	public function deleteVlans()
	{
		/* not yet implemented */
		throw new Exception('Action not yet implemented');
	}
}

?>