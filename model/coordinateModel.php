<?php

Class coordinateModel Extends baseModel {
	protected $table = "coordinate";

	public function getAllCoordinate($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createCoordinate($data) 
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
    public function updateCoordinate($data,$where) 
    {    
        if ($this->getCoordinateByWhere($where)) {
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
    public function deleteCoordinate($id){
    	if ($this->getCoordinate($id)) {
    		return $this->delete($this->table,array('coordinate_id'=>$id));
    	}
    }
    public function getCoordinate($id){
        return $this->getByID($this->table,$id);
    }
    public function getCoordinateByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllCoordinateByWhere($id){
        return $this->query('SELECT * FROM coordinate WHERE coordinate_id != '.$id);
    }
    public function queryCoordinate($sql){
        return $this->query($sql);
    }
    public function getLastCoordinate(){
        return $this->getLast($this->table);
    }
}
?>