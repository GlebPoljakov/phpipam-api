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
		// {{{ validate all params

			//verify IP address format, it must be 'decimal' or 'ip'
			if (
				$params['format'] == "decimal" ||
				$params['format'] == "ip"
			)
				$this->_params['format'] = $params['format'];
			else
				throw new Exception('Invalid format. Format will be [decimal|ip].');

			//get ip address and verify
			if ( $params['ip'] != '' ) {
				$ip = ( $this->_params['format']=='ip' ) ? $params['ip'] : ip2long($params['ip']);
				if ( filter_var ($ip, FILTER_VALIDATE_IP))
					$this->_params['ip'] = $params['ip'];
				else
					throw new Exception ( 'Invalid ip.' );
			}
		// }}}

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
		//check for necessary params
		if ( !$this->_params['ip']) 
			throw new Exception ('IP not provided.');

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