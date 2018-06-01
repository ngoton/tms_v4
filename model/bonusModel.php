<?php

Class bonusModel Extends baseModel {
	protected $table = "bonus";

	public function getAllBonus($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createBonus($data) 
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
    public function updateBonus($data,$where) 
    {    
        if ($this->getBonusByWhere($where)) {
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
    public function deleteBonus($id){
    	if ($this->getBonus($id)) {
    		return $this->delete($this->table,array('bonus_id'=>$id));
    	}
    }
    public function getBonus($id){
        return $this->getByID($this->table,$id);
    }
    public function getBonusByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllBonusByWhere($id){
        return $this->query('SELECT * FROM bonus WHERE bonus_id != '.$id);
    }
    public function queryBonus($sql){
        return $this->query($sql);
    }
    public function getLastBonus(){
        return $this->getLast($this->table);
    }
}
?>