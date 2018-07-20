<?php

Class oilModel Extends baseModel {
	protected $table = "oil";

	public function getAllOil($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createOil($data) 
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
    public function updateOil($data,$where) 
    {    
        if ($this->getOilByWhere($where)) {
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
    public function deleteOil($id){
    	if ($this->getOil($id)) {
    		return $this->delete($this->table,array('oil_id'=>$id));
    	}
    }
    public function getOil($id){
        return $this->getByID($this->table,$id);
    }
    public function getOilByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllOilByWhere($id){
        return $this->query('SELECT * FROM oil WHERE oil_id != '.$id);
    }
    public function queryOil($sql){
        return $this->query($sql);
    }
    public function getLastOil(){
        return $this->getLast($this->table);
    }
}
?>