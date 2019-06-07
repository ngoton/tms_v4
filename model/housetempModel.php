<?php

Class housetempModel Extends baseModel {
	protected $table = "house_temp";

	public function getAllHouse($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createHouse($data) 
    {    
        /*$data = array(
        	'Housename' => $data['Housename'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateHouse($data,$id) 
    {    
        if ($this->getHouseByWhere($id)) {
        	/*$data = array(
	        	'Housename' => $data['Housename'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteHouse($id){
    	if ($this->getHouse($id)) {
    		return $this->delete($this->table,array('house_temp_id'=>$id));
    	}
    }
    public function getHouse($id){
    	return $this->getByID($this->table,$id);
    }
    public function getHouseByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllHouseByWhere($id){
        return $this->query('SELECT * FROM house_temp WHERE house_temp_id != '.$id);
    }
    public function getLastHouse(){
        return $this->getLast($this->table);
    }
    public function checkHouse($id,$house_name){
        return $this->query('SELECT * FROM house_temp WHERE house_id != '.$id.' AND house_name = "'.$house_name.'"');
    }
    public function queryHouse($sql){
        return $this->query($sql);
    }
}
?>