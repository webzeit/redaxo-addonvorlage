<?php

class objectModel {
	
	public $id;
	public $settings;
	public $isNewRecord = 1;
	public $db;
	public $errors;
	
	
	function __construct($id = NULL) {
		
		if($id > 0)
			 $this->loadModel($id);
	}
	
	private function loadModel($id) {
		
		$sql = rex_sql::factory();
		$record = $sql->getArray("SELECT * FROM ".$this->settings['database']['table']." WHERE ".$this->settings['database']['primary']." = ".$id);

		if(count($record) == 1) {
			$this->isNewRecord = 0;
			foreach($record[0] as $field => $value)
				$this->{$field} = $value;
		}	
	
	}
	
	public function save() {
		
		$this->rexSql(0);
		foreach($this->settings['database']['fields'] as $field => $settings)
		   $this->db->setValue($field, $this->{$field});		
		   
		if($this->isNewRecord == 1)  {
			if(!$this->insert())
				$this->errors[] = array('err' => 'insert');
		} else {
			if(!$this->update())
				$this->errors[] = array('err' => 'update');
		}
			
		if(count($this->errors) > 0)
			return false;
		else
			return true;
	}
	
	private function update() {
		$this->db->setWhere($this->settings['database']['primary']. ' = '. $this->{$this->settings['database']['primary']});
		return $this->db->update();	
	}
	
	private function insert() {
		return $this->db->insert();
	}
	
	public function delete() {
		
		$this->rexSql(0);
		$this->db->setWhere($this->settings['database']['primary']. ' = '. $this->{$this->settings['database']['primary']});
		return $this->db->delete();	
		
	}
	
	private function rexSql($debug = 0) {
		
		$this->db = rex_sql::factory();
		$this->db->debugsql = 0;
		
		$this->db->setTable($this->settings['database']['table']);
	}
	

	
	
}

?>