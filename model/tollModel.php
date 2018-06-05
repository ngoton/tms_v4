<?php

Class tollModel Extends baseModel {
	protected $table = "toll";

	public function getAllToll($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createToll($data) 
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
    public function updateToll($data,$where) 
    {    
        if ($this->getTollByWhere($where)) {
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
    public function deleteToll($id){
    	if ($this->getToll($id)) {
    		return $this->delete($this->table,array('toll_id'=>$id));
    	}
    }
    public function getToll($id){
        return $this->getByID($this->table,$id);
    }
    public function getTollByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllTollByWhere($id){
        return $this->query('SELECT * FROM toll WHERE toll_id != '.$id);
    }
    public function queryToll($sql){
        return $this->query($sql);
    }
    public function getLastToll(){
        return $this->getLast($this->table);
    }
}
?>