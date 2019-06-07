<?php

Class tollcostModel Extends baseModel {
	protected $table = "toll_cost";

	public function getAllToll($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createToll($data) 
    {    
        /*$data = array(
        	'Tollname' => $data['Tollname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateToll($data,$id) 
    {    
        if ($this->getTollByWhere($id)) {
        	/*$data = array(
	        	'Tollname' => $data['Tollname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteToll($id){
    	if ($this->getToll($id)) {
    		return $this->delete($this->table,array('toll_cost_id'=>$id));
    	}
    }
    public function getToll($id){
    	return $this->getByID($this->table,$id);
    }
    public function getTollByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllTollByWhere($id){
        return $this->query('SELECT * FROM toll_cost WHERE toll_cost_id != '.$id);
    }
    public function getLastToll(){
        return $this->getLast($this->table);
    }
    public function checkToll($id,$toll_name){
        return $this->query('SELECT * FROM toll_cost WHERE toll_cost_id != '.$id.' AND toll_cost_name = '.$toll_name);
    }
    public function queryToll($sql){
        return $this->query($sql);
    }
}
?>