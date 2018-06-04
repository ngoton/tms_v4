<?php

Class warehouseModel Extends baseModel {
	protected $table = "warehouse";

	public function getAllWarehouse($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createWarehouse($data) 
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
    public function updateWarehouse($data,$where) 
    {    
        if ($this->getWarehouseByWhere($where)) {
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
    public function deleteWarehouse($id){
    	if ($this->getWarehouse($id)) {
    		return $this->delete($this->table,array('warehouse_id'=>$id));
    	}
    }
    public function getWarehouse($id){
        return $this->getByID($this->table,$id);
    }
    public function getWarehouseByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllWarehouseByWhere($id){
        return $this->query('SELECT * FROM warehouse WHERE warehouse_id != '.$id);
    }
    public function queryWarehouse($sql){
        return $this->query($sql);
    }
    public function getLastWarehouse(){
        return $this->getLast($this->table);
    }
}
?>