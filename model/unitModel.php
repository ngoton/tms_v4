<?php

Class unitModel Extends baseModel {
	protected $table = "unit";

	public function getAllUnit($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createUnit($data) 
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
    public function updateUnit($data,$where) 
    {    
        if ($this->getUnitByWhere($where)) {
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
    public function deleteUnit($id){
    	if ($this->getUnit($id)) {
    		return $this->delete($this->table,array('unit_id'=>$id));
    	}
    }
    public function getUnit($id){
        return $this->getByID($this->table,$id);
    }
    public function getUnitByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllUnitByWhere($id){
        return $this->query('SELECT * FROM unit WHERE unit_id != '.$id);
    }
    public function queryUnit($sql){
        return $this->query($sql);
    }
    public function getLastUnit(){
        return $this->getLast($this->table);
    }
}
?>