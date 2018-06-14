<?php

Class dispatchModel Extends baseModel {
	protected $table = "dispatch";

	public function getAllDispatch($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createDispatch($data) 
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
    public function updateDispatch($data,$where) 
    {    
        if ($this->getDispatchByWhere($where)) {
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
    public function deleteDispatch($id){
    	if ($this->getDispatch($id)) {
    		return $this->delete($this->table,array('dispatch_id'=>$id));
    	}
    }
    public function getDispatch($id){
        return $this->getByID($this->table,$id);
    }
    public function getDispatchByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllDispatchByWhere($id){
        return $this->query('SELECT * FROM dispatch WHERE dispatch_id != '.$id);
    }
    public function queryDispatch($sql){
        return $this->query($sql);
    }
    public function getLastDispatch(){
        return $this->getLast($this->table);
    }
}
?>