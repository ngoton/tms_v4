<?php

Class salarybonusModel Extends baseModel {
	protected $table = "salary_bonus";

	public function getAllSalary($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createSalary($data) 
    {    
        /*$data = array(
        	'Salaryname' => $data['Salaryname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateSalary($data,$id) 
    {    
        if ($this->getSalaryByWhere($id)) {
        	/*$data = array(
	        	'Salaryname' => $data['Salaryname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteSalary($id){
    	if ($this->getSalary($id)) {
    		return $this->delete($this->table,array('salary_bonus_id'=>$id));
    	}
    }
    public function getSalary($id){
    	return $this->getByID($this->table,$id);
    }
    public function getSalaryByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllSalaryByWhere($id){
        return $this->query('SELECT * FROM salary_bonus WHERE salary_bonus_id != '.$id);
    }
    public function getLastSalary(){
        return $this->getLast($this->table);
    }
    public function checkSalary($id){
        return $this->query('SELECT * FROM salary_bonus WHERE salary_bonus_id != '.$id);
    }
    public function querySalary($sql){
        return $this->query($sql);
    }
}
?>