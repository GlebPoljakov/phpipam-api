<?php

/**
 *	phpIPAM Subnet class
 */

class Subnet
{	
	
	/**
	* get subnet details
	*/
	public function getSubnet() {
	
		/**
		* all subnets 
		*/
		if($this->all) {
			//get subnet by id
			$res = fetchAllSubnets ();
		}

		/** 
		* all subnets in section
		*/
		elseif($this->sectionId) {
			//id must be set and numberic
			if ( is_null($this->sectionId) || !is_numeric($this->sectionId) ) 	{ throw new Exception('Invalid section Id - '.$this->sectionId); }
			//get all subnets in section
			$res = fetchSubnets ($this->sectionId);
			//throw new exception if not existing
			if(sizeof($res)==0) {
				//check if section exists
				if(sizeof(getSectionDetailsById ($this->sectionId))==0) 		{ throw new Exception('Section not existing');	}
			}
		}
		
		/** 
		* subnet by id 
		*/
		elseif($this->id) {
			//id must be set and numberic
			if ( is_null($this->id) || !is_numeric($this->id) ) 				{ throw new Exception('Invalid subnet id - '.$this->id); }
			//get subnet by id
			$res = getSubnetDetailsById ($this->id);
			//throw new exception if not existing
			if(sizeof($res)==0) 												{ throw new Exception('Subnet not existing'); }
		}
		
		/**
		* subnet by name 
		*/
		elseif($this->name) {
			//name must be set and string
			if ( is_null($this->name) || strlen($this->name)==0 )				{ throw new Exception('Invalid subnet name - '.$this->name); }
			//get subnet by Name
			$res = getSubnetDetailsByName ($this->name);
			//throw new exception if not existing
			if(sizeof($res)==0) 												{ throw new Exception('Subnet not existing'); }
		}

		/**
		* subnet by CIDR
		*/
		elseif($this->cidr) {
			// {{{ cidr must be subnet in CIDR-format
				list ($ip, $mask) = explode ('/',$this->cidr);
				if (
				    !(
					filter_var($ip, FILTER_VALIDATE_IP) &&
					filter_var($mask, FILTER_VALIDATE_INT)
				    )
				)
					throw new Exception('Invalid subnet cidr - '.$this->cidr);
			// }}}

			//Transform ip form dotted-decimal to long integer
			$ip = Transform2decimal($ip);

			// {{{ get subnet by subnet/mask
				if ( $this->longerprefix ) {
					// {{{ find prefix with $ip
						//Get all subnets, ordered by subnet
						$allSubnets = fetchAllSubnets ('subnet', true);

						//Get subnets loger than given prefix
						foreach ($allSubnets as $row)
							//find first net
							if ( (int) $row['subnet'] <= (int) $ip ){
								$res = $row;
								break;
							}
					// }}}
				}
				else
					$res = getSubnetDetailsByIpMask ($ip, $mask);
			// }}}

			//throw new exception if not existing
			if(sizeof($res)==0) 												{ throw new Exception('Subnet not existing'); }

			//If format is IP, do resolve all Id-fiels 
			if ($this->format == 'ip'){
				//Get Master Subnet Details
				$masterSubnet = getSubnetDetailsById($res['masterSubnetId']);
				$res['masterSubnetId'] = (!($masterSubnet['isFolder'])) ?
					Transform2long($masterSubnet['subnet']).'/'.$masterSubnet['mask'].' ['.$masterSubnet['description'].']' :
					'';

				//Get VRF details
				if ($res['vrfId']) {
				    $vrfDetails = getVRFDetailsById($res['vrfId']);
				    $res['vrfId'] = '['.$vrfDetails['rd'].'] '.$vrfDetails['name'].' ('.$vrfDetails['description'].')';
				}

				//Get Section name
				if ($res['sectionId'])
				    $res['sectionName'] = getSectionDetailsById($res['sectionId'])['name'];

				//Get VLAN Details
				if ($res['vlanId']) {
				    $vlanDetails = subnetGetVlanDetailsById($res['vlanId']);
				    $res['vlanId'] = '['.$vlanDetails['number'].'] '.$vlanDetails['name'].' ('.$vlanDetails['description'].')';
				}
			}
		}

		/** 
		* method missing 
		*/
		else 																	{ throw new Exception('Selector missing'); }

		//create object from results
		foreach($res as $key=>$line) {
			$this->$key = $line;
		}
		//output format
		$format = $this->format;
		//remove input parameters from output
		unset($this->all);															//remove from result array
		unset($this->format);
		unset($this->name);	
		unset($this->id);
		unset($this->sectionId);	
		//convert object to array
		$result = $this->toArray($this, $format);	
		//return result
		return $result;
	}


	/**
	* create new subnet
	*/
	public function createSubnet() {
		# verications
		if(!isset($this->sectionId) || !is_numeric($this->sectionId)) 			{ throw new Exception('Invalid section Id'); }				//mandatory parameters
		if(!isset($this->masterSubnetId) || !is_numeric($this->masterSubnetId)) { throw new Exception('Invalid master Subnet Id'); }		//mandatory parameters
		if(!isset($this->subnet)) 												{ throw new Exception('Invalid subnet'); }					//mandatory parameters
		if(!isset($this->mask) || !is_numeric($this->mask)) 					{ throw new Exception('Invalid mask'); }					//mandatory parameters
		if(!is_numeric($this->vrfId))											{ throw new Exception('Invalid VRF Id'); }
		if(!is_numeric($this->vlanId))											{ throw new Exception('Invalid VRF Id'); }
		if($this->allowRequests != 0 || $this->allowRequests !=1)				{ throw new Exception('Invalid allow requests value'); }
		if($this->showName != 0 || $this->showName !=1)							{ throw new Exception('Invalid show Name value'); }
		if($this->pingSubnet != 0 || $this->pingSubnet !=1)						{ throw new Exception('Invalid ping subnet value'); }


		//output format
		$format = $this->format;
		
		//create array to write new section
		$newSubnet = $this->toArray($this, $format);
		//create new section
		$res = UpdateSection2 ($newSection, true);								//true means from API	
		//return result (true/false)
		if(!$res) 																{ throw new Exception('Invalid query'); } 
		else {
			//format response
			return "Subnet created";		
		}
	}
	

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
				// proper format
				if($key=="subnet" && $format=="ip") {
					$val = transform2long($val);
				}
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
