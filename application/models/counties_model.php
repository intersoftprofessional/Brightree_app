<?php 
class Counties_Model extends Isp_Model 
{
	var $limit = 10;
	var $id_key = "list_id";
        
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->limit = 10;
		
	}
    
	function getCounties()
	{        
		$this->db->select('*');
		$this->db->from('county_taxzone_mapping');
		$this->db->where('published', '1');
		$this->db->group_by("county"); 
		$qry = $this->db->get()->result();
		return $qry;
	}
	
	function getCountiesTaxzones($id='')
	{
		if($id!='')
		{
			$qry = 'select ID, county, taxzone_name, taxzone_ID, published from county_taxzone_mapping where 1=1 and county = (select county from county_taxzone_mapping where ID = '.$id.')';
			$results = $this->db->query($qry);
			return $results->result();
		}
	} 
	
}