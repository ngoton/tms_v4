<?php

Class houseModel Extends baseModel {
	protected $table = "house";

	public function getAllHouse($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createHouse($data) 
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
    public function updateHouse($data,$where) 
    {    
        if ($this->getHouseByWhere($where)) {
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
    public function deleteHouse($id){
    	if ($this->getHouse($id)) {
    		return $this->delete($this->table,array('house_id'=>$id));
    	}
    }
    public function getHouse($id){
        return $this->getByID($this->table,$id);
    }
    public function getHouseByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllHouseByWhere($id){
        return $this->query('SELECT * FROM house WHERE house_id != '.$id);
    }
    public function queryHouse($sql){
        return $this->query($sql);
    }
    public function getLastHouse(){
        return $this->getLast($this->table);
    }
}
?>