<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Contests_model extends CI_Model {

	function getCurrentContest() {
		$sql_get_current_contest = 'SELECT * FROM contests where used = 1';
		
		$query_get_current_contest = $this->db->query($sql_get_current_contest);
		
		if ($query_get_current_contest->num_rows() == 1) {
			foreach ($query_get_current_contest->result() as $contest) {
				$current_contest['contest_id'] = $contest->contest_id;
				$current_contest['contest_name'] = $contest->contest_name;
				$current_contest['contest_year'] = $contest->contest_year;
				$current_contest['contest_sport_section'] = $contest->contest_sport_section;
				$current_contest['used'] = $contest->used;
			}
			return $current_contest;
		}
		return FALSE;
	}
	
	function getAllContests() {
		$sql_get_all_contests = 'SELECT * FROM contests';
		
		$query_get_all_contests = $this->db->query($sql_get_all_contests);
		if ($query_get_all_contests->num_rows() > 0) {
			foreach ($query_get_all_contests->result() as $contest) {
				$contests[$contest->contest_id]['contest_name'] = $contest->contest_name;
				$contests[$contest->contest_id]['contest_year'] = $contest->contest_year;
				$contests[$contest->contest_id]['contest_sport_section'] = $contest->contest_sport_section;
				$contests[$contest->contest_id]['used'] = $contest->used;
			}
			return $contests;
		}
		return FALSE;
	}
	
	function getContest($contest_id) {
		$sql_get_contest = 'SELECT * FROM contests WHERE contest_id = ' . $contest_id;
		
		$query_get_contest = $this->db->query($sql_get_contest);
		if ($query_get_contest->num_rows() == 1) {
			foreach ($query_get_contest->result() as $contest_data) {
				$contest['contest_id'] = $contest_data->contest_id;
				$contest['contest_name'] = $contest_data->contest_name;
				$contest['contest_year'] = $contest_data->contest_year;
				$contest['contest_sport_section'] = $contest_data->contest_sport_section;
				$contest['used'] = $contest_data->used;
			}
			return $contest;
		}
		return FALSE;
	}
	
	function getPointsUsed() {
		$points_table_string = 'points_';
		
		$sql_get_points_table_used = 'SELECT contest_id FROM contests WHERE used = 1';
		$query_get_points_table_used = $this->db->query($sql_get_points_table_used);
		if ($query_get_points_table_used->num_rows() > 0) {
		
			foreach ($query_get_points_table_used->result() as $contest_table){
				$points_table_string .= $contest_table->contest_id;
			}
			return $points_table_string;
		} 
	}
	
	function getConditionsUsed() {
		$sql_get_conditions_type_used = 'SELECT contest_conditon_type FROM contests WHERE used = 1';
		
		$query_get_conditions_type_used = $this->db->query($sql_get_conditions_type_used);
		if ($query_get_conditions_type_used->num_rows() > 0) {
			foreach ($query_get_conditions_type_used->result() as $contest_table) {
				$conditions = $contest_table->contest_conditon_type; 
			}
		return $conditions;
		}
	}
	
	function getContestSportSection() {
		$sql_get_contest_sport_section = 'SELECT contest_sport_section FROM contests WHERE used = 1';
		
		$query_get_contest_sport_section = $this->db->query($sql_get_contest_sport_section);
		if ($query_get_contest_sport_section->num_rows() > 0) {
			foreach ($query_get_contest_sport_section->result() as $sport_section) {
				$contest_sport_section = $sport_section->contest_sport_section;
			}
		return $contest_sport_section; 
		}
	}
	
	function createContest($contest_info) {
		$this->setAllContestsUnused();
		
		$year = $contest_info['contest_year'];
		
		$sql_create_contest = "INSERT INTO `contests` (`contest_name`, `contest_year`, `contest_sport_section`, `contest_conditon_type`, `used`)
								VALUES ('" . $contest_info['contest_name'] . "', '" . $year ."', '" . $contest_info['contest_sport_section'] . "', 'years', '1')"; 
		$query_create_contest = $this->db->query($sql_create_contest);
	}
	
	function deleteContest($contest_id) {
		$contest = $this->getContest($contest_id);
		
		$sql_delete_contest = 'DELETE FROM ' . 'contests ' . ' WHERE contest_id = ' . $contest_id;
		$query_delete_contest = $this->db->query($sql_delete_contest);
		
		//Auch die benutzten Sport Gruppen müssen gelöscht werden
		$sql_remove_all_contest_sport_groups = 'DELETE FROM contests_sport_groups WHERE contest_id = ' . $contest_id;
		$query_remove_all_contest_sport_groups = $this->db->query($sql_remove_all_contest_sport_groups);
		
		if ($contest['used'] != 0) {
			$sql_get_hap_contest = 'SELECT contest_id FROM contests LIMIT 1';
			$query_get_hap_contest = $this->db->query($sql_get_hap_contest);
			if ($query_get_hap_contest->num_rows() > 0) {
				foreach ($query_get_hap_contest->result() as $contest) {
					$this->UpdateContestUsed($contest->contest_id);
				}
			}
		}
	}
	
	function setAllContestsUnused() {
		$sql_set_all_contests_unused = 'UPDATE contests SET used = 0';
		$this->db->query($sql_set_all_contests_unused);
	}
	
	function UpdateContestUsed($contest_id) {
		$this->setAllContestsUnused();
		
		$sql_update_contest_used = 'UPDATE contests SET used = 1 WHERE contest_id = ' . $contest_id;
		$query_update_contest_used = $this->db->query($sql_update_contest_used);
	}
	
	function UpdateConditionsTableUsed($contest_condition_type) {
		
		$sql_update_conditions_type_used = 'UPDATE contests SET contest_conditon_type = ' . "'$contest_condition_type'" . ' WHERE used = 1';
		
		$query_update_conditions_type_used = $this->db->query($sql_update_conditions_type_used);
	}
	
	
	
	
	// Außerhalb
	function table_exists($table) {
	     $exists = mysql_query("SELECT 1 FROM `$table` LIMIT 0");
	     if ($exists) {
	      	return true;
	     }
	     return false;
	}
	
	function tableEmpty($table) {
		$points_table_used = $this->getPointsUsed();
		$sql_table_empty = 'SELECT * FROM ' . $points_table_used . ' WHERE 1 LIMIT 1';
		
		$query_table_empty = $this->db->query($sql_table_empty);
		if ($query_table_empty->num_rows() > 0) {
			return false;
		}
		return true;
	}

}
?>