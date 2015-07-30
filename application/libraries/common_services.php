<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * Address_Verify Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 */
class Common_Services {

    var $OBJ;    

    /**
     * Constructor
     *
     * @access	public
     * @param	array	initialization parameters
     */
    public function __construct($params = array()) {
        require_once("class/class.commonwebservice.php");        
        
		//get instance
		$this->ISP =& get_instance();
		$this->OBJ = new CommonWebService("https://webservices.brightree.net/v0100-1501/ReferenceDataService/ReferenceDataService.svc","shaeva@chspharmapitest","coffee4u");
        
    }
	
    /**
     * Initialize Preferences
     *
     * @access	public
     * @param	array	initialization parameters
     * @return	void
     */
    public function FetchTaxZones($params = array()){
		$result = $this->OBJ->TaxZoneFetchAll();

		$xml = simplexml_load_string((string) $result);
		$records =$xml->children('s',true)->children()->TaxZoneFetchAllResponse->children()->TaxZoneFetchAllResult->children('a',true)->Items->children('b',true);

		$return=array();
		$count=1;

		if($records && ( count($records) > 0)) {			
			foreach($records->TaxZone as $key => $taxzone){
				$return[$count]['taxzone_ID'] = (string) $taxzone->children()->ID;
				$return[$count]['taxzone_name'] = (string) $taxzone->children()->Value;
				$count++;
			}				
		}
		return $return;
	}
}