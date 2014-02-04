<?php

/**
 *	phpIPAM API class to work with subnets
 *
 * Reading subnets:
 *	get by id: 		?controller=subnets&action=read&id=1
 *	get by name: 	?controller=subnets&action=read&name=subnet name
 *	get all:		?controller=subnets&action=read&all=true
 */

class Subnets
{
	/* variables */
	private $_params;
	
	/* set parameters, provided via post
	 *
	 * available params:
	 *		id	- id of subnet in db table, integer.
	 *		format	- format of ip-address representation, will be 'decimal' or 'ip'.
	 *		
	 *		
	 *		cidr	- subnet in CIDR format
	 *		
	 *
	 */
	public function __construct($params)
	{
		// {{{ verify params
			//set form input validation filters
			$params_filter = array(
			    'id'		=>  FILTER_VALIDATE_INT,
			    'format'		=>  array(	'filter'	=> FILTER_VALIDATE_REGEXP,
								'flags'		=> '', 
								'options'	=> array('regexp' => '/decimal|ip/')
							),
			    'action'		=>  array(	'filter'	=> FILTER_VALIDATE_REGEXP,
								'flags'		=> '', 
								'options'	=> array('regexp' => '/create|read|delete|edit|update/')
							),
			    'sectionId'		=>  FILTER_VALIDATE_INT,
			    'masterSubnetId'	=>  FILTER_VALIDATE_INT,
			    'subnet'		=>  FILTER_VALIDATE_INT,
			    'mask'		=>  FILTER_VALIDATE_INT,
			    'description'	=>  FILTER_VALIDATE_STRING,
			    'vrfId'		=>  FILTER_VALIDATE_INT,
			    'vlanId'		=>  FILTER_VALIDATE_INT,
			    'allowRequests'	=>  FILTER_VALIDATE_BOOL,
			    'showName'		=>  FILTER_VALIDATE_BOOL,
			    'permissions'	=>  array(	'filter'	=> FILTER_CALLBACK,
								'flags'		=> '', 
								'options'	=> function ($str){ return (json_decode($str, true))?true:false; } //check normal json code in 'permissions' filed.
							),
			    'pingSubnet'	=>  FILTER_VALIDATE_BOOL,
			    'cidr'		=>  array(	'filter'	=> FILTER_CALLBACK,
								'flags'		=> '', 
								'options'	=> function ($str){
											$cidrAr = explode('/',$str);
											if ( sizeof($cidrAr)==2 ){
												return (
														filter_var($cidrAr[0], FILTER_VALIDATE_IP) &&
														filter_var($cidrAr[1], FILTER_VALIDATE_INT)
													)? $str : FALSE;
											}
											else
												return false;
										}
							),
			    'longerprefix'	=>  FILTER_VALIDATE_BOOL,
			);

			// at this point all fieds is optional, but several have default values.
			$params_default_fields = array(
			    'format'		=> "decimal",
			);

			$invalid_inputs = array ();

			//validate all $params[] to $params_filter
			$this->_params = filter_var_array($params, $params_filter);

			//If filter_var_array return FALSE, stop and throw Exception
			if ( ! $this->_params )
				throw new Exception ('Parameter validation failed!');

			// {{{ check form inputs for filter validation errors
				foreach ($this->_params as $key => &$value){
				    //If filter FAILED value is FALSE
				    if( $value === FALSE ){
					$invalid_inputs[] = $key;		//If value was determined as not valid, memorize its name
				    }
				    //If Key is not provided, Value is NULL
				    elseif ( $value === NULL ) {
					if(!(array_key_exists($key, $params_default_fields) ))
						$this->_params[$key] = $params_default_fields[$key];	//If field not set and it has default value, set it.
				    }
				}
				//If we have errors on input params, throw Exception.
				if(!empty ($invalid_inputs))
					throw new Exception ('Invalid input in fields: ' . implode ( ', ' , $invalid_inputs ));
			// }}}
		// }}}
	}

	/** 
	* create new subnet 
	*/
	public function createSubnets($_params)
	{
		//init section class
		$subnet = new Subnet();
		//required parameters
		$subnet->action      		= $this->_params['action'];
		$subnet->sectionId        	= $this->_params['sectionId'];
		$subnet->masterSubnetId 	= $this->_params['masterSubnetId'];
		$subnet->subnet		  		= $this->_params['subnet'];
		$subnet->mask	  			= $this->_params['mask'];
		$subnet->description	  	= $this->_params['description'];
		$subnet->vrfId			  	= $this->_params['vrfId'];
		$subnet->vlanId			  	= $this->_params['vlanId'];
		$subnet->allowRequests		= $this->_params['allowRequests'];
		$subnet->showName			= $this->_params['showName'];
		$subnet->permissions		= $this->_params['permissions'];
		$subnet->pingSubnet			= $this->_params['pingSubnet'];

		//create section
		$res = $subnet->createSubnet();
		//return result
		return $res;
	}


	/** 
	* read subnets 
	*/
	public function readSubnets()
	{
		//init subnet class
		$subnet = new Subnet();
		
		//set IP address format
		$subnet->format = $this->_params['format'];
		
		//get all subnets
		if ( $this->_params['all'] )
			$subnet->all = true;
		//get all subnets in subnet
		elseif ( $this->_params['sectionId'] )
			$subnet->sectionId = $this->_params['sectionId'];
		//get subnet by ID
		elseif ( $this->_params['cidr'] ){
			$subnet->cidr = $this->_params['cidr'];
			$subnet->longerprefix = $this->_params['longerprefix'];
		}
		elseif ( $this->_params['id'] )
			$subnet->id = $this->_params['id'];
		else
			throw new Exception ('No parameters provieded!');

		//fetch results
		$res = $subnet->getSubnet(); 
		
		//return subnet(s) in array format
		return $res;
	}	
	
	
	/** 
	* update existing subnet 
	*/
	public function updateSubnets()
	{
		/* not yet implementes */
		throw new Exception('Action not yet implemented');
	}	
	
	
	/** 
	* delete subnet 
	*/
	public function deleteSubnets()
	{
		/* not yet implementes */
		throw new Exception('Action not yet implemented');
	}
}

?>