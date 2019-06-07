<?php

Class sparesubModel Extends baseModel {
	protected $table = "spare_sub";

	public function getAllStock($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createStock($data) 
    {    
        /*$data = array(
        	'Stock_id' => $data['Stock_id'],
        	'Stock_name' => $data['Stock_name'],
        	'Stock_birth' => $data['Stock_birth'],
        	'Stock_gender' => $data['Stock_gender'],
            'Stock_address' => $data['Stock_address'],
            'Stock_phone' => $data['Stock_phone'],
            'Stock_email' => $data['Stock_email'],
            'cmnd' => $data['cmnd'],
            'bank' => $data['bank'],
            'account' => $data['account'],
        	);*/

        return $this->insert($this->table,$data);
    }
    public function updateStock($data,$where) 
    {    
        if ($this->getStockByWhere($where)) {
        	/*$data = array(
            'Stock_id' => $data['Stock_id'],
            'Stock_name' => $data['Stock_name'],
            'Stock_birth' => $data['Stock_birth'],
            'Stock_gender' => $data['Stock_gender'],
            'Stock_address' => $data['Stock_address'],
            'Stock_phone' => $data['Stock_phone'],
            'Stock_email' => $data['Stock_email'],
            'cmnd' => $data['cmnd'],
            'bank' => $data['bank'],
            'account' => $data['account'],
            );*/
	        return $this->update($this->table,$data,$where);
        }
        
    }
    public function deleteStock($id){
    	if ($this->getStock($id)) {
    		return $this->delete($this->table,array('spare_sub_id'=>$id));
    	}
    }
    public function getStock($id){
        return $this->getByID($this->table,$id);
    }
    public function getStockByWhere($where){
    	return $this->getByWhere($this->table,$where);
    }
    public function getAllStockByWhere($id){
        return $this->query('SELECT * FROM spare_sub WHERE spare_sub_id != '.$id);
    }
    public function getLastStock(){
        return $this->getLast($this->table);
    }
    public function queryStock($sql){
        return $this->query($sql);
    }
}
?>