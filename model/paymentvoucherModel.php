<?php



Class paymentvoucherModel Extends baseModel {

	protected $table = "payment_voucher";



	public function getAllPayment($data = null,$join = null) 

    {

        return $this->fetchAll($this->table,$data,$join);

    }



    public function createPayment($data) 

    {    

        /*$data = array(

        	'Paymentname' => $data['Paymentname'],

        	'password' => $data['password'],

        	'create_time' => $data['create_time'],

        	'role' => $data['role'],

        	);*/

        return $this->insert($this->table,$data);

    }

    public function updatePayment($data,$id) 

    {    

        if ($this->getPaymentByWhere($id)) {

        	/*$data = array(

	        	'Paymentname' => $data['Paymentname'],

	        	'password' => $data['password'],

	        	'create_time' => $data['create_time'],

	        	'role' => $data['role'],

	        	);*/

	        return $this->update($this->table,$data,$id);

        }

        

    }

    public function deletePayment($id){

    	if ($this->getPayment($id)) {

    		return $this->delete($this->table,array('payment_voucher_id'=>$id));

    	}

    }

    public function getPayment($id){

    	return $this->getByID($this->table,$id);

    }

    public function getPaymentByWhere($where){

        return $this->getByWhere($this->table,$where);

    }

    public function getAllPaymentByWhere($id){

        return $this->query('SELECT * FROM payment_voucher WHERE payment_voucher_id != '.$id);

    }

    public function getLastPayment(){

        return $this->getLast($this->table);

    }

    public function checkPayment($id){

        return $this->query('SELECT * FROM payment_voucher WHERE payment_voucher_id != '.$id);

    }

    public function queryPayment($sql){

        return $this->query($sql);

    }

}

?>