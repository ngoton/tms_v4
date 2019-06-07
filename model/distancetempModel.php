<?php

Class distancetempModel Extends baseModel {
	protected $table = "distance_temp";

	public function getAllDistance($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createDistance($data) 
    {    
        /*$data = array(
        	'staff_id' => $data['staff_id'],
        	'staff_name' => $data['staff_name'],
        	'staff_birth' => $data['staff_birth'],
        	'staff_gender' => $data['staff_gender'],
            'staff_address' => $data['staff_address'],
            'staff_phone' => $data['staff_phone'],
            'staff_email' => $data['staff_email'],
            'cmnd' => $data['cmnd'],
            'Distance' => $data['Distance'],
            'account' => $data['account'],
        	);*/

        return $this->insert($this->table,$data);
    }
    public function updateDistance($data,$where) 
    {    
        if ($this->getDistanceByWhere($where)) {
        	/*$data = array(
            'staff_id' => $data['staff_id'],
            'staff_name' => $data['staff_name'],
            'staff_birth' => $data['staff_birth'],
            'staff_gender' => $data['staff_gender'],
            'staff_address' => $data['staff_address'],
            'staff_phone' => $data['staff_phone'],
            'staff_email' => $data['staff_email'],
            'cmnd' => $data['cmnd'],
            'Distance' => $data['Distance'],
            'account' => $data['account'],
            );*/
	        return $this->update($this->table,$data,$where);
        }
        
    }
    public function deleteDistance($id){
    	if ($this->getDistance($id)) {
    		return $this->delete($this->table,array('distance_temp_id'=>$id));
    	}
    }
    public function getDistance($id){
        return $this->getByID($this->table,$id);
    }
    public function getDistanceByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllDistanceByWhere($id){
        return $this->query('SELECT * FROM distance_temp WHERE distance_temp_id != '.$id);
    }
    public function getLastDistance(){
        return $this->getLast($this->table);
    }
    public function queryDistance($sql){
        return $this->query($sql);
    }
}
?>