<?php

Class costtypeModel Extends baseModel {
	protected $table = "cost_type";

	public function getAllCost($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createCost($data) 
    {    
        /*$data = array(
        	'Costname' => $data['Costname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateCost($data,$id) 
    {    
        if ($this->getCostByWhere($id)) {
        	/*$data = array(
	        	'Costname' => $data['Costname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteCost($id){
    	if ($this->getCost($id)) {
    		return $this->delete($this->table,array('cost_type_id'=>$id));
    	}
    }
    public function getCost($id){
    	return $this->getByID($this->table,$id);
    }
    public function getCostByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllCostByWhere($id){
        return $this->query('SELECT * FROM cost_type WHERE cost_type_id != '.$id);
    }
    public function getLastCost(){
        return $this->getLast($this->table);
    }
    public function checkCost($id){
        return $this->query('SELECT * FROM cost_type WHERE cost_type_id != '.$id);
    }
    public function queryCost($sql){
        return $this->query($sql);
    }
}
?>