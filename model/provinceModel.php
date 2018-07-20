<?php

Class provinceModel Extends baseModel {
	protected $table = "province";

	public function getAllProvince($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createProvince($data) 
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
    public function updateProvince($data,$where) 
    {    
        if ($this->getProvinceByWhere($where)) {
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
    public function deleteProvince($id){
    	if ($this->getProvince($id)) {
    		return $this->delete($this->table,array('province_id'=>$id));
    	}
    }
    public function getProvince($id){
        return $this->getByID($this->table,$id);
    }
    public function getProvinceByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllProvinceByWhere($id){
        return $this->query('SELECT * FROM province WHERE province_id != '.$id);
    }
    public function queryProvince($sql){
        return $this->query($sql);
    }
    public function getLastProvince(){
        return $this->getLast($this->table);
    }
}
?>