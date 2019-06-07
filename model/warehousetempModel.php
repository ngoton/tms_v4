<?php

Class warehousetempModel Extends baseModel {
	protected $table = "warehouse_temp";

	public function getAllWarehouse($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createWarehouse($data) 
    {    
        /*$data = array(
        	'Warehousename' => $data['Warehousename'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateWarehouse($data,$id) 
    {    
        if ($this->getWarehouseByWhere($id)) {
        	/*$data = array(
	        	'Warehousename' => $data['Warehousename'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteWarehouse($id){
    	if ($this->getWarehouse($id)) {
    		return $this->delete($this->table,array('warehouse_temp_id'=>$id));
    	}
    }
    public function getWarehouse($id){
    	return $this->getByID($this->table,$id);
    }
    public function getWarehouseByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllWarehouseByWhere($id){
        return $this->query('SELECT * FROM warehouse_temp WHERE warehouse_temp_id != '.$id);
    }
    public function getLastWarehouse(){
        return $this->getLast($this->table);
    }
    public function queryWarehouse($sql){
        return $this->query($sql);
    }
}
?>