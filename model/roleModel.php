<?php

Class roleModel Extends baseModel {
	protected $table = "role";

	public function getAllRole($data = null,$join = null) 
    {
        return $this->fetchAll($this->table,$data,$join);
    }

    public function createRole($data) 
    {    
        /*$data = array(
            'Rolename' => $data['Rolename'],
            'password' => $data['password'],
            'create_time' => $data['create_time'],
            'role' => $data['role'],
            );*/
        return $this->insert($this->table,$data);
    }
    public function updateRole($data,$id) 
    {    
        if ($this->getRoleByWhere($id)) {
            /*$data = array(
                'Rolename' => $data['Rolename'],
                'password' => $data['password'],
                'create_time' => $data['create_time'],
                'role' => $data['role'],
                );*/
            return $this->update($this->table,$data,$id);
        }
        
    }
    public function deleteRole($id){
        if ($this->getRole($id)) {
            return $this->delete($this->table,array('role_id'=>$id));
        }
    }
    public function getRole($id){
        return $this->getByID($this->table,$id);
    }
    public function getRoleByWhere($where){
        return $this->getByWhere($this->table,$where);
    }
    public function getAllRoleByWhere($id){
        return $this->query('SELECT * FROM role WHERE role_id != '.$id);
    }
    public function getLastRole(){
        return $this->getLast($this->table);
    }

}
?>