<?php

/**
 *	phpIPAM Address class
 */

class Address
{	
	// {{{ properties:
	var $format = 'decimal';	//decimal=long or ip=dotted
	var $ip = '';			//ip address in retrospective to $this->format
	// }}}

	// {{{ Get address details
	public function getAddress() {
	
		// {{{ Check ip address validity
		if ($this->format=='decimal') {
			//Check ip validity for format
			if (!is_numeric($this->ip)) 					{ throw new Exception('Invalid ip address - '.$this->ip.' in format '.$this->format); }
		}
		elseif($this->format=='ip') {
			if (verifyCidr($this->ip.'/32', false))				{ throw new Exception('Invalid ip address - '.$this->ip.' in format '.$this->format); }

			//Convert ip to long
			$this->ip = Transform2decimal($this->ip);
		}
		// }}}

		// {{{ get addr details by ip
		$res = getIpAddrDetailsByIp ($this->ip);
		//throw new exception if not existing
		if(sizeof($res)==0) 							{ throw new Exception('Address not existed',10); }

		//create object from results
		foreach($res as $key=>$line) {
			$this->$key = $line;
		}

		// {{{ If format==ip, then convert all Ids to names and etc.
		if ($this->format=='ip'){
			$this->state = reformatIPStateText($this->state);

			//Get Switch and its params
			$switchDetails = getSwitchById($this->switch);
			$this->switch = $switchDetails['hostname'].' ['.$switchDetails['ip_addr'].']';

			// {{{ Get Subnet and its params
				$this->subnet = getSubnetDetailsById($this->subnetId);
				$this->subnet['subnet'] = Transform2long($this->subnet['subnet']);

				//Get Master Subnet Details
				$masterSubnet = getSubnetDetailsById($this->subnet['masterSubnetId']);
				if (!($masterSubnet['isFolder'])) $this->subnet['masterSubnet'] = Transform2long($masterSubnet['subnet']).'/'.$masterSubnet['mask'].' ['.$masterSubnet['description'].']';

				//Get VRF details
				if ($this->subnet['vrfId']) {
					$vrfDetails = getVRFDetailsById($this->subnet['vrfId']);
					$this->subnet['vrfId'] = '['.$vrfDetails['rd'].'] '.$vrfDetails['name'].' ('.$vrfDetails['description'].')';
				}

				//Get Section name
				$this->subnet['sectionId'] = getSectionDetailsById($this->subnet['sectionId'])['name'];

				//Get VLAN Details
				if ($this->subnet['vlanId']) {
					$vlanDetails = subnetGetVlanDetailsById($this->subnet['vlanId']);
					$this->subnet['vlanId'] = '['.$vlanDetails['number'].'] '.$vlanDetails['name'].' ('.$vlanDetails['description'].')';
				}
			// }}}


		}
		// }}}

		// {{{ remove input parameters from output
		unset($this->format);
		unset($this->ip);
		if ($this->format=='ip'){
			unset($this->id);
			unset($this->subnetId);
		}
		//}}}

		//convert object to array
		$result = $this->toArray($this, $format);

		//return result
		return $result;
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
