<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Sport_groups_model extends CI_Model {
	 
	function getAllSportGroups($sport_section) {
		$sql_get_sports = 'SELECT * FROM sport_groups WHERE sport_section = ' . $sport_section;
	  	
	  	$query_get_sports = $this->db->query($sql_get_sports);
	  	if ($query_get_sports->num_rows() > 0) {
	  		foreach ($query_get_sports->result() as $sport) {
	  			
	  			$sport_groups[$sport->sport_group_id]['name'] = $sport->sport_group_name;
	  			$sport_groups[$sport->sport_group_id]['calcus'] = $sport->calcus;
	  		}
	  	return $sport_groups;
	  	}
	  }	
	  
	  function getSportGroup($sport_group_id) {
	  	$sql_get_sport_group = 'SELECT sport_group_name, calcus FROM sport_groups WHERE sport_group_id = ' . $sport_group_id;
		$query_get_sport_group = $this->db->query($sql_get_sport_group);
		if ($query_get_sport_group->num_rows() == 1) {
			foreach ($query_get_sport_group->result() as $sport_group_data) {
		  		$sport_group['name'] = $sport_group_data->sport_group_name;
		  		$sport_group['calcus'] = $sport_group_data->calcus;
		  	}
		  	return $sport_group;
		}
	  }
	  
	  function getSportGroupName($sport_group_id) {
	  	$sql_get_sport_group_name = 'SELECT sport_group_name FROM sport_groups WHERE sport_group_id = ' . $sport_group_id;
	  	$query_get_sport_group_name = $this->db->query($sql_get_sport_group_name);
	  	if ($query_get_sport_group_name->num_rows() == 1) {
	  		foreach ($query_get_sport_group_name->result() as $sport_group) {
	  			$sport_group_name = $sport_group->sport_group_name;
	  		}
	  		return $sport_group_name;
	  	}
	  }
	  
	 function getCalcus($sport_group_id) {
	 	$sql_get_calcus = 'SELECT calcus FROM sport_groups WHERE sport_group_id = ' .  $sport_group_id;
	 	
	 	$sql_get_calcus = $this->db->query($sql_get_calcus);
	 	
	 	if ($sql_get_calcus->num_rows() > 0) {
	 		foreach ($sql_get_calcus->result() as $calcus) {
	 			$calcus = $calcus->calcus;
	 		}
	 		return $calcus;
	 	}
	 }
	 
	 function updateSportGroups($sql_update_sport_groups) {
	 	$rows_added = 0;
	 	
	 	foreach ($sql_update_sport_groups as $update_sport_group) {
	 		$sql_update_sport_groups = $update_sport_group;
	 		$this->db->query($sql_update_sport_groups);
	 		$rows_added++;
	 	}
	 	return $rows_added;
	 }	
	 
	 function insertSportGroup($sport_group_name, $sport_section, $calcus) {
	 	$sql_insert_sport_group = "INSERT INTO sport_groups (sport_group_name, sport_section, calcus) VALUES ('$sport_group_name', $sport_section, $calcus)";
	 	$this->db->query($sql_insert_sport_group);
	 	
	 	return $this->db->insert_id();
	 }

}
?>