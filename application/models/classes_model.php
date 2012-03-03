<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Classes_model extends CI_Model {
	
	//Gibt alle Klassennamen aus
	function getClasses() {
		$this->load->model('contests_model');
		$points_table_used = $this->contests_model->getPointsUsed();
		
	
		$sql_get_classes = 'SELECT * FROM classes ORDER BY class_name ASC';
		$query_get_classes = $this->db->query($sql_get_classes);
		
		if ($query_get_classes->num_rows() > 0) {
			foreach ($query_get_classes->result() as $class) {
				
				//überprüfen, ob es in dieser Klasse wirklich Teilnehmer gibt
				$sql_check_class = 'SELECT competitor_id FROM ' . $points_table_used . ' WHERE class_id = ' . $class->class_id;
				$query_check_class = $this->db->query($sql_check_class);
				if($query_check_class->num_rows() > 0) {
					$classes[$class->class_id] = $class;
				}
			}
			return $classes;
		}
	}
	
	function getClassId($class_name) {
		$sql_get_class_id = "SELECT class_id FROM classes WHERE class_name = '$class_name'";
		$query_get_class_id = $this->db->query($sql_get_class_id);
		if ($query_get_class_id->num_rows() >0) {
			foreach ($query_get_class_id->result() as $class_id) {
				$class_id = $class_id->class_id;
			}
			return $class_id;
		}
	}
	
	function getClassName($class_id) {
		$sql_get_class_name = 'SELECT class_name FROM classes WHERE class_id = ' . $class_id;
		$query_get_class_name = $this->db->query($sql_get_class_name);
		
		if($query_get_class_name->num_rows() > 0) {
			foreach ($query_get_class_name->result() as $class_name) {
				$class_name = $class_name->class_name;
			}
			return $class_name;
		}
		return false;
	}
	
	function getClassesTakenPart($points_table) {
		$this->load->model('points_model');
		$sport_columns = $this->points_model->getSportsColumns($points_table);
		$classes = $this->getClasses();
		
		foreach ($classes as $class_id => $class) {
			$sql_class_taken_part = 'SELECT competitor_id FROM ' . $points_table . ' WHERE class_id = '. $class_id . ' AND (';
			foreach ($sport_columns as $sport_colum_term => $sport_column) {
				$sql_class_taken_part .= $sport_colum_term . ' > 0 OR ';
			}
			$sql_class_taken_part = substr($sql_class_taken_part, 0, -4);
			$sql_class_taken_part .= ') LIMIT 1';
			$query_class_taken_part = $this->db->query($sql_class_taken_part);
			if ($query_class_taken_part->num_rows() == 1) {
				$classes_taken_part[$class_id] = $class;
			}
		}
		
		if (isset($classes_taken_part)) {
			return $classes_taken_part;
		}
		return false;
	}
}
?>