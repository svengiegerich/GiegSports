<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Years_model extends CI_Model {
	
	function getYearId($year_name) {
		$sql_get_year_id = 'SELECT year_id FROM years WHERE year_name = ' . $year_name;
		
		$query_get_year_id = $this->db->query($sql_get_year_id);
		if ($query_get_year_id->num_rows() >0) {
			foreach ($query_get_year_id->result() as $year_id) {
				$year_id = $year_id->year_id;
			}
		return $year_id;
		}
	}
	
	function getYearName($year_id) {
		
		$sql_get_year_name = 'SELECT year_name FROM years WHERE year_id = ' . $year_id;
		$query_get_year_name = $this->db->query($sql_get_year_name);
		if ($query_get_year_name->num_rows() > 0) {
			foreach ($query_get_year_name->result() as $result_year_name) {
				$year_name = $result_year_name->year_name;
			}
		return $year_name;
		}
	}
}
?>