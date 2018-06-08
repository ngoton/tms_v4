<?php

Class vehicleromoocModel Extends baseModel {
	protected $table = "vehicle_romooc";

	public function getAllVehicle($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createVehicle($data) 
    {    
        /*$data = array(
        	'Vehiclename' => $data['Vehiclename'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateVehicle($data,$id) 
    {    
        if ($this->getVehicleByWhere($id)) {
        	/*$data = array(
	        	'Vehiclename' => $data['Vehiclename'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteVehicle($id){
    	if ($this->getVehicle($id)) {
    		return $this->delete($this->table,array('vehicle_romooc_id'=>$id));
    	}
    }
    public function getVehicle($id){
    	return $this->getByID($this->table,$id);
    }
    public function getVehicleByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllVehicleByWhere($id){
        return $this->query('SELECT * FROM vehicle_romooc WHERE vehicle_romooc_id != '.$id);
    }
    public function getLastVehicle(){
        return $this->getLast($this->table);
    }
    public function queryVehicle($sql){
        return $this->query($sql);
    }
}
?>