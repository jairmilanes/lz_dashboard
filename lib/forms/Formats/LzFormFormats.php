<?php
namespace Lib\Formats;

class LzFormFormats {
	
	protected $format;
	
	public function __construct(){
		return true;
	}
	
	public function get($key){
		return $this->format[$key];
	}
}