<?php

Class gasModel Extends baseModel {
	protected $table = "gas";

	public function getAllGas($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createGas($data) 
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
    public function updateGas($data,$where) 
    {    
        if ($this->getGasByWhere($where)) {
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
    public function deleteGas($id){
    	if ($this->getGas($id)) {
    		return $this->delete($this->table,array('gas_id'=>$id));
    	}
    }
    public function getGas($id){
        return $this->getByID($this->table,$id);
    }
    public function getGasByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllGasByWhere($id){
        return $this->query('SELECT * FROM gas WHERE gas_id != '.$id);
    }
    public function queryGas($sql){
        return $this->query($sql);
    }
    public function getLastGas(){
        return $this->getLast($this->table);
    }
}
?>