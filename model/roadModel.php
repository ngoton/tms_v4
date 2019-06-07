<?php

Class roadModel Extends baseModel {
	protected $table = "road";

	public function getAllRoad($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createRoad($data) 
    {    
        /*$data = array(
        	'Roadname' => $data['Roadname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateRoad($data,$id) 
    {    
        if ($this->getRoadByWhere($id)) {
        	/*$data = array(
	        	'Roadname' => $data['Roadname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteRoad($id){
    	if ($this->getRoad($id)) {
    		return $this->delete($this->table,array('road_id'=>$id));
    	}
    }
    public function getRoad($id){
    	return $this->getByID($this->table,$id);
    }
    public function getRoadByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllRoadByWhere($id){
        return $this->query('SELECT * FROM road WHERE road_id != '.$id);
    }
    public function getLastRoad(){
        return $this->getLast($this->table);
    }
    public function queryRoad($sql){
        return $this->query($sql);
    }
}
?>