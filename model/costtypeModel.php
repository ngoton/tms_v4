<?php

Class costtypeModel Extends baseModel {
	protected $table = "cost_type";

	public function getAllCost($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createCost($data) 
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
    public function updateCost($data,$where) 
    {    
        if ($this->getCostByWhere($where)) {
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
    public function deleteCost($id){
    	if ($this->getCost($id)) {
    		return $this->delete($this->table,array('cost_type_id'=>$id));
    	}
    }
    public function getCost($id){
        return $this->getByID($this->table,$id);
    }
    public function getCostByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllCostByWhere($id){
        return $this->query('SELECT * FROM cost_type WHERE cost_type_id != '.$id);
    }
    public function queryCost($sql){
        return $this->query($sql);
    }
    public function getLastCost(){
        return $this->getLast($this->table);
    }
}
?>