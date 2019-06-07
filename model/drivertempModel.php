<?php

Class drivertempModel Extends baseModel {
	protected $table = "driver_temp";

	public function getAllDriver($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createDriver($data) 
    {    
        /*$data = array(
        	'Drivername' => $data['Drivername'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateDriver($data,$id) 
    {    
        if ($this->getDriverByWhere($id)) {
        	/*$data = array(
	        	'Drivername' => $data['Drivername'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteDriver($id){
    	if ($this->getDriver($id)) {
    		return $this->delete($this->table,array('driver_temp_id'=>$id));
    	}
    }
    public function getDriver($id){
    	return $this->getByID($this->table,$id);
    }
    public function getDriverByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllDriverByWhere($id){
        return $this->query('SELECT * FROM driver_temp WHERE driver_temp_id != '.$id);
    }
    public function getLastDriver(){
        return $this->getLast($this->table);
    }
    public function checkDriver($id,$driver,$vehicle){
        return $this->query('SELECT * FROM driver_temp WHERE driver_id != '.$id.' AND vehicle = '.$vehicle.' AND steersman = '.$driver);
    }
    public function queryDriver($sql){
        return $this->query($sql);
    }
}
?>