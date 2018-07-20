<?php

Class positionModel Extends baseModel {
	protected $table = "position";

	public function getAllPosition($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createPosition($data) 
    {    
        /*$data = array(
        	'staff_id' => $data['staff_id'],
        	'staff_name' => $data['staff_name'],
        	'staff_birth' => $data['staff_birth'],
        	'staff_gender' => $data['staff_gender'],
            'staff_address' => $data['staff_address'],
            'staff_phone' => $data['staff_phone'],
            'staff_email' => $data['staff_email'],
            'cmnd' => $data['cmnd'],
            'bank' => $data['bank'],
            'account' => $data['account'],
        	);*/

        return $this->insert($this->table,$data);
    }
    public function updatePosition($data,$where) 
    {    
        if ($this->getPositionByWhere($where)) {
        	/*$data = array(
            'staff_id' => $data['staff_id'],
            'staff_name' => $data['staff_name'],
            'staff_birth' => $data['staff_birth'],
            'staff_gender' => $data['staff_gender'],
            'staff_address' => $data['staff_address'],
            'staff_phone' => $data['staff_phone'],
            'staff_email' => $data['staff_email'],
            'cmnd' => $data['cmnd'],
            'bank' => $data['bank'],
            'account' => $data['account'],
            );*/
	        return $this->update($this->table,$data,$where);
        }
        
    }
    public function deletePosition($id){
    	if ($this->getPosition($id)) {
    		return $this->delete($this->table,array('position_id'=>$id));
    	}
    }
    public function getPosition($id){
        return $this->getByID($this->table,$id);
    }
    public function getPositionByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllPositionByWhere($id){
        return $this->query('SELECT * FROM position WHERE position_id != '.$id);
    }
    public function queryPosition($sql){
        return $this->query($sql);
    }
    public function getLastPosition(){
        return $this->getLast($this->table);
    }
}
?>