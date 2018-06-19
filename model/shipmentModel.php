<?php

Class shipmentModel Extends baseModel {
	protected $table = "shipment";

	public function getAllShipment($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createShipment($data) 
    {    
        /*$data = array(
        	'Shipment_id' => $data['Shipment_id'],
        	'Shipment_name' => $data['Shipment_name'],
        	'Shipment_birth' => $data['Shipment_birth'],
        	'Shipment_gender' => $data['Shipment_gender'],
            'Shipment_address' => $data['Shipment_address'],
            'Shipment_phone' => $data['Shipment_phone'],
            'Shipment_email' => $data['Shipment_email'],
            'cmnd' => $data['cmnd'],
            'bank' => $data['bank'],
            'account' => $data['account'],
        	);*/

        return $this->insert($this->table,$data);
    }
    public function updateShipment($data,$where) 
    {    
        if ($this->getShipmentByWhere($where)) {
        	/*$data = array(
            'Shipment_id' => $data['Shipment_id'],
            'Shipment_name' => $data['Shipment_name'],
            'Shipment_birth' => $data['Shipment_birth'],
            'Shipment_gender' => $data['Shipment_gender'],
            'Shipment_address' => $data['Shipment_address'],
            'Shipment_phone' => $data['Shipment_phone'],
            'Shipment_email' => $data['Shipment_email'],
            'cmnd' => $data['cmnd'],
            'bank' => $data['bank'],
            'account' => $data['account'],
            );*/
	        return $this->update($this->table,$data,$where);
        }
        
    }
    public function deleteShipment($id){
    	if ($this->getShipment($id)) {
    		return $this->delete($this->table,array('shipment_id'=>$id));
    	}
    }
    public function getShipment($id){
        return $this->getByID($this->table,$id);
    }
    public function getShipmentByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllShipmentByWhere($id){
        return $this->query('SELECT * FROM shipment WHERE shipment_id != '.$id);
    }
    public function getLastShipment(){
        return $this->getLast($this->table);
    }
    public function queryShipment($sql){
        return $this->query($sql);
    }
}
?>