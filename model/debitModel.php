<?php

Class debitModel Extends baseModel {
	protected $table = "debit";

	public function getAllDebit($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createDebit($data) 
    {    
        /*$data = array(
        	'Debitname' => $data['Debitname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateDebit($data,$id) 
    {    
        if ($this->getDebitByWhere($id)) {
        	/*$data = array(
	        	'Debitname' => $data['Debitname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteDebit($id){
    	if ($this->getDebit($id)) {
    		return $this->delete($this->table,array('debit_id'=>$id));
    	}
    }
    public function getDebit($id){
    	return $this->getByID($this->table,$id);
    }
    public function getDebitByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllDebitByWhere($id){
        return $this->query('SELECT * FROM debit WHERE debit_id != '.$id);
    }
    public function getLastDebit(){
        return $this->getLast($this->table);
    }
    public function checkDebit($id){
        return $this->query('SELECT * FROM debit WHERE debit_id != '.$id);
    }
    public function queryDebit($sql){
        return $this->query($sql);
    }
}
?>