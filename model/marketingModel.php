<?php

Class marketingModel Extends baseModel {
	protected $table = "marketing";

	public function getAllMarketing($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createMarketing($data) 
    {    
        /*$data = array(
        	'Tirename' => $data['Tirename'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateMarketing($data,$id) 
    {    
        if ($this->getMarketingByWhere($id)) {
        	/*$data = array(
	        	'Marketingname' => $data['Marketingname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteMarketing($id){
    	if ($this->getMarketing($id)) {
    		return $this->delete($this->table,array('marketing_id'=>$id));
    	}
    }
    public function getMarketing($id){
    	return $this->getByID($this->table,$id);
    }
    public function getMarketingByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllMarketingByWhere($id){
        return $this->query('SELECT * FROM marketing WHERE marketing_id != '.$id);
    }
    public function getLastMarketing(){
        return $this->getLast($this->table);
    }
    
    public function queryMarketing($sql){
        return $this->query($sql);
    }
}
?>