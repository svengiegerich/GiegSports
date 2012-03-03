<?php
class Add_competitors_model extends CI_Model {
	
	function insertCompetitors($competitors, $from_grade, $to_grade) {
		
		$this->load->model('classes_model');
		$this->load->model('years_model');
		$this->load->model('competitors_model');
		$this->load->model('contests_model');
		$this->load->model('sport_groups_model');
		
		$sport_section = $this->contests_model->getContestSportSection();
		$sport_groups = $this->sport_groups_model->getAllSportGroups($sport_section);
		// for ???
		//$count_sport_groups = count($sport_groups);
		
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
								) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';
		
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
		
		print_r($competitors);
		
		foreach ($competitors as $competitor) {
			
			$class_name = $competitor['class'];
			$grade = substr($class_name, 0 ,-1);
			$gender = $competitor['gender'];
			$name = $competitor['firstname'] . ' ' . $competitor['lastname'];
			$fulldate = $competitor['date_of_birth'];
			
			
			if ($grade >= $from_grade AND $grade <= $to_grade) {
				$class_id = $this->classes->getClassId($class_name);
			}
			
			//Manipuliet das Datum, wenn nötig, um nur das Jahr herauszubekommen
			$fulldate = $competitor[2];
			if (strlen($fulldate) > 4) {
				$year_name = substr(strrchr($fulldate, '.'), 1);
			} else {
				$year_name = $fulldate;
			}
			$year_id = $this->years_model->getYearId($year_name);
			
			if (strlen($gender) == 1) {
			
			//Gibt es den Competitor schon in der Competitors
			$sql_exist_competitor = 'SELECT * FROM competitors WHERE competitor_name = ' . "'$name'" . ' AND year_id = ' . $year_id . ' AND competitor_gender = ' . "'$gender'";
			$query_exist_competitor = $this->db->query($sql_exist_competitor);
			
			if ($query_exist_competitor->num_row() == 0) {
				$sql_add_competitor = "INSERT INTO competitors (competitor_name, year_id, competitor_gender) VALUES ('$name', '$year_id', '$gender')";	
				$query_add_competitor = $this->db->query($sql_add_competitor);
				
				$competitor_id = $this->competitors_model->getCompetitorId($name, $year_id, $gender);
				
				$sql_insert_competitor_into_points = 'INSERT INTO ' . $points_table_used . ' (competitor_id, class_id) VALUES (' . $competitor_id. ', ' . $class_id . ')';
				$query_insert_competitor_into_points = $this->db->query($sql_insert_competitor_into_points);
				
				$competitor_added++;
				$points_table_added++;
			} else {
					$competitor_id = $this->competitors_model->getCompetitorId($name, $year_id, $gender);
					
					//gibt es den Teilnehmer schon in der Competitors, aber nicht in der aktuellen Points
					$competitor_id = $this->competitors_model->getCompetitorId($name, $year_id, $gender);
					$sql_exist_competitor_in_points = 'SELECT * FROM ' . $points_table_used . ' WHERE competitor_id = ' . $competitor_id;

					$query_exist_competitor_in_points = $this->db->query($sql_exist_competitor_in_points);
					if ($query_exist_competitor_in_points->num_row() == 0) {
						
						$sql_insert_competitor_into_points = 'INSERT INTO ' . $points_table_used . 	' (competitor_id, class_id) VALUES (' . $competitor_id. ', ' . $class_id . ')';
						$query_insert_competitor_into_points = $this->db->query($sql_insert_competitor_into_points);
						
						$points_table_added++;
					} else {
						//schaut die Klasse noch stimmt
						$sql_look_for_class = 'SELECT * FROM ' . $points_table_used . ' WHERE competitor_id = ' . $competitor_id . ' AND class_id = ' . $class_id;
						$query_look_for_class = $this->db->query($sql_look_for_class);
						if ($query_look_for_class->num_row() != 0) {
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
		$data['competitor_exitsts'] = $competitor_exitsts;
		$data['competitor_added'] = $competitor_added;
		$data['points_table_added'] = $points_table_added;
		return $data;
	}
}
?>