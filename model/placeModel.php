<?php

Class placeModel Extends baseModel {
	protected $table = "place";

	public function getAllPlace($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createPlace($data) 
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
            'bank' => $data['bank'],
            'account' => $data['account'],
        	);*/

        return $this->insert($this->table,$data);
    }
    public function updatePlace($data,$where) 
    {    
        if ($this->getPlaceByWhere($where)) {
        	/*$data = array(
            'staff_id' => $data['staff_id'],
            'staff_name' => $data['staff_name'],
            'staff_birth' => $data['staff_birth'],
            'staff_gender' => $data['staff_gender'],
            'staff_address' => $data['staff_address'],
            'staff_phone' => $data['staff_phone'],
            'staff_email' => $data['staff_email'],
            'cmnd' => $data['cmnd'],
            'bank' => $data['bank'],
            'account' => $data['account'],
            );*/
	        return $this->update($this->table,$data,$where);
        }
        
    }
    public function deletePlace($id){
    	if ($this->getPlace($id)) {
    		return $this->delete($this->table,array('place_id'=>$id));
    	}
    }
    public function getPlace($id){
        return $this->getByID($this->table,$id);
    }
    public function getPlaceByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllPlaceByWhere($id){
        return $this->query('SELECT * FROM place WHERE place_id != '.$id);
    }
    public function queryPlace($sql){
        return $this->query($sql);
    }
    public function getLastPlace(){
        return $this->getLast($this->table);
    }
}
?>