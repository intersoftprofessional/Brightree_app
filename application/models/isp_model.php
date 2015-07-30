<?php 

class Isp_Model extends CI_Model
{
    var $__relationships = array();
    var $table_name;
    var $id_key;
    var $msg = '';	
    public function __construct()
    {
        parent::__construct();
            $this->load->helper('date');
            $this->set_id_key();
            
    }  
	
	
	
    public function set_id_key()
    {
        if(!isset($this->id_key))
         $this->id_key = 'id'; 
    }
	 
	 
	//To get the categories to display in alphabetical order

	
	
	
	
	/**
	*	function bind
	*	params $post, form fields data
	*	$tables, table name
	*	$dbo, database object
	*	returns binded data for each table
	*
	*/
	
	public function bind($post, $tables)
	{
		$form = '';
		if(!is_array($tables))
		  $tables = (array) $tables;
		foreach($tables as $table )
		{
			$fields = $this->db->list_fields($table);
			if(count($fields)>0)
			{				
				foreach($fields as $k=>$v)
				{
					if(isset($post[$v]))
					{
						$form[$table][$v] = is_array($post[$v]) ? serialize($post[$v]) : $post[$v];
					}
				}
			}
		}
	   return $form;
	}
        
        public function current_date()
	 {
	   $current_date = date("y:m:d H:i:s");
	   return $current_date;
	 }
                                                                     
	public function __insert($form_data)
	{
	 //pre($form_data);
	 $forms = $this->bind($form_data, $this->table_name);
	 $this->msg = 'insert';
	 $this->db->insert($this->table_name,$forms[$this->table_name]);
	 return $this->db->insert_id();
	  
	}
         
	
}

?>