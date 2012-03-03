<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Add_results extends CI_Controller {

	function index() {
		$this->load->model('classes_model');
		$this->load->model('contests_model');
		$this->load->model('contests_sport_groups_model');
		$this->load->helper('url');
		
		$arg_list = func_get_args();
		$points_table_used = $this->contests_model->getPointsUsed();
		
		//Gibt es diese Tabelle nicht gibt -> noch keine Competitors hinzugefügt wurden, dann leite ihn dort hin
		if(!$this->contests_model->table_exists($points_table_used) or $this->contests_model->tableEmpty($points_table_used)) {
			redirect("/competitors?redirected_from='site.php'");
		}
		if (count($this->contests_sport_groups_model->getSportGroupsUsed()) < 2) {
			redirect('/preferences');
		}	
		
		$data['classes'] = $this->classes_model->getClasses();
		
		$data['select_class'] = array( 'call_function' => 'add_results/load_competitors', 'submit_function' => 'add_results/submit', 'string' => 'Bitte geben sie hier ihre Ergebnisse ein', 'upload_sheet' => 'add_results/uploadResultsSheet');
		
		if (isset($arg_list[0]['rows_added']) && $arg_list[0]['rows_added'] > 0) { $data['rows_added'] = $arg_list[0]['rows_added']; }
		if (isset($arg_list[0]['uncomplete']) && $arg_list[0]['uncomplete']) { $data['error']['uncomplete'] = true; } 
		
		if ($this->input->get('redirected_from')) { $data['warning']['redirected_from'] = $this->input->get('redirect_from'); }
		
		$data['main_content'] = 'select_class_view.php';
		$this->load->view('includes/template.php', $data);
	}
	
	function load_competitors() {
		$this->load->model('classes_model');
		$this->load->model('sport_groups_model');
		$this->load->model('competitors_model');
		$this->load->model('contests_model');
		$this->load->model('contests_sport_groups_model');
		
		$sport_section = $this->contests_model->getContestSportSection();
		
		if ( $this->input->post('class_id') ) {
			$class_id = $_POST["class_id"];
		} else {
			$class_id = '1';
		}
		
		//notwendig für die selection-fields
		$data['selected_class_id'] = $class_id;
		
		$data['classes'] = $this->classes_model->getClasses();
		$data['select_class'] = array( 'call_function' => 'add_results/load_competitors', 'submit_function' => 'add_results/submit', 'string' => 'Bitte geben sie hier ihre Ergebnisse ein', 'upload_sheet' => 'add_results/uploadResultsSheet');
		
		$data['competitors_info'] = $this->competitors_model->getCompetitors($class_id);
		
		$data['sport_groups'] = $this->contests_sport_groups_model->getSportGroupsUsed();
		
		
		$this->load->view('select_class_view.php', $data);
		$this->load->view('add_results_view.php', $data);
	}
	
	function submit() {
		$this->load->model('competitors_model');
		$this->load->model('sport_groups_model');
		$this->load->model('ac_model');
		$this->load->model('conditions_model');
		$this->load->model('sports_model');
		$this->load->model('classes_model');
		$this->load->model('sport_sections_model');	
		$this->load->model('contests_model');
		$this->load->model('contests_sport_groups_model');
		$this->load->model('points_model');
		
		$class_id = $this->input->post('class-select');
		
		$sport_section = $this->contests_model->getContestSportSection();
		
		$current_conditions_type = $this->contests_model->getConditionsUsed();
		$grade = substr($this->classes_model->getClassName($class_id), 0, -1);
		$data['rows_added'] = 0;
		$competitors_info = $this->competitors_model->getCompetitors($class_id);
		
		$sport_section = $this->contests_model->getContestSportSection();
		$conditions = $this->conditions_model->getAllConditions($sport_section, $current_conditions_type);
		
		$ac = $this->ac_model->getAllAC();
		
		$sport_groups = $this->contests_sport_groups_model->getSportGroupsUsed();
		
		
		$count_sport_groups = count($sport_groups);
		$sports = $this->sports_model->getSports($sport_section);
		$points_table_used = $this->contests_model->getPointsUsed();
		
		//x: Für den $sql Befehl, da es passieren könnte, dass $i und die tatsächliche Anzahl im Array nicht identisch sein könnten
		$x = 0;
			
			foreach ($competitors_info as $competitor_id => $competitor) {
					
					$competitor_gender = $this->competitors_model->getGender($competitor_id);
					$age = $this->competitors_model->getAge($competitor_id);
					
					//Zählt wie viele Input-Felder pro Teilnehmer gesetzt sind
					$y = 0;
										
					foreach ($sport_groups as $sport_group_id => $sport_group) {
						if (is_numeric($this->input->post($competitor_id . '_' . $sport_group_id))) {
							$competitor_results[$competitor_id][$sport_group_id] = $this->input->post($competitor_id . '_' . $sport_group_id);
							
							if ($current_conditions_type == 'classes') {
								$value = $grade;
							} else if ($current_conditions_type == 'years') {
								$value = $age;
								if ($value > 19) {
									$value = 19;
								}
							}
							
							//if (isset($conditions[$competitor_gender][$value]['sport_group_' . $sport_group_id])) {
								$sport_id = $conditions[$competitor_gender][$value]['sport_group_' . $sport_group_id];
								$calcus = $this->sport_groups_model->getCalcus($sport_group_id);
								
								$result = $competitor_results[$competitor_id][$sport_group_id];
								
								if ($calcus == 1 || $calcus == 2 || $calcus == 3) {
									
									$a = $ac['a'][$competitor_gender][$sport_id]['ac_value'];
									$c = $ac['c'][$competitor_gender][$sport_id]['ac_value'];
									
									if ($calcus == 1 || $calcus == 2) { 
										$d = $sports[$sport_id]['sport_name'];
									} else {
										$d = 0;
									}
									$points[$competitor_id][$sport_group_id] = $this->points_model->getPointsFromResult($result, $a, $c, $calcus, $d);
									
								} else {
									$points[$competitor_id][$sport_group_id] = $this->points_model->getPointsFromResult($result, 0, 0, $calcus);
								}
								$y++;
							}
						//}
					}
					
					if ($y >= 3 OR $y == $count_sport_groups) {
						$insert_points = $this->points_model->insertPoints($competitor_id , $points[$competitor_id], $points_table_used);
						if ($insert_points) {
							$data['rows_added']++;
						}
					} else {
						//$error[$competitor_id] = 'Not enough per row';
					}
					
					$x++;
				}

		if ($data['rows_added'] > 0 AND !isset($error)) {
			$this->index($data);
		}	else {
			$error['uncomplete'] = true;
			$this->index($error);
		}
	}
	
	
	
	//Upload Funktionen
	function uploadResultsSheet() {
		$this->load->model('contests_sport_groups_model');
		
		$data['sport_groups'] = $this->contests_sport_groups_model->getSportGroupsUsed();
		
		
		//select_options
		$data['select_options']['competitor_name'] = 'Name';
		$data['select_options']['competitor_class'] = 'Klasse';
		foreach ($data['sport_groups'] as $sport_group_id => $sport_group) {
			$data['select_options'][$sport_group_id] = $sport_group['name'];
		}	
	

		$data['main_content'] = 'upload_results_sheet_view.php';
		$this->load->view('includes/template.php', $data);
	}
	
	function do_upload() {
		$config['upload_path'] = './uploads/results_sheets';
		$config['allowed_types'] = 'csv';
		$config['max_size']	= '1000';
		$config['max_width']  = '4000';
		$config['max_height']  = '3000';
		
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload('result_sheet')) {
			$data['error'] = array('error' => $this->upload->display_errors());
			print_r($data['error']);
					
			$data['main_content'] = 'upload_results_sheet_view.php';
			$this->load->view('includes/template.php', $data);
		} else {
			$this->load->model('contests_sport_groups_model');
			$this->load->model('ac_model');
			$this->load->model('conditions_model');
			$this->load->model('points_model');
			$this->load->model('competitors_model');
			$this->load->model('sport_groups_model');
			$this->load->model('contests_model');
			
			$points_table_used = $this->contests_model->getPointsUsed();
			
			$sport_section = $this->contests_model->getContestSportSection();
			$condition_type = $this->contests_model->getConditionsUsed();
			$conditions = $this->conditions_model->getAllConditions($sport_section, $condition_type);
			
			$ac = $this->ac_model->getAllAC();
			$current_conditions_type = $this->contests_model->getConditionsUsed();
			$sport_groups = $this->contests_sport_groups_model->getSportGroupsUsed();
			
			//select_options
			$select_options['competitor_name'] = 'Name';
			$select_options['competitor_class'] = 'Klasse';
			$select_options['competitor_year'] = 'Geburtsjahr';
			$select_options['competitor_gender'] = 'Geschlecht';
			foreach ($sport_groups as $sport_group_id => $sport_group) {
				$select_options['sport_group_' . $sport_group_id] = $sport_group['name'];
			}
			//Die vorgelegte Reihenfolge wird gesetzt
			for ($i = 0; $i != count($select_options); $i++) {
				$order[$i] = $this->input->post($i);
			}
			
			
			
			$data['upload_data'] = $this->upload->data();
			$file_name = $data['upload_data']['file_name'];
			$file = base_url("/uploads/results_sheets/$file_name");
			
			$row = 1;  
			$rows_added = 0;    
			$divider = ',';
			$handle = fopen ("$file","r");
			
			//wird kurz durchlaufen um die Anzahl der Zeilen herauszufinden für die progress bar
			while ( ($data = fgetcsv ($handle, 1000, "$divider")) !== FALSE ) {	
				$row++;
			}
			$row = $row -1;
			
			while ( ($data = fgetcsv ($handle, 1000, "$divider")) !== FALSE ) {	
				if (count($data) >= count($select_options)) {
					for ($i = 0; $i != count($select_options); $i++) {
						// zerstückelt die teile jeder Zeile in der festgelegten Reihenfolge
						$column_id = $order[$i];
						$competitor[$column_id] = $data[$i];
					}
					$competitor_name = $competitor['competitor_name'];
					$competitor_year = $competitor['competitor_year'];
					$competitor_gender = $competitor['competitor_gender'];
					$competitor_id = $this->competitors_model->getCompetitorId($competitor_name, $competitor_year, $competitor_gender);
					
					if ($current_conditions_type == 'classes') {
						if (!is_numeric($competitor['competitor_class'])) {
							$value = substr($competitor['competitor_class'], 0 , -1);
						} else {
							$value = $competitor['competitor_class'];
						}
					} else if ($current_conditions_type == 'years') {
						$value = $competitor_year;
						if ($value > 19) {
							$value = 19;
						}
					}
					foreach ($sport_groups as $sport_group_id => $sport_group) {
						$result = $competitor['sport_group_' . $sport_group_id];
						$sport_id = $conditions[$competitor_gender][$value]['sport_group_' . $sport_group_id];
						$calcus = $this->sport_groups_model->getCalcus($sport_group_id);
						$a = $ac['a'][$competitor_gender][$sport_id]['ac_value'];
						$c = $ac['c'][$competitor_gender][$sport_id]['ac_value'];
						
						//wenn eine Disziplin bei der die Distanz (d) gebraucht wird
						//geht davon aus, dass dabei am Schluss das 'm' entfernt werden muss
						if ($calcus == 1) {
							$d = substr($sport_group['name'], -1);
							$points[$sport_group_id] = $this->points_model->getPointsFromResult($result, $a, $c, $calcus, $d);
						} else if ($calcus == 2) {
							$d = substr($sport_group['name'], -1);
							//$result = $result * 60;
							$points[$sport_group_id] = $this->points_model->getPointsFromResult($result, $a, $c, $calcus, $d);
						} else {
							$points[$sport_group_id] = $this->points_model->getPointsFromResult($result, $a, $c, $calcus);
						}
						
					}
					$this->points_model->insertPoints($competitor_id, $points, $points_table_used);
					$rows_added++;
				}
				
			}
			fclose ($handle);
			
			$this->index($rows_added);
		}
	}
	
	function __construct() {
			parent::__construct();
			$this->load->helper(array('form', 'url'));
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function appraiseResultsSheet() {
		
		$this->load->model('competitors_model');
		
		$class_id = 1;
		$competitors = $this->competitors_model->getCompetitors($class_id);
		
		// Datei auslesen
		$filename = './uploads/results_sheets/test.txt';
		$handle = fopen ($filename,"r");
		$competitors_results = fread($handle ,filesize($filename));
		fclose ($handle);
		
		$competitor_results_rows = explode(';', $competitors_results);
		
		//competitor_id  // sport_1 // sport_2 // sport_3 // sport_4
		foreach ($competitor_results_rows as $competitor_row) {
			$competitor_splits = explode('|', $competitor_row);
			//print_r($competitors);
			$competitor_id = $competitor_splits[0];
			$competitor_result_sport_1 = $competitor_splits[1];
			$competitor_result_sport_2 = $competitor_splits[2];
			$competitor_result_sport_3 = $competitor_splits[3];
				if (isset($competitor_splits[4])) {
					$competitor_result_sport_4 = $competitor_splits[4];
				}
			
			
			//Wenn competitors_array "saniert" ist über isset(array($competitors)) …
			
			if ($this->competitors_model->getCompetitorNameTrans($competitor_id)) {
				echo 'Diesen Teilnehmer gibt es wirklich. Braves OCR!';
				
				$competitor_name = $this->competitors_model->getCompetitorNameTrans($competitor_id);
				
				$competitor_results[$competitor_id]['name'] = $competitor_name;
				$competitor_results[$competitor_id]['sport_1'] = $competitor_result_sport_1;
				$competitor_results[$competitor_id]['sport_2'] = $competitor_result_sport_2;
				$competitor_results[$competitor_id]['sport_3'] = $competitor_result_sport_3;
					if (isset($competitor_result_sport_4)) {
						$competitor_results[$competitor_id]['sport_4'] = $competitor_result_sport_4;
					}
				
			} else {
				$competitor_results['error'] = 'Diese Teilnehmer gibt es nicht.';
			}
		}
		$this->checkCompetitorsResults($competitor_results, $class_id);
	}
	
	function checkCompetitorsResults($competitor_results, $class_id) {
		//$this->load->model('add_results_model');
		$this->load->model('classes_model');
		$this->load->model('sport_groups_model');
		$this->load->model('competitors_model');
		$this->load->model('contests_model');
		$this->load->model('contests_sport_groups_model');
		
		$sport_section = $this->contests_model->getContestSportSection();
		
		//notwendig für die selection-fields
		$data['selected_class_id'] = $class_id;
		
		$data['classes'] = $this->classes_model->getClasses();
		$data['select_class'] = array( 'call_function' => 'add_results/load_competitors', 'submit_function' => 'add_results/submit', 'string' => 'Bitte geben sie hier ihre Ergebnisse ein', 'upload_sheet' => 'add_results/uploadResultsSheet');
		
		$data['competitors_info'] = $competitor_results;
		
		
		$data['sport_groups'] = $this->contests_sport_groups_model->getSportGroupsUsed();
		
		$this->load->view('select_class_view.php', $data);
		$this->load->view('add_results_view.php', $data);
	}
}
?>