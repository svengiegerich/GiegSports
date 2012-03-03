<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Conditions_model extends CI_Model {
	function getAllConditions($sport_section, $condition_type) {
	
		$this->load->model('contests_model');
		$this->load->model('sport_groups_model');
		$this->load->model('contests_sport_groups_model');
		
		$sport_section = $this->contests_model->getContestSportSection();
		
		//$sport_groups = $this->contests_sport_groups_model->getSportGroupsUsed();
		$sport_groups = $this->sport_groups_model->getAllSportGroups($sport_section);
		
		$condition_type = $this->contests_model->getConditionsUsed();
		$sql_get_all_conditions = 'SELECT * FROM conditions WHERE condition_type = ' . "'$condition_type'" . ' AND sport_section = ' . $sport_section;
		
		$query_get_all_conditions = $this->db->query($sql_get_all_conditions);
		if ($query_get_all_conditions->num_rows() > 0) {
			foreach ($query_get_all_conditions->result() as $condition) {
				
				$condition_id = $condition->condition_id;
				$condition_gender = $condition->condition_competitor_gender;
				$condition_value = $condition->condition_value;
				$condition_winner = $condition->condition_winner;
				$condition_honor = $condition->condition_honor;
				
				$conditions[$condition_gender][$condition_value]['gender'] = $condition_gender;
				$conditions[$condition_gender][$condition_value]['value'] = $condition_value;
				$conditions[$condition_gender][$condition_value]['winner'] = $condition_winner;
				$conditions[$condition_gender][$condition_value]['honor'] = $condition_honor;
				$conditions[$condition_gender][$condition_value]['id'] = $condition_id;
				
				$sql_get_condition_sports = 'SELECT * FROM conditions_sports WHERE ';
				foreach ($sport_groups as $sport_group_id => $sport_group) {
					$sql_get_condition_sports .= '(condition_id = ' . $condition_id . ' AND sport_group_id = ' . $sport_group_id . ' ) OR ';
				}
				$sql_get_condition_sports = substr($sql_get_condition_sports, 0,-4);
				
				$query_get_condition_sports = $this->db->query($sql_get_condition_sports);
				if ($query_get_condition_sports->num_rows() > 0) {
					foreach ($query_get_condition_sports->result() as $sport) {
						$conditions[$condition_gender][$condition_value]['sport_group_' . $sport->sport_group_id] = $sport->sport_id;
					}
				}
			}
			return $conditions;
		}
	}
	
	function getPointsNeeded() {
		$this->load->model('contests_model');
		
		$condition_type = $this->contests_model->getConditionsUsed();
		$sport_section = $this->contests_model->getContestSportSection();
		
		$sql_get_points_needed = 'SELECT condition_value, condition_winner, condition_honor, condition_competitor_gender FROM conditions WHERE condition_type = ' . "'$condition_type' AND sport_section = " . $sport_section;
		
		$query_get_points_needed = $this->db->query($sql_get_points_needed);
		if ($query_get_points_needed->num_rows() > 0) {
			foreach ($query_get_points_needed->result() as $point_needed) {
				$condition_value = $point_needed->condition_value;
				$condition_competitor_gender = $point_needed->condition_competitor_gender;
				$condition_winner = $point_needed->condition_winner;
				$condition_honor = $point_needed->condition_honor;
				
				$points_needed[$condition_competitor_gender][$condition_value]['winner'] = $condition_winner;
				$points_needed[$condition_competitor_gender][$condition_value]['honor'] = $condition_honor;
			}
		return $points_needed;
		}
	}
	
	function insertConditionsUpdate($conditions_update) {
		$this->load->model('contests_model');
		$condition_type = $this->contests_model->getConditionsUsed();
		
		foreach ($conditions_update as $condition_id => $condition) {
			//updatet die punkte
			$sql_update_conditions_points = 'UPDATE conditions ' . 'SET ';
			foreach ($condition as $condition_field_index => $condition_field_value) {
				if ($condition_field_index == 'condition_honor' OR $condition_field_index == 'condition_winner' ) {
					//punkte
					$sql_update_conditions_points .= ' ' . $condition_field_index . ' = ' . $condition_field_value . ',';
				} else {
					//sports
					//muss abgeschnitten warden, da index = sport_group_3 Aber bnötigt 3
					$sql_update_conditions_sports = 'UPDATE conditions_sports SET ' ;
					$sport_group_id = substr($condition_field_index, -1);
					$sql_update_conditions_sports .= 'sport_id = ' . $condition_field_value;
					$sql_update_conditions_sports .= ' WHERE condition_id = ' . $condition_id . ' AND sport_group_id = ' . $sport_group_id;
					$this->db->query($sql_update_conditions_sports);	
				}
			}
					
			$sql_update_conditions_points = substr($sql_update_conditions_points, 0, -1);		
			$sql_update_conditions_points .= ' WHERE condition_id = ' . $condition_id;
			$this->db->query($sql_update_conditions_points);
		}
	}
	
	/*
	$conditions_reset = array(
				array(
					'WHERE' => array(
						'sport_section' => ,
						'condition_type' => '',
						'condition_value' => '',
						'condition_competitor_gender' => '',
					)
					'SET' => array(
						'honor' =>,
						'winner' =>,
					)
				)
			);
	*/
	
	function resetConditionsTable() {
		/*if ($sport_section == 1) {
			if ($condition_type == 'years') {
				$conditions_reset = array(
							array(
								'WHERE' => array(
									'sport_section' => 1,
									'condition_type' => 'years',
									'condition_value' => '9',
									'condition_competitor_gender' => 'W',
								)
								'SET' => array(
									'honor' =>,
									'winner' =>,
								)
							),
							array(
								'WHERE' => array(
									'sport_section' => 1,
									'condition_type' => 'years',
									'condition_value' => '9',
									'condition_competitor_gender' => 'M',
								)
								'SET' => array(
									'honor' =>,
									'winner' =>,
								)
							),
							array(
								'WHERE' => array(
									'sport_section' => 1,
									'condition_type' => 'years',
									'condition_value' => '10',
									'condition_competitor_gender' => 'W',
								)
								'SET' => array(
									'honor' =>,
									'winner' =>,
								)
							),
							array(
								'WHERE' => array(
									'sport_section' => 1,
									'condition_type' => 'years',
									'condition_value' => '10',
									'condition_competitor_gender' => 'M',
								)
								'SET' => array(
									'honor' =>,
									'winner' =>,
								)
							),
							array(
								'WHERE' => array(
									'sport_section' => 1,
									'condition_type' => 'years',
									'condition_value' => '11',
									'condition_competitor_gender' => 'W',
								)
								'SET' => array(
									'honor' =>,
									'winner' =>,
								)
							),
						);
			
			} else if ($condition_type == 'classes') {
			
			}
		} else if () {
		
		} else if () {
		
		}
		
		if (isset($conditions_reset)) {
			foreach ($conditions_reset as $condition) {
				$sql_reset_condition = 'UPDATE conditions';
				
				$sql_reset_condition = 'SET ';
				foreach ($condition['SET'] as $set_key as $set_value) 
					$sql_reset_condition .= $set_key . ' = ' . $set_value . ', ';
				}
				$sql_reset_condition = substr($sql_reset_condition, 0, -2);
				
				$sql_reset_condition = 'WHERE ';
				foreach ($condition['WHERE'] as $where_key => $where_value) {
					$sql_reset_condition = $where_key . ' = ' . $where_value . ', ';
				}
				$sql_reset_condition = substr($sql_reset_condition, 0, -2);
			}
		}*/
		$sql_truncate_conditions = 'TRUNCATE TABLE conditions';
		$this->db->query($sql_truncate_conditions);
		
		$sql_reset_conditions = "
			INSERT INTO `conditions` (`condition_id`, `condition_value`, `condition_competitor_gender`, `condition_winner`, `condition_honor`, `condition_type`, `sport_section`) VALUES
						(1, '9', 'W', 550, 725, 'years', 1),
						(2, '9', 'M', 525, 675, 'years', 1),
						(3, '10', 'W', 625, 825, 'years', 1),
						(4, '10', 'M', 600, 775, 'years', 1),
						(5, '11', 'W', 700, 900, 'years', 1),
						(6, '11', 'M', 675, 875, 'years', 1),
						(7, '12', 'W', 775, 975, 'years', 1),
						(8, '12', 'M', 750, 975, 'years', 1),
						(9, '13', 'W', 825, 1025, 'years', 1),
						(10, '13', 'M', 825, 1050, 'years', 1),
						(11, '14', 'W', 850, 1050, 'years', 1),
						(12, '14', 'M', 900, 1125, 'years', 1),
						(13, '15', 'W', 875, 1075, 'years', 1),
						(14, '15', 'M', 975, 1225, 'years', 1),
						(15, '16', 'W', 900, 1100, 'years', 1),
						(16, '16', 'M', 1050, 1325, 'years', 1),
						(17, '17', 'W', 925, 1125, 'years', 1),
						(18, '18', 'W', 950, 1150, 'years', 1),
						(19, '19', 'W', 950, 1150, 'years', 1),
						(20, '17', 'M', 1125, 1400, 'years', 1),
						(21, '18', 'M', 1200, 1475, 'years', 1),
						(22, '19', 'M', 1275, 1550, 'years', 1),
						(30, '5', 'M', 700, 900, 'classes', 1),
						(31, '5', 'W', 675, 875, 'classes', 1),
						(32, '6', 'M', 775, 975, 'classes', 1),
						(33, '6', 'W', 750, 975, 'classes', 1),
						(34, '7', 'M', 825, 1025, 'classes', 1),
						(35, '7', 'W', 825, 1050, 'classes', 1),
						(36, '8', 'M', 850, 1050, 'classes', 1),
						(37, '8', 'W', 900, 1125, 'classes', 1),
						(38, '9', 'M', 875, 1075, 'classes', 1),
						(39, '9', 'W', 975, 1225, 'classes', 1),
						(40, '10', 'M', 900, 1100, 'classes', 1),
						(41, '10', 'W', 1050, 1325, 'classes', 1),
						(42, '11', 'M', 925, 1125, 'classes', 1),
						(43, '11', 'W', 1125, 1400, 'classes', 1),
						(44, '12', 'M', 950, 1150, 'classes', 1),
						(45, '12', 'W', 1200, 1475, 'classes', 1),
						(46, '10', 'M', 15, 27, 'years', 2),
						(47, '10', 'W', 15, 27, 'years', 2),
						(48, '11', 'W', 15, 27, 'years', 2),
						(49, '11', 'M', 15, 27, 'years', 2),
						(50, '12', 'M', 15, 27, 'years', 2),
						(51, '12', 'W', 15, 27, 'years', 2),
						(52, '13', 'M', 15, 27, 'years', 2),
						(53, '13', 'W', 15, 27, 'years', 2),
						(54, '14', 'W', 15, 27, 'years', 2),
						(55, '14', 'M', 15, 27, 'years', 2),
						(56, '15', 'M', 15, 27, 'years', 2),
						(57, '15', 'W', 15, 27, 'years', 2),
						(58, '16', 'M', 27, 15, 'years', 2),
						(59, '16', 'W', 15, 27, 'years', 2),
						(60, '17', 'M', 15, 27, 'years', 2),
						(61, '17', 'W', 15, 27, 'years', 2),
						(62, '18', 'M', 15, 27, 'years', 2),
						(63, '18', 'W', 15, 27, 'years', 2),
						(64, '5', 'W', 15, 27, 'classes', 2),
						(65, '5', 'M', 15, 27, 'classes', 2),
						(66, '6', 'M', 15, 27, 'classes', 2),
						(67, '6', 'W', 15, 27, 'classes', 2),
						(68, '6', 'M', 15, 27, 'classes', 2),
						(69, '7', 'W', 15, 27, 'classes', 2),
						(70, '7', 'M', 15, 27, 'classes', 2),
						(71, '8', 'W', 15, 27, 'classes', 2),
						(72, '8', 'M', 15, 27, 'classes', 2),
						(73, '9', 'W', 15, 27, 'classes', 2),
						(74, '9', 'M', 15, 27, 'classes', 2),
						(75, '10', 'W', 15, 27, 'classes', 2),
						(76, '10', 'M', 15, 27, 'classes', 2),
						(77, '11', 'W', 15, 27, 'classes', 2),
						(78, '11', 'M', 15, 27, 'classes', 2),
						(79, '12', 'M', 15, 27, 'classes', 2),
						(80, '12', 'W', 15, 27, 'classes', 2),
						(81, '9', 'M', 0, 0, 'years', 3),
						(82, '9', 'W', 14, 17, 'years', 3),
						(83, '10', 'W', 17, 20, 'years', 3),
						(84, '10', 'M', 17, 20, 'years', 3),
						(85, '11', 'W', 20, 23, 'years', 3),
						(86, '11', 'M', 20, 23, 'years', 3),
						(87, '12', 'W', 20, 23, 'years', 3),
						(88, '12', 'M', 20, 23, 'years', 3),
						(89, '13', 'W', 20, 23, 'years', 3),
						(90, '13', 'M', 20, 23, 'years', 3),
						(91, '14', 'W', 23, 26, 'years', 3),
						(92, '14', 'M', 23, 26, 'years', 3),
						(93, '15', 'W', 23, 26, 'years', 3),
						(94, '15', 'M', 23, 26, 'years', 3),
						(95, '16', 'W', 23, 26, 'years', 3),
						(96, '16', 'M', 23, 26, 'years', 3),
						(97, '17', 'W', 26, 29, 'years', 3),
						(98, '17', 'M', 26, 29, 'years', 3),
						(99, '18', 'W', 26, 29, 'years', 3),
						(100, '18', 'M', 26, 29, 'years', 3),
						(101, '19', 'W', 26, 29, 'years', 3),
						(102, '19', 'M', 26, 29, 'years', 3),
						(107, '5', 'W', 20, 23, 'classes', 3),
						(108, '5', 'M', 20, 23, 'classes', 3),
						(109, '6', 'W', 20, 23, 'classes', 3),
						(110, '6', 'M', 20, 23, 'classes', 3),
						(111, '7', 'W', 20, 23, 'classes', 3),
						(112, '7', 'M', 20, 23, 'classes', 3),
						(115, '8', 'W', 23, 26, 'classes', 3),
						(116, '8', 'M', 23, 26, 'classes', 3),
						(117, '9', 'W', 23, 26, 'classes', 3),
						(118, '9', 'M', 23, 26, 'classes', 3),
						(119, '10', 'W', 23, 26, 'classes', 3),
						(120, '10', 'M', 23, 26, 'classes', 3),
						(121, '11', 'W', 26, 29, 'classes', 3),
						(122, '11', 'M', 26, 29, 'classes', 3),
						(123, '12', 'W', 26, 29, 'classes', 3),
						(124, '12', 'M', 26, 29, 'classes', 3);";
		$this->db->query($sql_reset_conditions);
		
		$sql_truncate_conditions_sports = 'TRUNCATE TABLE conditions_sports';
		$this->db->query($sql_truncate_conditions_sports);
		
		$sql_reset_conditions_sports = "
			INSERT INTO `conditions_sports` (`condition_id`, `sport_id`, `sport_group_id`) VALUES
			(0, 0, 0),
			(1, 1, 1),
			(1, 3, 2),
			(1, 5, 4),
			(1, 10, 3),
			(2, 1, 1),
			(2, 3, 2),
			(2, 6, 4),
			(2, 10, 3),
			(3, 1, 1),
			(3, 3, 2),
			(3, 7, 4),
			(3, 10, 3),
			(4, 1, 1),
			(4, 3, 2),
			(4, 7, 4),
			(4, 10, 3),
			(5, 1, 1),
			(5, 3, 2),
			(5, 7, 4),
			(5, 10, 3),
			(6, 1, 1),
			(6, 4, 2),
			(6, 7, 4),
			(6, 10, 3),
			(7, 1, 1),
			(7, 3, 2),
			(7, 7, 4),
			(7, 10, 3),
			(8, 1, 1),
			(8, 4, 2),
			(8, 7, 4),
			(8, 10, 3),
			(9, 2, 1),
			(9, 3, 2),
			(9, 7, 4),
			(9, 10, 3),
			(10, 2, 1),
			(10, 4, 2),
			(10, 7, 4),
			(10, 10, 3),
			(11, 2, 1),
			(11, 4, 2),
			(11, 7, 4),
			(11, 10, 3),
			(12, 2, 1),
			(12, 4, 2),
			(12, 8, 4),
			(12, 10, 3),
			(13, 2, 1),
			(13, 4, 2),
			(13, 7, 4),
			(13, 10, 3),
			(14, 2, 1),
			(14, 4, 2),
			(14, 8, 4),
			(14, 10, 3),
			(15, 1, 1),
			(15, 4, 2),
			(15, 8, 4),
			(15, 10, 3),
			(16, 1, 1),
			(16, 4, 2),
			(16, 8, 4),
			(16, 10, 3),
			(17, 1, 1),
			(17, 4, 2),
			(17, 8, 4),
			(17, 10, 3),
			(18, 1, 1),
			(18, 4, 2),
			(18, 8, 4),
			(18, 10, 3),
			(19, 1, 1),
			(19, 4, 2),
			(19, 8, 4),
			(19, 10, 3),
			(20, 1, 1),
			(20, 4, 2),
			(20, 8, 4),
			(20, 10, 3),
			(21, 1, 1),
			(21, 4, 2),
			(21, 8, 4),
			(21, 10, 3),
			(22, 1, 1),
			(22, 4, 2),
			(22, 8, 4),
			(22, 10, 3),
			(30, 1, 1),
			(30, 3, 2),
			(30, 5, 4),
			(30, 10, 3),
			(31, 1, 1),
			(31, 3, 2),
			(31, 5, 4),
			(31, 10, 3),
			(32, 1, 1),
			(32, 3, 2),
			(32, 5, 4),
			(32, 10, 3),
			(33, 1, 1),
			(33, 4, 2),
			(33, 6, 4),
			(33, 9, 3),
			(34, 2, 1),
			(34, 3, 2),
			(34, 7, 4),
			(34, 10, 3),
			(35, 2, 1),
			(35, 4, 2),
			(35, 7, 4),
			(35, 10, 3),
			(36, 2, 1),
			(36, 4, 2),
			(36, 7, 4),
			(36, 10, 3),
			(37, 2, 1),
			(37, 4, 2),
			(37, 8, 4),
			(37, 10, 3),
			(38, 4, 2),
			(38, 7, 4),
			(38, 10, 3),
			(38, 11, 1),
			(39, 4, 2),
			(39, 8, 4),
			(39, 10, 3),
			(39, 11, 1),
			(40, 4, 2),
			(40, 8, 4),
			(40, 10, 3),
			(40, 11, 1),
			(41, 4, 2),
			(41, 8, 4),
			(41, 10, 3),
			(41, 11, 1),
			(42, 4, 2),
			(42, 8, 4),
			(42, 10, 3),
			(42, 11, 1),
			(43, 4, 2),
			(43, 8, 4),
			(43, 10, 3),
			(43, 11, 1),
			(44, 4, 2),
			(44, 8, 4),
			(44, 10, 3),
			(44, 11, 1),
			(45, 4, 2),
			(45, 8, 4),
			(45, 10, 3),
			(45, 11, 1),
			(46, 12, 24),
			(46, 13, 23),
			(46, 14, 22),
			(46, 15, 25),
			(46, 16, 26),
			(47, 12, 24),
			(47, 13, 23),
			(47, 14, 22),
			(47, 15, 25),
			(47, 16, 26),
			(48, 12, 24),
			(48, 13, 23),
			(48, 14, 22),
			(48, 15, 25),
			(48, 16, 26),
			(49, 12, 24),
			(49, 13, 23),
			(49, 14, 22),
			(49, 15, 25),
			(49, 16, 26),
			(50, 12, 24),
			(50, 13, 23),
			(50, 14, 22),
			(50, 15, 25),
			(50, 16, 26),
			(51, 12, 24),
			(51, 13, 23),
			(51, 14, 22),
			(51, 15, 25),
			(51, 16, 26),
			(52, 12, 24),
			(52, 13, 23),
			(52, 14, 22),
			(52, 15, 25),
			(52, 16, 26),
			(53, 12, 24),
			(53, 13, 23),
			(53, 14, 22),
			(53, 15, 25),
			(53, 16, 26),
			(54, 12, 24),
			(54, 14, 22),
			(54, 16, 26),
			(54, 17, 23),
			(54, 18, 25),
			(55, 12, 24),
			(55, 14, 22),
			(55, 16, 26),
			(55, 17, 23),
			(55, 18, 25),
			(56, 12, 24),
			(56, 14, 22),
			(56, 16, 26),
			(56, 17, 23),
			(56, 18, 25),
			(57, 12, 24),
			(57, 14, 22),
			(57, 16, 26),
			(57, 17, 23),
			(57, 18, 25),
			(58, 12, 24),
			(58, 14, 22),
			(58, 16, 26),
			(58, 17, 23),
			(58, 18, 25),
			(59, 12, 24),
			(59, 14, 22),
			(59, 16, 26),
			(59, 17, 23),
			(59, 18, 25),
			(60, 12, 24),
			(60, 14, 22),
			(60, 16, 26),
			(60, 17, 23),
			(60, 18, 25),
			(61, 12, 24),
			(61, 14, 22),
			(61, 16, 26),
			(61, 17, 23),
			(61, 18, 25),
			(62, 12, 24),
			(62, 14, 22),
			(62, 16, 26),
			(62, 17, 23),
			(62, 18, 25),
			(63, 12, 24),
			(63, 14, 22),
			(63, 16, 26),
			(63, 17, 23),
			(63, 18, 25),
			(64, 12, 24),
			(64, 13, 23),
			(64, 14, 22),
			(64, 15, 25),
			(64, 16, 26),
			(65, 12, 24),
			(65, 13, 23),
			(65, 14, 22),
			(65, 15, 25),
			(65, 16, 26),
			(66, 12, 24),
			(66, 13, 23),
			(66, 14, 22),
			(66, 15, 25),
			(67, 12, 24),
			(67, 13, 23),
			(67, 14, 22),
			(67, 15, 25),
			(67, 16, 26),
			(68, 12, 24),
			(68, 13, 23),
			(68, 14, 22),
			(68, 15, 25),
			(68, 16, 26),
			(69, 12, 24),
			(69, 13, 23),
			(69, 14, 22),
			(69, 15, 25),
			(69, 16, 26),
			(70, 12, 24),
			(70, 13, 23),
			(70, 14, 22),
			(70, 15, 25),
			(70, 16, 26),
			(71, 12, 24),
			(71, 14, 22),
			(71, 16, 26),
			(71, 17, 23),
			(71, 18, 25),
			(72, 12, 24),
			(72, 14, 22),
			(72, 16, 26),
			(72, 17, 23),
			(72, 18, 25),
			(73, 12, 24),
			(73, 14, 22),
			(73, 16, 26),
			(73, 17, 23),
			(73, 18, 25),
			(74, 12, 24),
			(74, 14, 22),
			(74, 16, 26),
			(74, 17, 23),
			(74, 18, 25),
			(75, 12, 24),
			(75, 14, 22),
			(75, 16, 26),
			(75, 17, 23),
			(75, 18, 25),
			(76, 12, 24),
			(76, 14, 22),
			(76, 16, 26),
			(76, 17, 23),
			(76, 18, 25),
			(77, 12, 24),
			(77, 14, 22),
			(77, 16, 26),
			(77, 17, 23),
			(77, 18, 25),
			(78, 12, 24),
			(78, 14, 22),
			(78, 16, 26),
			(78, 17, 23),
			(78, 18, 25),
			(79, 12, 24),
			(79, 14, 22),
			(79, 16, 26),
			(79, 17, 23),
			(79, 18, 25),
			(80, 12, 24),
			(80, 14, 22),
			(80, 16, 26),
			(80, 17, 23),
			(80, 18, 25),
			(81, 19, 5),
			(81, 20, 6),
			(81, 21, 7),
			(81, 22, 8),
			(81, 23, 9),
			(81, 24, 10),
			(82, 19, 5),
			(82, 20, 6),
			(82, 21, 7),
			(82, 22, 8),
			(82, 23, 9),
			(82, 24, 10),
			(83, 19, 5),
			(83, 20, 6),
			(83, 21, 7),
			(83, 22, 8),
			(83, 23, 9),
			(83, 24, 10),
			(84, 19, 5),
			(84, 20, 6),
			(84, 21, 7),
			(84, 22, 8),
			(84, 23, 9),
			(84, 24, 10),
			(85, 25, 5),
			(85, 26, 6),
			(85, 27, 7),
			(85, 28, 8),
			(85, 29, 9),
			(85, 30, 10),
			(86, 25, 5),
			(86, 26, 6),
			(86, 27, 7),
			(86, 28, 8),
			(86, 29, 9),
			(86, 30, 10),
			(87, 25, 5),
			(87, 26, 6),
			(87, 27, 7),
			(87, 28, 8),
			(87, 29, 9),
			(87, 30, 10),
			(88, 25, 5),
			(88, 26, 6),
			(88, 27, 7),
			(88, 28, 8),
			(88, 29, 9),
			(88, 30, 10),
			(89, 25, 5),
			(89, 26, 6),
			(89, 27, 7),
			(89, 28, 8),
			(89, 29, 9),
			(89, 30, 10),
			(90, 25, 5),
			(90, 26, 6),
			(90, 27, 7),
			(90, 28, 8),
			(90, 29, 9),
			(90, 30, 10),
			(91, 29, 9),
			(91, 31, 5),
			(91, 32, 6),
			(91, 33, 7),
			(91, 34, 8),
			(91, 35, 10),
			(92, 29, 9),
			(92, 31, 5),
			(92, 32, 6),
			(92, 33, 7),
			(92, 34, 8),
			(92, 35, 10),
			(93, 29, 9),
			(93, 31, 5),
			(93, 32, 6),
			(93, 33, 7),
			(93, 34, 8),
			(93, 35, 10),
			(94, 29, 9),
			(94, 31, 5),
			(94, 32, 6),
			(94, 33, 7),
			(94, 34, 8),
			(94, 35, 10),
			(95, 29, 9),
			(95, 31, 5),
			(95, 32, 6),
			(95, 33, 7),
			(95, 34, 8),
			(95, 35, 10),
			(96, 29, 9),
			(96, 31, 5),
			(96, 32, 6),
			(96, 33, 7),
			(96, 34, 8),
			(96, 35, 10),
			(97, 36, 5),
			(97, 37, 6),
			(97, 38, 7),
			(97, 39, 8),
			(97, 40, 9),
			(97, 41, 10),
			(98, 36, 5),
			(98, 37, 6),
			(98, 38, 7),
			(98, 39, 8),
			(98, 40, 9),
			(98, 41, 10),
			(99, 36, 5),
			(99, 37, 6),
			(99, 38, 7),
			(99, 39, 8),
			(99, 40, 9),
			(99, 41, 10),
			(100, 36, 5),
			(100, 37, 6),
			(100, 38, 7),
			(100, 39, 8),
			(100, 40, 9),
			(100, 41, 10),
			(101, 36, 5),
			(101, 37, 6),
			(101, 38, 7),
			(101, 39, 8),
			(101, 40, 9),
			(101, 41, 10),
			(102, 36, 5),
			(102, 37, 6),
			(102, 38, 7),
			(102, 39, 8),
			(102, 40, 9),
			(102, 41, 10),
			(107, 25, 5),
			(107, 26, 6),
			(107, 27, 7),
			(107, 28, 8),
			(107, 29, 9),
			(107, 30, 10),
			(108, 25, 5),
			(108, 26, 6),
			(108, 27, 7),
			(108, 28, 8),
			(108, 29, 9),
			(108, 30, 10),
			(109, 25, 5),
			(109, 26, 6),
			(109, 27, 7),
			(109, 28, 8),
			(109, 29, 9),
			(109, 30, 10),
			(110, 25, 5),
			(110, 26, 6),
			(110, 27, 7),
			(110, 28, 8),
			(110, 29, 9),
			(110, 30, 10),
			(111, 25, 5),
			(111, 26, 6),
			(111, 27, 7),
			(111, 28, 8),
			(111, 29, 9),
			(111, 30, 10),
			(112, 25, 5),
			(112, 26, 6),
			(112, 27, 7),
			(112, 28, 8),
			(112, 29, 9),
			(112, 30, 10),
			(115, 29, 9),
			(115, 31, 5),
			(115, 32, 6),
			(115, 33, 7),
			(115, 34, 8),
			(115, 35, 10),
			(116, 29, 9),
			(116, 31, 5),
			(116, 32, 6),
			(116, 33, 7),
			(116, 34, 8),
			(116, 35, 10),
			(117, 29, 9),
			(117, 31, 5),
			(117, 32, 6),
			(117, 33, 7),
			(117, 34, 8),
			(117, 35, 10),
			(118, 29, 9),
			(118, 31, 5),
			(118, 32, 6),
			(118, 33, 7),
			(118, 34, 8),
			(118, 35, 10),
			(119, 29, 9),
			(119, 31, 5),
			(119, 32, 6),
			(119, 33, 7),
			(119, 34, 8),
			(119, 35, 10),
			(120, 29, 9),
			(120, 31, 5),
			(120, 32, 6),
			(120, 33, 7),
			(120, 34, 8),
			(120, 35, 10),
			(121, 36, 5),
			(121, 37, 6),
			(121, 38, 7),
			(121, 39, 8),
			(121, 40, 9),
			(121, 41, 10),
			(122, 36, 5),
			(122, 37, 6),
			(122, 38, 7),
			(122, 39, 8),
			(122, 40, 9),
			(122, 41, 10),
			(123, 36, 5),
			(123, 37, 6),
			(123, 38, 7),
			(123, 39, 8),
			(123, 40, 9),
			(123, 41, 10),
			(124, 36, 5),
			(124, 37, 6),
			(124, 38, 7),
			(124, 39, 8),
			(124, 40, 9),
			(124, 41, 10);";
		$this->db->query($sql_reset_conditions_sports);
	}
	
	function insertConditionSport($condition_id, $sport_group_id, $sport_id) {
		$sql_insert_condition_sport = "INSERT INTO conditions_sports (condition_id, sport_group_id, sport_id) VALUES ($condition_id, $sport_group_id, $sport_id)";
		
		$this->db->query($sql_insert_condition_sport);
		return true;
	}
}
?>