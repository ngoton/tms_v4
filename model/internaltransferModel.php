<?php

Class internaltransferModel Extends baseModel {
	protected $table = "internal_transfer";

	public function getAllBank($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createBank($data) 
    {    
        /*$data = array(
        	'Bankname' => $data['Bankname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateBank($data,$id) 
    {    
        if ($this->getBankByWhere($id)) {
        	/*$data = array(
	        	'Bankname' => $data['Bankname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteBank($id){
    	if ($this->getBank($id)) {
    		return $this->delete($this->table,array('internal_transfer_id'=>$id));
    	}
    }
    public function getBank($id){
    	return $this->getByID($this->table,$id);
    }
    public function getBankByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllBankByWhere($id){
        return $this->query('SELECT * FROM internal_transfer WHERE internal_transfer_id != '.$id);
    }
    public function getLastBank(){
        return $this->getLast($this->table);
    }
    public function checkBank($id){
        return $this->query('SELECT * FROM internal_transfer WHERE internal_transfer_id != '.$id);
    }
    public function queryBank($sql){
        return $this->query($sql);
    }
}
?>