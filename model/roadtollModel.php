<?php

Class roadtollModel Extends baseModel {
	protected $table = "road_toll";

	public function getAllRoad($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createRoad($data) 
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
    public function updateRoad($data,$where) 
    {    
        if ($this->getRoadByWhere($where)) {
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
    public function deleteRoad($id){
    	if ($this->getRoad($id)) {
    		return $this->delete($this->table,array('road_toll_id'=>$id));
    	}
    }
    public function getRoad($id){
        return $this->getByID($this->table,$id);
    }
    public function getRoadByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllRoadByWhere($id){
        return $this->query('SELECT * FROM road_toll WHERE road_toll_id != '.$id);
    }
    public function queryRoad($sql){
        return $this->query($sql);
    }
    public function getLastRoad(){
        return $this->getLast($this->table);
    }
}
?>