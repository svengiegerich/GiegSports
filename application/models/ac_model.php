<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class AC_model extends CI_Model {
	
	function getAllAC() {
		
		$sql_get_all_ac = 'SELECT * FROM ac';
		
		$query_get_all_ac = $this->db->query($sql_get_all_ac);
		if ($query_get_all_ac->num_rows() >0) {
			foreach ($query_get_all_ac->result() as $all_ac) {
				$ac_id = $all_ac->ac_id;
				$ac_value = $all_ac->ac_value;
				$ac_type = $all_ac->ac_type;
				$sport_id = $all_ac->sport_id;
				$ac_gender = $all_ac->ac_gender;
				
				$ac[$ac_type][$ac_gender][$sport_id]['ac_value'] = $ac_value;
			}
		return $ac;
		} 
	}
	
	function getA($sport_id, $competitor_gender) {
		
		$sql_get_a = 'SELECT ac_value FROM ac WHERE ac_type = ' . "'a'" . 'AND sport_id = ' . $sport_id . ' AND ac_gender = ' . "'$competitor_gender'";
		
		$query_get_a = $this->db->query($sql_get_a);
		if ($query_get_a->num_rows() > 0) {
			foreach ($query_get_a->result() as $a) {
				$a = $a->ac_value;
			}
		return $a;
		}
	}
	
	function getC($sport_id, $competitor_gender) {
		
		$sql_get_c = 'SELECT ac_value FROM ac WHERE ac_type = ' . "'c'" . ' AND sport_id = ' . $sport_id . ' AND ac_gender = ' . "'$competitor_gender'";
		
		$query_get_c = $this->db->query($sql_get_c);
		if ($query_get_c->num_rows() > 0) {
			foreach ($query_get_c->result() as $c) {
				$c = $c->ac_value;
			}
		return $c;
		}
	}
}
?>