<?php

Class excessModel Extends baseModel {
	protected $table = "excess";

	public function getAllExcess($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createExcess($data) 
    {    
        /*$data = array(
        	'Excessname' => $data['Excessname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateExcess($data,$id) 
    {    
        if ($this->getExcessByWhere($id)) {
        	/*$data = array(
	        	'Excessname' => $data['Excessname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteExcess($id){
    	if ($this->getExcess($id)) {
    		return $this->delete($this->table,array('excess_id'=>$id));
    	}
    }
    public function getExcess($id){
    	return $this->getByID($this->table,$id);
    }
    public function getExcessByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllExcessByWhere($id){
        return $this->query('SELECT * FROM excess WHERE excess_id != '.$id);
    }
    public function getLastExcess(){
        return $this->getLast($this->table);
    }
    public function checkExcess($shipment){
        return $this->query('SELECT * FROM excess WHERE shipment = '.$shipment);
    }
}
?>