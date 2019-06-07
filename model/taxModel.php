<?php

Class taxModel Extends baseModel {
	protected $table = "tax";

	public function getAllTax($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createTax($data) 
    {    
        /*$data = array(
        	'Taxname' => $data['Taxname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateTax($data,$id) 
    {    
        if ($this->getTaxByWhere($id)) {
        	/*$data = array(
	        	'Taxname' => $data['Taxname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteTax($id){
    	if ($this->getTax($id)) {
    		return $this->delete($this->table,array('tax_id'=>$id));
    	}
    }
    public function getTax($id){
    	return $this->getByID($this->table,$id);
    }
    public function getTaxByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllTaxByWhere($id){
        return $this->query('SELECT * FROM tax WHERE Tax_id != '.$id);
    }
    public function getLastTax(){
        return $this->getLast($this->table);
    }
    public function checkTax($id,$tax_date,$driver){
        return $this->query('SELECT * FROM tax WHERE tax_id != '.$id.' AND tax_date = "'.$tax_date.'" AND driver = '.$driver);
    }
    public function queryTax($sql){
        return $this->query($sql);
    }
}
?>