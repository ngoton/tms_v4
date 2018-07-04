<?php

Class sparepartcodeModel Extends baseModel {
	protected $table = "spare_part_code";

	public function getAllStock($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createStock($data) 
    {    
        /*$data = array(
        	'Stockname' => $data['Stockname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateStock($data,$id) 
    {    
        if ($this->getStockByWhere($id)) {
        	/*$data = array(
	        	'Stockname' => $data['Stockname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteStock($id){
    	if ($this->getStock($id)) {
    		return $this->delete($this->table,array('spare_part_code_id'=>$id));
    	}
    }
    public function getStock($id){
    	return $this->getByID($this->table,$id);
    }
    public function getStockByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllStockByWhere($id){
        return $this->query('SELECT * FROM spare_part_code WHERE spare_part_code_id != '.$id);
    }
    public function getLastStock(){
        return $this->getLast($this->table);
    }
    public function checkStock($id,$code){
        return $this->query('SELECT * FROM spare_part_code WHERE spare_part_code_id != '.$id.' AND code = "'.$code.'"');
    }
    public function queryStock($sql){
        return $this->query($sql);
    }
}
?>