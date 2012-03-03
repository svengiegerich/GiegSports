<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Points_model extends CI_Model {
	
	function getPoints($competitors) {
		$this->load->model('contests_model');
		$this->load->model('sport_groups_model');
		$points_table_used = $this->contests_model->getPointsUsed();
		
		$sport_section = $this->contests_model->getContestSportSection();
		$sport_groups = $this->sport_groups_model->getAllSportGroups($sport_section);
		
		foreach ($competitors as $competitor_id => $competitor_id) {
			$sql_get_points = 'SELECT * FROM ' . $points_table_used . ' WHERE competitor_id = ' . $competitor_id;
		
			$query_get_points = $this->db->query($sql_get_points);
			if ($query_get_points->num_rows() > 0) {
				foreach ($query_get_points->result() as $competitor) {
					
					foreach ($competitor as $column_id => $column) {
						if ($column_id != 'competitor_id' && $column_id != 'class_id') {
							$points[$competitor_id][$column_id] = $column;
						}
					}
					$points[$competitor_id]['total_points'] = 0;
					
					if (count($points[$competitor_id]) > 3 && $sport_section == 3) { 
						$this->load->model('sport_groups_model');
						$this->load->model('sports_model');
						
						foreach ($points[$competitor_id] as $sport_id => $point_sport_group) {
							if ($sport_id != 'total_points') {
								$sport_id = substr($sport_id, 6);
								
								$sport = $this->sports_model->getSport($sport_id);
								$calcus = $this->sport_groups_model->getCalcus($sport['sport_group_id']);
								
								if (!empty($point_sport_group)) {
									if ($calcus == 4) {
										$points[$competitor_id]['total_points'] = $points[$competitor_id]['total_points'] + $point_sport_group;
									} else {
										$completed_sport_groups[] = $point_sport_group;
									}
								}
							}
						}
						if (isset($completed_sport_groups)) {
							if (count($completed_sport_groups) == 4) {
								foreach ($completed_sport_groups as $points_sport_group) {
									$points[$competitor_id]['total_points'] = $points[$competitor_id]['total_points'] + $points_sport_group;
								}
								$points[$competitor_id]['total_points'] = $points[$competitor_id]['total_points'] - min($completed_sport_groups);
							} else {
								$first_lowest = min($completed_sport_groups);
								foreach ($completed_sport_groups as $points_sport_group) {
									if ($points_sport_group != $first_lowest) {
										$find_second_lowest[] = $points_sport_group;
									} 
								}
								$second_lowest = min($find_second_lowest);
								
								foreach ($completed_sport_groups as $points_sport_group) {
									$points[$competitor_id]['total_points'] = $points[$competitor_id]['total_points'] + $points_sport_group;
								}
								$points[$competitor_id]['total_points'] = $points[$competitor_id]['total_points'] - ($first_lowest + $second_lowest);
								
								unset($completed_sport_groups);
							}
						}
						
						
						
						
					} else {
						foreach ($points[$competitor_id] as $point_sport_group) {
							$points[$competitor_id]['total_points'] = $points[$competitor_id]['total_points'] + $point_sport_group;
						}
					}
					
					/*$points_sport_1 = $competitor_points->sport_1;
					$points_sport_2 = $competitor_points->sport_2;
					$points_sport_3 = $competitor_points->sport_3;
					$points_sport_4 = $competitor_points->sport_4;*/
					
					/*$lowest = min($points_sport_2, $points_sport_4, $points_sport_3, $points_sport_1);
				
					$points[$competitor_id]['total_points'] = $points_sport_1 + $points_sport_2 + $points_sport_3 + $points_sport_4 - $lowest;
					$points[$competitor_id]['points_sport_group_1'] = $points_sport_1;
					$points[$competitor_id]['points_sport_group_2'] = $points_sport_2;
					$points[$competitor_id]['points_sport_group_3'] = $points_sport_3;
					$points[$competitor_id]['points_sport_group_4'] = $points_sport_4;*/
				}
			}
		}
		return $points;
	}
	
	function insertPoints($competitor_id , $points, $points_table) {
		//benötigt als $points Syntax: array($sport_group_id => '425')
		$sql_insert_points = 'UPDATE ' . $points_table . ' SET ';
		foreach ($points as $sport_group_id => $points_value) {
			$sql_insert_points .= 'sport_' . $sport_group_id . ' = ' . $points_value . ', ';
		}
		$sql_insert_points = substr($sql_insert_points, 0, -2);
		$sql_insert_points .= ' WHERE competitor_id = ' .  $competitor_id;
		
		$this->db->query($sql_insert_points);
		return TRUE;
	}
	
	function whoGetsWhat($competitors, $class_id) {
		$this->load->model('competitors_model');
		$this->load->model('conditions_model');
		$this->load->model('contests_model');
		$this->load->model('classes_model');
		
		$current_conditions_type = $this->contests_model->getConditionsUsed();
		$points = $this->getPoints($competitors);
		$points_needed = $this->conditions_model->getPointsNeeded();
		
		$grade = substr($this->classes_model->getClassName($class_id), 0, -1);
		
		//points_needed Array BSP.: $points_needed['M'][14]['winner']	   
		
		foreach ($competitors as $competitor_id => $competitor) {
			$year = $this->competitors_model->getAge($competitor_id);
			$gender = $this->competitors_model->getGender($competitor_id);
			$competitor_points = $points[$competitor_id];
			
			if ($current_conditions_type == 'classes') {
				$value = $grade;
			} else {
				$value = $year;
				if ($value > 19) {
					$value = 19;
				}
			}
			
			if ($competitor_points['total_points'] >= $points_needed[$gender][$value]['honor']) {
				$who_gets_what['honorcharter'][$competitor_id] = $competitor_id;
			} elseif($competitor_points['total_points'] >= $points_needed[$gender][$value]['winner']) {
				$who_gets_what['winnercharter'][$competitor_id] = $competitor_id;
			} else {
				$who_gets_what['participantcharter'][$competitor_id] = $competitor_id;
			}
		}
		
		return $who_gets_what;
	}
	
	
	
	
	
	function deleteCompetitor($competitor_id) {
		$this->load->model('contests_model');
		$points_table_used = $this->contests_model->getPointsUsed();
		
		$sql_delete_competitor = 'DELETE FROM ' . $points_table_used . ' WHERE competitor_id = ' . $competitor_id ;
		$this->db->query($sql_delete_competitor);
	}
	function changeCompetitorClass($competitor_id, $class_id) {
		$this->load->model('contests_model');
		$points_table_used = $this->contests_model->getPointsUsed();
		
		$sql_change_competitor_class = 'UPDATE  ' . $points_table_used . ' SET class_id = ' . $class_id . ' WHERE competitor_id = ' . $competitor_id;
		$this->db->query($sql_change_competitor_class);
	}
	
	
	
	
	
	
	function getMaxPoints($sport_section) { 
		$this->load->model('contests_model');
		$this->load->model('sport_groups_model');
		$this->load->model('contests_sport_groups_model');
		$sport_groups = $this->contests_sport_groups_model->getSportGroupsUsed();
		
		$points_table_used = $this->contests_model->getPointsUsed();
		
		foreach ($sport_groups as $sport_group_id => $sport_group) {
			
			$sport_group_field = 'sport_' . $sport_group_id;
			
			$sql_get_average_points = 'SELECT AVG(' . $sport_group_field . ') AS average_points FROM ' . $points_table_used;
			$query_get_average_points = $this->db->query($sql_get_average_points);
			if ($query_get_average_points->num_rows() > 0) { 
				foreach ($query_get_average_points->result() as $average) {
					$max_points[$sport_group_id]['average_points'] = $average->average_points;
				}
			}
			
			$sql_get_max_points = 'SELECT * FROM ' . $points_table_used . ' WHERE ' . $sport_group_field . ' = (SELECT MAX(' . $sport_group_field . ') FROM ' . $points_table_used . ')';
			
			$query_get_max_points = $this->db->query($sql_get_max_points);
			if ($query_get_max_points->num_rows() > 0) { 
				foreach ($query_get_max_points->result() as $competitor) {
					$max_points[$sport_group_id]['competitor_id'] = $competitor->competitor_id; 
					$max_points[$sport_group_id]['points'] = $competitor->$sport_group_field;
					$max_points[$sport_group_id]['calcus'] = $sport_group['calcus'];
				}
			}
		}
		return $max_points;
	}
	
	function getAverageClassPoints($class, $point_table) {
		$sql_get_all_points = 'SELECT * FROM ' . $point_table . ' WHERE class_id = ' . $class . '';
		$query_get_all_points = $this->db->query($sql_get_all_points);
		if ($query_get_all_points->num_rows() > 0) {
			foreach ($query_get_all_points->result() as $competitor) {
				foreach ($competitor as $attribute_id => $attribute) {
					if (!($attribute_id == 'competitor_id') && !($attribute_id == 'class_id') && $attribute != 0) {
						$all_points[$competitor->competitor_id][$attribute_id] = $attribute;
					}
				}
			}
			if (isset($all_points)) {
				$total_points = 0;
			
				foreach ($all_points as $row) {
					foreach ($row as $points) {
						$total_points = $total_points + $points;
					}
				}
				$average_points = $total_points / count($all_points);
				
				} else {
					$average_points = 0;
				}
			return $average_points;
		} 
	}
	
	function enoughResults($points_table) {
		$sports_columns = $this->getSportsColumns($points_table);
		
		$sql_enough_results = 'SELECT * FROM ' . $points_table . ' WHERE ';
		foreach ($sports_columns as $sport_colum_term => $sport_column) {
			$sql_enough_results .= $sport_colum_term . ' > 0 OR ';
		}
		$sql_enough_results = substr($sql_enough_results, 0, -4);
		
		$query_enough_results = $this->db->query($sql_enough_results);
		if ($query_enough_results->num_rows > 0) {
			return $query_enough_results->num_rows;
		}
		return false;
	}
	
	function getSportsColumns($points_table) {
		$sql_get_sports_columns = 'SELECT * FROM ' . $points_table . ' LIMIT 1;';
		$query_get_sports_columns = $this->db->query($sql_get_sports_columns);
		
		if ($query_get_sports_columns->num_rows() > 0) {
			foreach ($query_get_sports_columns->result() as $row) {
				
				foreach ($row as $column_term => $column) {
					if (!($column_term == 'competitor_id') && !($column_term == 'class_id')) {
						$sports_columns[$column_term] = $column;
					}
				}
			}
			return $sports_columns;
		}
	}
	
	function getResultFromPoints($points, $a, $c, $calcus, $d = 0) {
		switch ($calcus) {
			case 1:
				if (!empty($d) && !empty($a) && !empty($c) && !empty($d)) {
					$result = $d/ ( ($points * $c + $a) * 60 );
				} else {
					return false;
				}
				break;
				
			case 2:
				if (!empty($d) && !empty($a) && !empty($c) && !empty($d)) {
					$this->load->model('settings_model');
					$timekeeping = $this->settings_model->getTimekeeping();
					if ($timekeeping['setting_value'] == '1' && $calcus == 2) {
						$result = ( $d / ($points * $c + $a) ) - 0.24;
					} else {
						$result = $d/ ($points * $c + $a);
					}
				} else {
					return false;
				}
				break;
			
			case 3:
				if ($d == 0) {
					$result = pow($points * $c + $a, 2);
				} else {
					return false;
				}
				break;
		}
		return $result;
	}
	
	function getPointsFromResult($result, $a = 0, $c = 0, $calcus, $d = 0) {
		switch ($calcus) {
			case 1:
				if (!empty($d) && !empty($a) && !empty($c) && !empty($d)) {
					$points = (( $d / ( $result * 60)) - $a ) / $c;
				} else {
					return false;
				}
				break;
				
			case 2:
				if (!empty($d) && !empty($a) && !empty($c)) {
					$this->load->model('settings_model');
					$timekeeping = $this->settings_model->getTimekeeping();
					if ($timekeeping['setting_value'] == '1' && $calcus == 2) {
						$points = (( $d / ( $result + 0.24 )) - $a ) / $c;
					} else {
						$points = (( $d / ( $result )) - $a ) / $c;
					}
				} else {
					return false;
				}
				break;
			
			case 3:
				if (!empty($a) && !empty($c)) {
					$points = ( sqrt($result) - $a ) / $c;
				} else {
					return false;
				}
				break;
			
			default:
				return $result;
		} 
		//keine minus Zahlen!
		if ($points < 0) {
			$points = 0;
		}
		
		return $points;
	}
}
?>