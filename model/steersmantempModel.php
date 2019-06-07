<?php

Class steersmantempModel Extends baseModel {
	protected $table = "steersman_temp";

	public function getAllSteersman($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createSteersman($data) 
    {    
        /*$data = array(
        	'Steersmanname' => $data['Steersmanname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateSteersman($data,$id) 
    {    
        if ($this->getSteersmanByWhere($id)) {
        	/*$data = array(
	        	'Steersmanname' => $data['Steersmanname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteSteersman($id){
    	if ($this->getSteersman($id)) {
    		return $this->delete($this->table,array('steersman_temp_id'=>$id));
    	}
    }
    public function getSteersman($id){
    	return $this->getByID($this->table,$id);
    }
    public function getSteersmanByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllSteersmanByWhere($id){
        return $this->query('SELECT * FROM steersman_temp WHERE steersman_temp_id != '.$id);
    }
    public function getLastSteersman(){
        return $this->getLast($this->table);
    }
    public function checkSteersman($id,$Steersman_name,$steersman_code){
        return $this->query('SELECT * FROM steersman_temp WHERE steersman_id != '.$id.' AND steersman_code = "'.$steersman_code.'" AND steersman_name = "'.$Steersman_name.'"');
    }
    public function querySteersman($sql){
        return $this->query($sql);
    }
}
?>