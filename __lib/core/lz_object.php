<?php
/**
 * oLzPaymentObject object class
 * 
 * @author Jair Milanes Junior
 *
 */
class oLzObject {
	
	/**
	 * Object constructor
	 * 
	 * @param array $params
	 */
	public function __construct(){
		return $this;
	}
	
	/**
	 * Sets all object properties using the proper set method
	 * 
	 * @param array $params
	 * @return boolean
	 */
	protected function setParams(array $params = array()){
		foreach( $params as $key => $value ){
			$method = 'set'.ucfirst(strtolower($key));
			if( method_exists($this, $method)){
				$this->$method($value);
			}
		}
		return true;
	}
	
	/**
	 * Returns this object in the desired format
	 * 
	 * @param string $type
	 * @return array|PaymentObject|boolean
	 */
	public function to( $type = 'object' ){
		$rs = $this->toArray($this);
		if( $type == 'object' ){
			return $this->toObject($rs);
		}
		return $rs;
	}
	
	/**
	 * Converts this to a generic stdClass calling the proper get methods
	 * 
	 * @param array $array
	 * @return StdClass
	 */
	public function toObject($array) {
		if (is_array($array)) {
			return (object) array_map(__FUNCTION__, $array);
		} else {
			return $array;
		}
	}
	
	/**
	 * Converts this to a array calling the proper get methods
	 * 
	 * @param  oLzObject $object
	 * @return array
	 */
	public function toArray($object){
		$rs = array();
		foreach( $object as $key => $value ){
			$method = 'get'.ucfirst(strtolower($key));
			
			if( method_exists($object, $method)){
				$rs[$key] = $object->$method();
				
				if( is_array($rs[$key])){
					$rs[$key] = $this->loop_objects($rs[$key]);
				
				} else if( is_object($rs[$key])){
					$rs[$key] = $this->toArray($rs[$key]);
				}
			}
		}
		return $rs;
	}
	
	/**
	 * Private method to help toArray
	 * 
	 * @param array $objects
	 * @return array
	 */
	private function loop_objects($objects){
		$rs = array();
		foreach( $objects as $index => $object ){
			if( is_object($object)){
				$rs[$index] = $this->toArray($object);
			} else if(is_array($object)) {
				$rs[$index] = $object;
			} else {
				$method = 'get'.ucfirst(strtolower($index));
				if( method_exists($object, $method)){
					$rs[$method] = $object->$method();
				} else {
					$rs = $objects;
					break;
				}
			}
		}
		return $rs;
	}
}