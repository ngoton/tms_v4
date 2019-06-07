<?php

Class insuranceModel Extends baseModel {
	protected $table = "insurance";

	public function getAllInsurance($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createInsurance($data) 
    {    
        /*$data = array(
        	'Insurancename' => $data['Insurancename'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateInsurance($data,$id) 
    {    
        if ($this->getInsuranceByWhere($id)) {
        	/*$data = array(
	        	'Insurancename' => $data['Insurancename'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteInsurance($id){
    	if ($this->getInsurance($id)) {
    		return $this->delete($this->table,array('insurance_id'=>$id));
    	}
    }
    public function getInsurance($id){
    	return $this->getByID($this->table,$id);
    }
    public function getInsuranceByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllInsuranceByWhere($id){
        return $this->query('SELECT * FROM insurance WHERE insurance_id != '.$id);
    }
    public function getLastInsurance(){
        return $this->getLast($this->table);
    }
    public function checkInsurance($id,$insurance_date,$driver){
        return $this->query('SELECT * FROM insurance WHERE insurance_id != '.$id.' AND insurance_date = "'.$insurance_date.'" AND driver = '.$driver);
    }
    public function queryInsurance($sql){
        return $this->query($sql);
    }
}
?>