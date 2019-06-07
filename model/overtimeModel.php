<?php

Class overtimeModel Extends baseModel {
	protected $table = "overtime";

	public function getAllOvertime($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createOvertime($data) 
    {    
        /*$data = array(
        	'Overtimename' => $data['Overtimename'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateOvertime($data,$id) 
    {    
        if ($this->getOvertimeByWhere($id)) {
        	/*$data = array(
	        	'Overtimename' => $data['Overtimename'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteOvertime($id){
    	if ($this->getOvertime($id)) {
    		return $this->delete($this->table,array('overtime_id'=>$id));
    	}
    }
    public function getOvertime($id){
    	return $this->getByID($this->table,$id);
    }
    public function getOvertimeByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllOvertimeByWhere($id){
        return $this->query('SELECT * FROM overtime WHERE overtime_id != '.$id);
    }
    public function getLastOvertime(){
        return $this->getLast($this->table);
    }
    public function checkOvertime($id,$overtime_date,$driver){
        return $this->query('SELECT * FROM overtime WHERE overtime_id != '.$id.' AND overtime_date = "'.$overtime_date.'" AND driver = '.$driver);
    }
    public function queryOvertime($sql){
        return $this->query($sql);
    }
}
?>