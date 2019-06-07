<?php

Class shipmenttempModel Extends baseModel {
	protected $table = "shipment_temp";

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
    		return $this->delete($this->table,array('shipment_temp_id'=>$id));
    	}
    }
    public function getShipment($id){
    	return $this->getByID($this->table,$id);
    }
    public function getShipmentByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllShipmentByWhere($id){
        return $this->query('SELECT * FROM shipment_temp WHERE shipment_temp_id != '.$id);
    }
    public function getLastShipment(){
        return $this->getLast($this->table);
    }
    public function checkShipment($id,$shipment_from,$shipment_to,$vehicle,$shipment_date,$shipment_round){
        return $this->query('SELECT * FROM shipment_temp WHERE shipment_id != '.$id.' AND shipment_from = "'.$shipment_from.'" AND shipment_to = '.$shipment_to.' AND vehicle = '.$vehicle.' AND shipment_date = '.$shipment_date.' AND shipment_round = '.$shipment_round);
    }
    public function checkUpdate($vehicle,$shipment_round,$shipment_date){
        return $this->query('SELECT * FROM shipment_temp WHERE vehicle = '.$vehicle.' AND shipment_date >= '.strtotime('-1 month' ,strtotime('30-'.date('m-Y',$shipment_date))).' AND shipment_date <= '.strtotime('29-'.date('m-Y',$shipment_date)).' AND shipment_round != '.$shipment_round.' AND shipment_update = 0');
    }
    public function queryShipment($sql){
        return $this->query($sql);
    }
    public function checkComplete($vehicle,$shipment_round,$shipment_date){
        return $this->query('SELECT * FROM shipment_temp WHERE vehicle = '.$vehicle.' AND shipment_date >= '.strtotime('-1 month' ,strtotime('30-'.date('m-Y',$shipment_date))).' AND shipment_date <= '.strtotime('29-'.date('m-Y',$shipment_date)).' AND shipment_round != '.$shipment_round.' AND shipment_complete = 0');
    }
}
?>