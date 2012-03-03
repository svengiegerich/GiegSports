<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Add_results_model extends CI_Model {
	
	/*function getClasses() {
		$query = $this->db->get('classes');
		
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$data[] = $row->class_name;
			}
			return $data;
		}
	}
	
	function getCompetitors($class_id) {
		//SQL-Befehle für:
		$sql_competitors_id = 'SELECT competitor_id FROM points_2011 WHERE class_id = ' . $class_id;
		$sql_competitors = 'SELECT competitor_name, competitor_id FROM competitors WHERE ';
		
		//Fragt alle Competitor_id´s in points_X ab wo die class_id übereinstimmt
		$query_competitors_id = $this->db->query($sql_competitors_id);	
		if ($query_competitors_id->num_rows() > 0) {
			foreach ($query_competitors_id->result() as $row) {
				$data['competitors_id'][] = $row->competitor_id;
				//Hängt die jeweiligen competitor_id´s an den 2.Sql Befehl
				$sql_competitors .= 'competitor_id = ' . $row->competitor_id . ' OR ';
			}
			
			//Kürzt den SQL-Befehl ab, da am Schluss ein 'OR' zu viel vorhanden ist
			$sql_competitors = substr($sql_competitors, 0, -4);
			
			//2.SQL-Befehl um die jeweils passenden Namen für die competitor_id zu finden
			$query_competitors = $this->db->query($sql_competitors);
			if ($query_competitors->num_rows() > 0) {
				foreach ($query_competitors->result() as $competitor) {
					$competitors[$competitor->competitor_id]['name'] = $competitor->competitor_name;
				}
			}
		} 
		return $competitors;
	}*/
	
	function getSportsName() {
		$sql_get_sports = 'SELECT * FROM sport_groups WHERE used != 0';
		
		$query_get_sports = $this->db->query($sql_get_sports);
		if ($query_get_sports->num_rows() > 0) {
			foreach ($query_get_sports->result() as $sport) {
				$sport_names[] = $sport->sport_group_name;
			}
		return $sport_names;
		}
	}	
	
	function insert_competitor_results($sql_competitor_results) {
		$rows_added = 0;
		foreach ($sql_competitor_results as $comeptitor_results) {
			$sql_insert_competitors_results = $comeptitor_results;
			$this->db->query($sql_insert_competitors_results);
			$rows_added++;
		}
	
		return $rows_added;
	}	
			
}