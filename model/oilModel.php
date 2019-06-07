<?php

Class oilModel Extends baseModel {
	protected $table = "oil";

	public function getAllOil($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createOil($data) 
    {    
        /*$data = array(
        	'Oilname' => $data['Oilname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateOil($data,$id) 
    {    
        if ($this->getOilByWhere($id)) {
        	/*$data = array(
	        	'Oilname' => $data['Oilname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteOil($id){
    	if ($this->getOil($id)) {
    		return $this->delete($this->table,array('oil_id'=>$id));
    	}
    }
    public function getOil($id){
    	return $this->getByID($this->table,$id);
    }
    public function getOilByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllOilByWhere($id){
        return $this->query('SELECT * FROM oil WHERE oil_id != '.$id);
    }
    public function getLastOil(){
        return $this->getLast($this->table);
    }
    public function checkOil($id,$way){
        return $this->query('SELECT * FROM oil WHERE oil_id != '.$id.' AND way = "'.$way.'"');
    }
    public function queryOil($sql){
        return $this->query($sql);
    }
}
?>