<?php
class LZModel extends DAO {
	
	public function __construct($options = array() ){
		
		if( isset($options['table_name'])){
			$this->setTableName($options['table_name']);
		}
		
		if( isset($options['fields'])){
			$this->setFields($options['fields']);
		}
		
		if( isset($options['primary_key'])){
			$this->setPrimaryKey($options['primary_key']);
		}
		return;
	}
	
}