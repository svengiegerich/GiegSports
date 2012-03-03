<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Competitors_model extends CI_Model {

	function getCompetitorId($name, $year, $gender) {
		$sql_get_competitor_id = 'SELECT competitor_id FROM competitors WHERE competitor_name = ' . "'$name'" . ' AND year = ' . $year . ' AND competitor_gender = ' . "'$gender'";

		$query_get_competitor_id = $this->db->query($sql_get_competitor_id);
		if ($query_get_competitor_id->num_rows() >0) {
			foreach ($query_get_competitor_id->result() as $competitor_id) {
				$competitor_id = $competitor_id->competitor_id;
			}
			return $competitor_id;
		}
	}
	
	function getCompetitorsNames($competitors) {
		foreach ($competitors as $competitor_id => $competitor) {
			$sql_get_competitor_name = 'SELECT competitor_name FROM competitors WHERE competitor_id = ' . $competitor_id;
			
			$query_get_competitor_name = $this->db->query($sql_get_competitor_name);
			if ($query_get_competitor_name->num_rows() > 0) {
				foreach ($query_get_competitor_name->result() as $competitor_name) {
					$competitor_names[$competitor_id] = $competitor_name->competitor_name;
				}
			}
		}
		return $competitor_names;
	} 
	
	//MUSS VERBESSERT WERDEN!!!!
	function getCompetitorNameTrans($competitor_id) {
			$sql_get_competitor_name = 'SELECT competitor_name FROM competitors WHERE competitor_id = ' . $competitor_id;
			
			$query_get_competitor_name = $this->db->query($sql_get_competitor_name);
			if ($query_get_competitor_name->num_rows() > 0) {
				foreach ($query_get_competitor_name->result() as $competitor) {
					$competitor_name = $competitor->competitor_name;
				}
			return $competitor_name;
			}
	} 
	
	function getGender($competitor_id) {
		$sql_get_gender = 'SELECT competitor_gender FROM competitors WHERE competitor_id = ' . $competitor_id;
		$query_get_gender = $this->db->query($sql_get_gender);
		if ($query_get_gender->num_rows() > 0) {
			foreach ($query_get_gender->result() as $gender) {
				$competitor_gender = $gender->competitor_gender;
			}
			return $competitor_gender;
		}
	}
	
	
	function deleteCompetitor($competitor_id) {
		$sql_delete_competitor = 'DELETE FROM competitors WHERE competitor_id = ' . $competitor_id ;
		$this->db->query($sql_delete_competitor);
	}
		
	function getCompetitors($class_id) {
		$this->load->model('contests_model');
		$points_table = $this->contests_model->getPointsUsed();
		
		//SQL-Befehle für:
		$sql_competitors_id = 'SELECT competitor_id FROM ' . $points_table . ' WHERE class_id = ' . $class_id;
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
			return $competitors;
		} 
	}
	
	function getCompetitor($competitor_id, $points_table) {
		$sql_get_competitor = 'SELECT * FROM competitors WHERE competitor_id = ' . $competitor_id;
		$query_get_competitor = $this->db->query($sql_get_competitor);
		if ($query_get_competitor->num_rows() == 1) {
			foreach ($query_get_competitor->result() as $competitor_entry) {
				
				foreach ($competitor_entry as $column_id => $column) {
					$competitor[$column_id] = $column;
				}
				
				$sql_get_points_entry = 'SELECT * FROM ' . $points_table . ' WHERE competitor_id = ' . $competitor_id;
				$query_get_points_entry = $this->db->query($sql_get_points_entry);
				if ($query_get_points_entry->num_rows == 1) {
					foreach ($query_get_points_entry->result() as $point_entry) {
						foreach ($point_entry as $column_id => $column_value) {
							$competitor[$column_id] = $column_value;
						}
					}
				}
			}
			return $competitor;
		}
	}
	
	function getAge($competitor_id) {
		date_default_timezone_set('CET');
		
		$sql_get_year = 'SELECT year FROM competitors WHERE competitor_id = ' . $competitor_id;
		
		$query_get_year = $this->db->query($sql_get_year);
		if ($query_get_year->num_rows() > 0) {
			foreach ($query_get_year->result() as $year) {
				
				$year = date('Y') - $year->year;
			}
			return $year;
		}
	}
	
	function insertCompetitors($competitors, $from_grade, $to_grade) {
		$this->load->model('classes_model');
		$this->load->model('years_model');
		$this->load->model('competitors_model');
		$this->load->model('contests_model');
		$this->load->model('sport_groups_model');
		
		$sport_section = $this->contests_model->getContestSportSection();
		$sport_groups = $this->sport_groups_model->getAllSportGroups($sport_section);
		
		$points_table_used = $this->contests_model->getPointsUsed();
		
		//Erstellt die Points-Tabele, wenn es wie noch nicht gibt
		
		/*$sql_creat_points_table = 'CREATE TABLE IF NOT EXISTS ' . $points_table_used . ' (
			  								competitor_id int(11) NOT NULL AUTO_INCREMENT,
			 								 sport_1 int(11) DEFAULT NULL,
			 								 sport_2 int(11) DEFAULT NULL,
										     sport_3 int(11) DEFAULT NULL,
									     	 sport_4 int(11) DEFAULT NULL,
			  								class_id int(11) NOT NULL,
			 								PRIMARY KEY (competitor_id)
									) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';*/
		
		
		$sql_create_points_table = 'CREATE TABLE IF NOT EXISTS ' . $points_table_used . ' (
			  	
			  								competitor_id int(11) NOT NULL AUTO_INCREMENT,';
			 $i = 1;
			foreach ($sport_groups as $sport_group_id => $sport_group) {
				$sql_create_points_table .= ' sport_' . $sport_group_id . ' int(11) DEFAULT NULL,';
				$i++;
			} 
			
			
			
		$sql_create_points_table .= ' class_id int(11) NOT NULL,
									PRIMARY KEY (competitor_id)
								) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;';
		
		//echo $sql_create_points_table;
									
		/*$sql_creat_points_table = 'CREATE TABLE IF NOT EXISTS ' . $points_table_used . ' (
			  								competitor_id int(11) NOT NULL AUTO_INCREMENT,
			 								 sport_1 int(11) DEFAULT NULL,
			 								 sport_2 int(11) DEFAULT NULL,
										     sport_3 int(11) DEFAULT NULL,
									     	 sport_4 int(11) DEFAULT NULL,
			  								class_id int(11) NOT NULL,
			 								PRIMARY KEY (competitor_id)
									) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';*/
									
		$query_create_points_table = $this->db->query($sql_create_points_table);

		$competitor_added = 0;
		$competitor_exitsts = 0;
		$points_table_added = 0;
		$competitor_class_changed = 0;
		
		
		foreach ($competitors as $competitor) {
			
			$class_name = $competitor['class'];
			//Nötig da es z.B. keine 11a gibt
			if (!is_numeric(substr($class_name, -1))) {
				$grade = substr($class_name, 0 ,-1);
			} else {
				$grade = $class_name;
			}
			$gender = $competitor['gender'];
			$name = $competitor['lastname'] . ' ' . $competitor['firstname'];
			$fulldate = $competitor['date_of_birth'];
			
			if ($grade >= $from_grade AND $grade <= $to_grade) {
				$class_id = $this->classes_model->getClassId($class_name);
			}
			
			
			//Manipuliet das Datum, wenn nötig, um nur das Jahr herauszubekommen
			if (strlen($fulldate) > 4) {
				//$year_name = substr(strrchr($fulldate, '.'), 1);
				$year = substr(strrchr($fulldate, '.'), 1);
			} else {
				//$year_name = $fulldate;
				$year = $fulldate;
			}
			
			if (isset($class_id) AND strlen($gender) == 1 AND !empty($class_name) AND !empty($grade) AND !empty($name) ) {
				
				//Gibt es den Competitor schon in der Competitors
				$sql_exist_competitor = 'SELECT * FROM competitors WHERE competitor_name = ' . "'$name'" . ' AND year = ' . $year . ' AND competitor_gender = ' . "'$gender'";
				$query_exist_competitor = $this->db->query($sql_exist_competitor);
				
				if ($query_exist_competitor->num_rows() == 0) {
					$sql_add_competitor = "INSERT INTO competitors (competitor_name, year, competitor_gender) VALUES ('$name', '$year', '$gender')";	
					$query_add_competitor = $this->db->query($sql_add_competitor);
					
					$competitor_id = $this->getCompetitorId($name, $year, $gender);
					
					$sql_insert_competitor_into_points = 'INSERT INTO ' . $points_table_used . ' (competitor_id, class_id) VALUES (' . $competitor_id. ', ' . $class_id . ')';
					$query_insert_competitor_into_points = $this->db->query($sql_insert_competitor_into_points);
					
					$competitor_added++;
					$points_table_added++;
				} else {
					$competitor_id = $this->getCompetitorId($name, $year, $gender);
						
					//gibt es den Teilnehmer schon in der Competitors, aber nicht in der aktuellen Points
					$competitor_id = $this->getCompetitorId($name, $year, $gender);
					$sql_exist_competitor_in_points = 'SELECT * FROM ' . $points_table_used . ' WHERE competitor_id = ' . $competitor_id;
	
					$query_exist_competitor_in_points = $this->db->query($sql_exist_competitor_in_points);
					if ($query_exist_competitor_in_points->num_rows() == 0) {
						$sql_insert_competitor_into_points = 'INSERT INTO ' . $points_table_used . 	' (competitor_id, class_id) VALUES (' . $competitor_id. ', ' . $class_id . ')';
							
						$query_insert_competitor_into_points = $this->db->query($sql_insert_competitor_into_points);
							
						$points_table_added++;
					} else {
						//schaut die Klasse noch stimmt
						$sql_look_for_class = 'SELECT * FROM ' . $points_table_used . ' WHERE competitor_id = ' . $competitor_id . ' AND class_id = ' . $class_id;
						
						$query_look_for_class = $this->db->query($sql_look_for_class);
						if ($query_look_for_class->num_rows() != 0) {
							$competitor_exitsts++;
						} else {
							//die Klasse wurde geändert
							$sql_change_class = 'UPDATE ' . $points_table_used . ' SET class_id = ' . $class_id . ' WHERE competitor_id = ' . $competitor_id;
							$this->db->query($sql_change_class);
							$competitor_class_changed++;
						}
					}
				}
			}
		}
		$data['competitors_exitsts'] = $competitor_exitsts;
		$data['competitors_added'] = $competitor_added;
		$data['points_table_added'] = $points_table_added;
		$data['competitors_class_changed'] = $competitor_class_changed;
		
		return $data;
	}



	// wird noch bearbeitet
	function analyzeData($row, $divider) {
		$splites = explode($divider, $row);
		foreach ($splites as $splite) {
			if (is_numeric($splite)) {
			
			} else {
			
			}
		}
	}
}
?>