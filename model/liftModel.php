<?php

Class liftModel Extends baseModel {
	protected $table = "lift";

	public function getAllLift($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createLift($data) 
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
    public function updateLift($data,$where) 
    {    
        if ($this->getLiftByWhere($where)) {
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
    public function deleteLift($id){
    	if ($this->getLift($id)) {
    		return $this->delete($this->table,array('lift_id'=>$id));
    	}
    }
    public function getLift($id){
        return $this->getByID($this->table,$id);
    }
    public function getLiftByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllLiftByWhere($id){
        return $this->query('SELECT * FROM lift WHERE lift_id != '.$id);
    }
    public function queryLift($sql){
        return $this->query($sql);
    }
    public function getLastLift(){
        return $this->getLast($this->table);
    }
}
?>