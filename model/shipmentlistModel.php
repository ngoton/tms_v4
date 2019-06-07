<?php

Class shipmentlistModel Extends baseModel {
	protected $table = "shipment_list";

	public function getAllShipment($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createShipment($data) 
    {    
        /*$data = array(
        	'Shipmentname' => $data['Shipmentname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateShipment($data,$id) 
    {    
        if ($this->getShipmentByWhere($id)) {
        	/*$data = array(
	        	'Shipmentname' => $data['Shipmentname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteShipment($id){
    	if ($this->getShipment($id)) {
    		return $this->delete($this->table,array('shipment_list_id'=>$id));
    	}
    }
    public function getShipment($id){
    	return $this->getByID($this->table,$id);
    }
    public function getShipmentByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllShipmentByWhere($id){
        return $this->query('SELECT * FROM shipment_list WHERE shipment_list_id != '.$id);
    }
    public function getLastShipment(){
        return $this->getLast($this->table);
    }
    public function checkShipment($id){
        return $this->query('SELECT * FROM shipment_list WHERE shipment_list_id != '.$id);
    }
    public function queryShipment($sql){
        return $this->query($sql);
    }
}
?>