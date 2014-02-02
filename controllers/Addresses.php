<?php

/**
 *	phpIPAM API class to work with addressess
 *
 * @author: gleb.poljakov
 *
 */

class Addresses
{
	/* variables */
	private $_params;
	
	/* set parameters, provided via post
	 *
	 * available params:
	 *		format	- format of ip-address representation
	 *		ip	- ip-address on format of $param['format']
	 *
	 */
	public function __construct($params)
	{
		$this->_params = $params;
		
		//ip address format, can be decimal or ip
		if (!$this->_params['format'])
			$this->_params['format'] = "decimal";

		//verify IP address format, it must be 'decimal' or 'ip'
		if (
			!(
			    $this->_params['format'] == "decimal" ||
			    $this->_params['format'] == "ip"
			)
		)
		    throw new Exception('Invalid format');
	}

	/** 
	* create new address
	*/
	public function createAddresses($_params)
	{
		/* not yet implemented */
		throw new Exception('Action not yet implemented');
	}


	/** 
	* read addresses
	*/
	public function readAddresses()
	{
		//init address class
		$address = new Address();
		
		//set IP address format
		$address->format = $this->_params['format'];
		//set ip-address
		$address->ip = $this->_params['ip'];

		//fetch results
		$res = $address->getAddress(); 
		
		//return address
		return $res;
	}


	/** 
	* update existing subnet 
	*/
	public function updateAddresses()
	{
		/* not yet implementes */
		throw new Exception('Action not yet implemented');
	}	
	
	
	/** 
	* delete subnet 
	*/
	public function deleteAddresses()
	{
		/* not yet implementes */
		throw new Exception('Action not yet implemented');
	}
}

/* debug

include_once '../../functions/functions.php';
include_once '../models/address.php';

$params['format'] = 'ip';
$params['ip'] = "192.168.1.1";

$controller = new Addresses ($params);
$action = 'readAddresses';
print "AAA";
if (method_exists($controller,$action)) {
	print "true";

	print_r($controller->$action());

} else { print "False";};
*/

?>