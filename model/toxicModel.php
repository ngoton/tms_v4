<?php

Class toxicModel Extends baseModel {
	protected $table = "toxic";

	public function getAllToxic($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createToxic($data) 
    {    
        /*$data = array(
        	'Toxicname' => $data['Toxicname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateToxic($data,$id) 
    {    
        if ($this->getToxicByWhere($id)) {
        	/*$data = array(
	        	'Toxicname' => $data['Toxicname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteToxic($id){
    	if ($this->getToxic($id)) {
    		return $this->delete($this->table,array('toxic_id'=>$id));
    	}
    }
    public function getToxic($id){
    	return $this->getByID($this->table,$id);
    }
    public function getToxicByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllToxicByWhere($id){
        return $this->query('SELECT * FROM toxic WHERE toxic_id != '.$id);
    }
    public function getLastToxic(){
        return $this->getLast($this->table);
    }
    public function checkToxic($id,$toxic_date,$driver){
        return $this->query('SELECT * FROM toxic WHERE toxic_id != '.$id.' AND toxic_date = "'.$toxic_date.'" AND driver = '.$driver);
    }
    public function queryToxic($sql){
        return $this->query($sql);
    }
}
?>