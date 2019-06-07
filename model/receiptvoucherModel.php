<?php



Class receiptvoucherModel Extends baseModel {

	protected $table = "receipt_voucher";



	public function getAllReceipt($data = null,$join = null) 

    {

        return $this->fetchAll($this->table,$data,$join);

    }



    public function createReceipt($data) 

    {    

        /*$data = array(

        	'Receiptname' => $data['Receiptname'],

        	'password' => $data['password'],

        	'create_time' => $data['create_time'],

        	'role' => $data['role'],

        	);*/

        return $this->insert($this->table,$data);

    }

    public function updateReceipt($data,$id) 

    {    

        if ($this->getReceiptByWhere($id)) {

        	/*$data = array(

	        	'Receiptname' => $data['Receiptname'],

	        	'password' => $data['password'],

	        	'create_time' => $data['create_time'],

	        	'role' => $data['role'],

	        	);*/

	        return $this->update($this->table,$data,$id);

        }

        

    }

    public function deleteReceipt($id){

    	if ($this->getReceipt($id)) {

    		return $this->delete($this->table,array('receipt_voucher_id'=>$id));

    	}

    }

    public function getReceipt($id){

    	return $this->getByID($this->table,$id);

    }

    public function getReceiptByWhere($where){

        return $this->getByWhere($this->table,$where);

    }

    public function getAllReceiptByWhere($id){

        return $this->query('SELECT * FROM receipt_voucher WHERE receipt_voucher_id != '.$id);

    }

    public function getLastReceipt(){

        return $this->getLast($this->table);

    }

    public function checkReceipt($id){

        return $this->query('SELECT * FROM receipt_voucher WHERE receipt_voucher_id != '.$id);

    }

    public function queryReceipt($sql){

        return $this->query($sql);

    }

}

?>