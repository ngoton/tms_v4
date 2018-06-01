<?php

Class routeModel Extends baseModel {
	protected $table = "route";

	public function getAllRoute($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createRoute($data) 
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
    public function updateRoute($data,$where) 
    {    
        if ($this->getRouteByWhere($where)) {
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
    public function deleteRoute($id){
    	if ($this->getRoute($id)) {
    		return $this->delete($this->table,array('route_id'=>$id));
    	}
    }
    public function getRoute($id){
        return $this->getByID($this->table,$id);
    }
    public function getRouteByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllRouteByWhere($id){
        return $this->query('SELECT * FROM route WHERE route_id != '.$id);
    }
    public function queryRoute($sql){
        return $this->query($sql);
    }
    public function getLastRoute(){
        return $this->getLast($this->table);
    }
}
?>