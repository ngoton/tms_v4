<?php

Class loanshipmentModel Extends baseModel {
	protected $table = "loan_shipment";

	public function getAllUnit($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createUnit($data) 
    {    
        /*$data = array(
        	'Unitname' => $data['Unitname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateUnit($data,$id) 
    {    
        if ($this->getUnitByWhere($id)) {
        	/*$data = array(
	        	'Unitname' => $data['Unitname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteUnit($id){
    	if ($this->getUnit($id)) {
    		return $this->delete($this->table,array('loan_shipment_id'=>$id));
    	}
    }
    public function getUnit($id){
    	return $this->getByID($this->table,$id);
    }
    public function getUnitByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllUnitByWhere($id){
        return $this->query('SELECT * FROM loan_shipment WHERE loan_shipment_id != '.$id);
    }
    public function getLastUnit(){
        return $this->getLast($this->table);
    }
    public function checkUnit($id){
        return $this->query('SELECT * FROM loan_shipment WHERE loan_shipment_id != '.$id);
    }
    public function queryUnit($sql){
        return $this->query($sql);
    }
}
?>