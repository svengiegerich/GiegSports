<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Sports_model extends CI_Model {

	function getSports($sport_section) {
		
		$this->load->model('sport_groups_model');
		$sport_groups = $this->sport_groups_model->getAllSportGroups($sport_section);
		
		$sql_query_string = 'SELECT * FROM sports WHERE ';
		//um nur die Sportarten der aktuellen Sport Section zu bekommen
		foreach ($sport_groups as $sport_group_id => $sport_group) {
			$sql_query_string .= ' sport_group_id = ' . $sport_group_id . ' OR';
		}
		$sql_get_sports = substr($sql_query_string, 0,-3);
		$query_get_sports = $this->db->query($sql_get_sports);
		
		if ($query_get_sports->num_rows() > 0) {
			foreach ($query_get_sports->result() as $sport) {
				
				$sports[$sport->sport_id]['sport_name'] = $sport->sport_name;
				$sports[$sport->sport_id]['sport_group_id'] = $sport->sport_group_id;
			}
		return $sports;
		}
	}
	
	function getSportsBySportGroups($sport_section) {
	
		$sql_get_sports_from_sportgroup = 'SELECT * FROM sports';
		
		$query_get_sports_from_sportgroup = $this->db->query($sql_get_sports_from_sportgroup);
		if ($query_get_sports_from_sportgroup->num_rows() > 0) {
			foreach ($query_get_sports_from_sportgroup->result() as $sport) {
				$sports[$sport->sport_group_id][$sport->sport_id]['id'] = $sport->sport_id;
				$sports[$sport->sport_group_id][$sport->sport_id]['name'] = $sport->sport_name;
			}
		return $sports;
		}
	
	}
	
	function getAllSportsNames($sport_section) {
		$sql_get_all_sports_names = 'SELECT * FROM sports';
		
		$query_get_all_sports_names = $this->db->query($sql_get_all_sports_names);
		
		if ($query_get_all_sports_names->num_rows() > 0) {
			foreach ($query_get_all_sports_names->result() as $sport) {
				
				$sports_names[$sport->sport_name] = $sport->sport_id;
			}
		return $sports_names;
		}
	}
	
	function getSport($sport_id) {
		$sql_get_sport = 'SELECT * FROM sports WHERE sport_id = ' . $sport_id;
		$query_get_sport = $this->db->query($sql_get_sport);
		if ($query_get_sport->num_rows() == 1) {
			foreach ($query_get_sport->result() as $sport_entry) {
				foreach ($sport_entry as $sport_column_id => $sport_column_value) {
					$sport[$sport_column_id] = $sport_column_value;
				}
			}
			return $sport;
		}
	}
	
	function insertSport($sport_name, $sport_group_id) {
		$sql_insert_sport = "INSERT INTO sports (sport_name, sport_group_id) VALUES ('$sport_name', $sport_group_id)";
		$this->db->query($sql_insert_sport);
		
		return $this->db->insert_id();
	}
}
?>