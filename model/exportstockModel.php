<?php

Class exportstockModel Extends baseModel {
	protected $table = "export_stock";

	public function getAllStock($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createStock($data) 
    {    
        /*$data = array(
        	'Stockname' => $data['Stockname'],
        	'password' => $data['password'],
        	'create_time' => $data['create_time'],
        	'role' => $data['role'],
        	);*/
        return $this->insert($this->table,$data);
    }
    public function updateStock($data,$id) 
    {    
        if ($this->getStockByWhere($id)) {
        	/*$data = array(
	        	'Stockname' => $data['Stockname'],
	        	'password' => $data['password'],
	        	'create_time' => $data['create_time'],
	        	'role' => $data['role'],
	        	);*/
	        return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteStock($id){
    	if ($this->getStock($id)) {
    		return $this->delete($this->table,array('export_stock_id'=>$id));
    	}
    }
    public function getStock($id){
    	return $this->getByID($this->table,$id);
    }
    public function getStockByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllStockByWhere($id){
        return $this->query('SELECT * FROM export_stock WHERE export_stock_id != '.$id);
    }
    public function getLastStock(){
        return $this->getLast($this->table);
    }
    public function queryStock($sql){
        return $this->query($sql);
    }
}
?>