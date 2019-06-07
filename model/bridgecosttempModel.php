<?php

Class bridgecosttempModel Extends baseModel {
	protected $table = "bridge_cost_temp";

	public function getAllBridgecost($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createBridgecost($data) 
    {    
        /*$data = array(
        	'Bridgecostname' => $data['Bridgecostname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateBridgecost($data,$id) 
    {    
        if ($this->getBridgecostByWhere($id)) {
        	/*$data = array(
	        	'Bridgecostname' => $data['Bridgecostname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteBridgecost($id){
    	if ($this->getBridgecost($id)) {
    		return $this->delete($this->table,array('bridge_cost_temp_id'=>$id));
    	}
    }
    public function getBridgecost($id){
    	return $this->getByID($this->table,$id);
    }
    public function getBridgecostByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllBridgecostByWhere($id){
        return $this->query('SELECT * FROM bridge_cost_temp WHERE bridge_cost_temp_id != '.$id);
    }
    public function getLastBridgecost(){
        return $this->getLast($this->table);
    }
    public function checkBridgecost($id){
        return $this->query('SELECT * FROM bridge_cost_temp WHERE bridge_cost_temp_id != '.$id);
    }
    public function queryBridgecost($sql){
        return $this->query($sql);
    }
}
?>