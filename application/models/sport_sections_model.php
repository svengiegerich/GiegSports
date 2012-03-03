<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Sport_sections_model extends CI_Model {
	
	function sportSectionUsed() {
		$sql_get_sport_section_used = 'SELECT * FROM sport_sections WHERE used = 1';
		
		$query_get_sport_section_used = $this->db->query($sql_get_sport_section_used);
		
		if ($query_get_sport_section_used->num_rows() > 0) {
			foreach ($query_get_sport_section_used->result() as $sport_section_used) {
				$sport_section[$sport_section_used->sport_section_id]['name'] = $sport_section_used->sport_section_name;
			}
		return $sport_section;
		}
	} 
	
	function getSportSections() {
		$sql_get_sport_sections = 'SELECT * FROM sport_sections';
		
		$query_get_sport_sections = $this->db->query($sql_get_sport_sections);
		
		if ($query_get_sport_sections->num_rows() > 0) {
			foreach ($query_get_sport_sections->result() as $sport_section) {
				$sport_sections[$sport_section->sport_section_id] = $sport_section->sport_section_name;
			}
		return $sport_sections;
		}
	}
	
	function getSportSectionName($sport_section_id) {
		$sql_get_sport_section_name = 'SELECT * FROM sport_sections WHERE sport_section_id =' . $sport_section_id;
		
		$query_get_sport_section_name = $this->db->query($sql_get_sport_section_name);
		
		if ($query_get_sport_section_name->num_rows() > 0) {
			foreach ($query_get_sport_section_name->result() as $sport_section) {
				$sport_section_name = $sport_section->sport_section_name;
			} 
		return $sport_section_name;
		}
	}
}
?>