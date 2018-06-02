<?php

Class brandModel Extends baseModel {
	protected $table = "brand";

	public function getAllBrand($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createBrand($data) 
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
    public function updateBrand($data,$where) 
    {    
        if ($this->getBrandByWhere($where)) {
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
    public function deleteBrand($id){
    	if ($this->getBrand($id)) {
    		return $this->delete($this->table,array('brand_id'=>$id));
    	}
    }
    public function getBrand($id){
        return $this->getByID($this->table,$id);
    }
    public function getBrandByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllBrandByWhere($id){
        return $this->query('SELECT * FROM brand WHERE brand_id != '.$id);
    }
    public function queryBrand($sql){
        return $this->query($sql);
    }
    public function getLastBrand(){
        return $this->getLast($this->table);
    }
}
?>