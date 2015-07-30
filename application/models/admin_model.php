<?php 
class Admin_Model extends Isp_Model {
 
    var $username;
    var $password;
    var $table_name = 'sos_retailers';
    var $limit = 10;
    var $id_key = "retailler_id";
        
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->limit = 10;
    }
	
    function __select($form_data)
    {
      $forms = $this->bind($form_data, $this->table_name);
      $this->db->select('*');
      $this->db->from($this->table_name);
      $this->db->where($forms[$this->table_name]);
      $qry = $this->db->get();
      return $qry;
    }
    
    
}
