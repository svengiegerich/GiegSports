<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Contests_sport_groups_model extends CI_Model {

	function getSportGroupsUsed() {
		$this->load->model('contests_model');
		$this->load->model('sport_groups_model');
		
		$contest = $this->contests_model->getCurrentContest();
		$contest_id = $contest['contest_id'];
	
	 	$sql_get_sport_groups = 'SELECT * FROM contests_sport_groups WHERE contest_id = ' . $contest_id;
	 	
	 	$query_get_sport_groups = $this->db->query($sql_get_sport_groups);
	 	if ($query_get_sport_groups->num_rows() > 0) {
	 		foreach ($query_get_sport_groups->result() as $sport_group) {
	 			$sport_group_id = $sport_group->sport_group_id;
	 			$sport_group = $this->sport_groups_model->getSportGroup($sport_group_id);
	 			$sport_groups[$sport_group_id]['name'] = $sport_group['name'];
	 			$sport_groups[$sport_group_id]['calcus'] = $sport_group['calcus'];
	 		}
	 		return $sport_groups;
	 	}
	 }
	 
	 function updateContestSportGroups($sport_groups) {
	 	$this->load->model('contests_model');
	 	$contest = $this->contests_model->getCurrentContest();
	 	$contest_id = $contest['contest_id'];
	 	
	 	
	 	//HOLZHAMMERMETHODE
	 	//Remove all rows for the contest 
	 	$sql_remove_all_contest_sport_groups = 'DELETE FROM contests_sport_groups WHERE contest_id = ' . $contest_id;
	 	$query_remove_all_contest_sport_groups = $this->db->query($sql_remove_all_contest_sport_groups);
	 	
	 	//nur die gewünschten sport Gruppen wieder einfügen
	 	foreach ($sport_groups as $sport_group_id) {
	 		$this->addContestSportGroup($contest_id, $sport_group_id);
	 	}
	 }	
	 
	 function addContestSportGroup($contest_id, $sport_group_id) {
	 	$add_contest_sport_group = 'INSERT INTO contests_sport_groups (contest_id, sport_group_id) VALUES (' . $contest_id . ', ' . $sport_group_id . ')';
	 	$this->db->query($add_contest_sport_group);
	 }
}

?>