<?php

Class shippingModel Extends baseModel {
	protected $table = "shipping";

	public function getAllShipping($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createShipping($data) 
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
    public function updateShipping($data,$where) 
    {    
        if ($this->getShippingByWhere($where)) {
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
    public function deleteShipping($id){
    	if ($this->getShipping($id)) {
    		return $this->delete($this->table,array('shipping_id'=>$id));
    	}
    }
    public function getShipping($id){
        return $this->getByID($this->table,$id);
    }
    public function getShippingByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllShippingByWhere($id){
        return $this->query('SELECT * FROM shipping WHERE shipping_id != '.$id);
    }
    public function queryShipping($sql){
        return $this->query($sql);
    }
    public function getLastShipping(){
        return $this->getLast($this->table);
    }
}
?>