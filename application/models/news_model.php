<?php 
class News_Model extends Isp_Model 
{
 	var $table_name  = 'sos_news';
	var $limit = 10;
	var $id_key = "news_id";
        
        function __construct()
        {
            // Call the Model constructor
            parent::__construct();
            $this->limit = 10;
            
        }
	
        //select the slected rows from product table.
	function __select($form_data)
	{
            $forms = $this->bind($form_data, $this->table_name);
            $this->db->select('*');
            $this->db->from($this->table_name);
            $this->db->where($forms[$this->table_name]);
            $qry = $this->db->get();
            return $qry;
	}
        
        //select all rows from the product table
        function __select_all_data($order_by = '',$form_data = array())
        {
            $forms = $this->bind($form_data, $this->table_name);
            $this->db->select('*');
            $this->db->from($this->table_name);
            if($order_by != '')
             $this->db->order_by($order_by, "asc");
            $qry = $this->db->get();
            return $qry;
	 }
         
         //select all rows from the product table
        function __select_all_datas_table($table_name, $order_by = '',$form_data = array())
        {
            $forms = $this->bind($form_data, $table_name);
            $this->db->select('*');
            $this->db->from($table_name);
            if($order_by != '')
             $this->db->order_by($order_by, "asc");
            $qry = $this->db->get();
            return $qry;
	 }
         
         //select the selected rows
        function __select_table($form_data, $table_name, $order_by='')
	{
            $forms = $this->bind($form_data, $table_name);
            $this->db->select('*');
            $this->db->from($table_name);
            $this->db->where($forms[$table_name]); 
            if($order_by != '')
              $this->db->order_by($order_by, "asc");
            $qry = $this->db->get();
            return $qry;
	}
        
        //update the value in the table
        function __update_table($where, $form_data, $table_name)
	{
            $this->db->where($where);
            $this->db->update($table_name, $form_data); 
	} 
        
        //insert the new row in the table
        public function __insert_table($form_data, $table_name)
	{
            //pre($form_data);
            $forms = $this->bind($form_data, $table_name);
            $this->msg = 'insert';
            $this->db->insert($table_name,$forms[$table_name]);
            return $this->db->insert_id();
	}
}