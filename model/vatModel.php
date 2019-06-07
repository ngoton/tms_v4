<?php

Class vatModel Extends baseModel {
	protected $table = "vat";

	public function getAllVAT($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createVAT($data) 
    {    
        /*$data = array(
        	'VATname' => $data['VATname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateVAT($data,$id) 
    {    
        if ($this->getVATByWhere($id)) {
        	/*$data = array(
	        	'VATname' => $data['VATname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteVAT($id){
    	if ($this->getVAT($id)) {
    		return $this->delete($this->table,array('vat_id'=>$id));
    	}
    }
    public function getVAT($id){
    	return $this->getByID($this->table,$id);
    }
    public function getVATByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllVATByWhere($id){
        return $this->query('SELECT * FROM vat WHERE vat_id != '.$id);
    }
    public function getLastVAT(){
        return $this->getLast($this->table);
    }
    public function checkVAT($id){
        return $this->query('SELECT * FROM vat WHERE vat_id != '.$id);
    }
    public function queryVAT($sql){
        return $this->query($sql);
    }
}
?>