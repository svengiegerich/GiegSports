<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Preferences extends CI_Controller {
	
	public function index() {
		$this->load->model('sport_groups_model');
		$this->load->model('sports_model');
		$this->load->model('conditions_model');
		$this->load->model('sport_sections_model');
		$this->load->model('contests_model');
		$this->load->model('settings_model');
		$this->load->model('contests_model');
		$this->load->model('contests_sport_groups_model');
		$this->load->model('printing_model');
		$this->load->model('points_model');
		
		$arg_list = func_get_args();
		
		if (!($this->contests_model->getCurrentContest()) || !($this->contests_model->getAllContests())) {
			redirect('/preferences/createContest');
		}
		
		$points_table_used = $this->contests_model->getPointsUsed();
		
		if(!$this->contests_model->table_exists($points_table_used) or $this->contests_model->tableEmpty($points_table_used)) {
			$data['warning']['no_competitors'] = true;
		}
		
		if (!$this->printing_model->printerConfigured()) {
			$data['printer_set_up'] = FALSE;
		} else {
			$data['printers'] = $this->printing_model->getAllConfiguredPrinter();
		}
		
		$data['gcp'] = $this->settings_model->getGCPStatus();
		$data['print_settings'] = $this->settings_model->getPrintSettings();
		
		$sport_section = $this->contests_model->getContestSportSection();
		$data['sport_section_name'] = $this->sport_sections_model->getSportSectionName($sport_section);
		
		$data['all_conditions_types'] = array('classes', 'years');
		$data['current_points_table_year'] = substr($this->contests_model->getPointsUsed(), 9);
		$data['current_conditions_type'] = $this->contests_model->getConditionsUsed();
		$data['conditions'] = $this->conditions_model->getAllConditions($sport_section, $data['current_conditions_type']);
		if ($sport_section == 1) { 
			$data['timekeeping'] = $this->settings_model->getTimekeeping(); 
			$data['timekeeping_options'] = array(1 => 'per Hand', 2 => 'elektronisch'); 
		}
		
		$data['sports'] = $this->sports_model->getSportsBySportGroups($sport_section);
		$data['all_sport_groups'] = $this->sport_groups_model->getAllSportGroups($sport_section);
		$data['used_sport_groups'] = $this->contests_sport_groups_model->getSportGroupsUsed();
		
		if (empty($data['used_sport_groups'])) { $data['warning']['no_used_sport_groups'] = true;  }
		if (!isset($data['warning']['no_competitors']) && $this->points_model->enoughResults($points_table_used)) { $data['competitors_have_taken_part'] = true; }
		
		if (isset($arg_list[0]['error']['incomplete'])) { $data['error']['incomplete'] = true; }
		if (isset($arg_list[0]['success']['create_sport_group'])) { $data['success']['create_sport_group'] = true; }
		if (isset($arg_list[0]['success']['conditions_reseted'])) { $data['success']['conditions_reseted'] = true; }
		if (isset($arg_list[0]['success']['conditions_changed'])) { $data['success']['conditions_changed'] = true; }
		
		$data['all_contests'] = $this->contests_model->getAllContests();
		$data['current_contest'] = $this->contests_model->getCurrentContest();
		
		
		
		$data['points_nedeed'] = $this->conditions_model->getPointsNeeded();
		
		$data['main_content'] = 'preferences_view.php';
		$this->load->view('includes/template.php', $data);
	}
	
	/* Sport Groups */
	function changeSportGroups() {
		if ($this->input->post('sport_group') OR $this->input->post('sport_group')) {
			$this->load->model('contests_sport_groups_model');
			
			$sport_groups = $this->input->post('sport_group');
			
			$this->contests_sport_groups_model->updateContestSportGroups($sport_groups);
			$success = true;
		}
		
		if (!$this->input->get('ajax')) {
			$this->index();
		} else {
			if ($success) {
				echo 'success';
			}
		}
	}
	
	/* Conditions */
	function changeConditions() {
		if ($this->input->post('change_conditions_type_used')) {
			$this->changeConditionsTableUsed();
			
			if (!$this->input->get('ajax')) {
				$data['success']['conditions_changed'] = true;
				$this->index($data);
			}
		} elseif ($this->input->post('extended_conditions_changes')) {
			$this->changeExtendedConditions();
			
			if (!$this->input->get('ajax')) {
				$data['success']['conditions_changed'] = true;
				$this->index($data);
			}
		} elseif ($this->input->post('reset_conditions')) {
			$this->load->model('conditions_model');
			$this->conditions_model->resetConditionsTable();
			
			if (!$this->input->get('ajax')) {
				$data['success']['conditions_reseted'] = true;
				$this->index($data);
			}
		}
		
		if ($this->input->get('ajax')) {
			echo 'success';
		}
	}
	
	function changeConditionsTableUsed() {
		if ($this->input->post('conditions_type-select')) {
			$this->load->model('contests_model');
			
			$conditions_type = $this->input->post('conditions_type-select');
			$this->contests_model->UpdateConditionsTableUsed($conditions_type, 11);
		}
	}
	
	function changeExtendedConditions() {
		$this->load->model('sport_groups_model');
		$this->load->model('sports_model');
		$this->load->model('conditions_model');
		$this->load->model('contests_model');
		
		$sport_section = $this->contests_model->getContestSportSection();
		$condition_type = $this->contests_model->getConditionsUsed();
		$conditions = $this->conditions_model->getAllConditions($sport_section, $condition_type);
		//benutz diese Funktion, da diese nicht über id geht, sondern über name
		$sports = $this->sports_model->getAllSportsNames($sport_section);
		$sport_groups = $this->sport_groups_model->getAllSportGroups($sport_section);
		
		if ($sport_section == 1 && $this->input->post('timekeeping')) { 
			$this->load->model('settings_model');
			
			$setting_key = 'timekeeping';
			$setting_value = $this->input->post('timekeeping');
			$this->settings_model->updateSettingValue($setting_key, $setting_value);
		}
		
		//für jedes Geschlecht
		foreach ($conditions as $gender => $conditions_gender) {
			//für jedes conditions_value
			foreach ($conditions_gender as $conditions_value) {
				//für jede Sport_gruppe
				foreach ($sport_groups as $sport_group_id => $sport_group) {
					$post_sport_groups_query = $conditions_value['id'] . '_' . $sport_group_id;
					
					if (is_numeric($this->input->post($post_sport_groups_query)) ) {
						//$conditions_sport_name = $this->input->post($post_sport_groups_query);
						//$conditions_sport_id = $sports[$conditions_sport_name];
						
						$conditions_sport_id = $this->input->post($post_sport_groups_query);
						$conditions_update[$conditions_value['id']]['sport_group_' . $sport_group_id] = $conditions_sport_id;
						
					} else {
						//$conditions_sport_name = $this->input->post($post_sport_groups_query);
						//$conditions_sport_id = $sports[$conditions_sport_name];
						//$conditions_update[$conditions_value['id']]['sport_group_' . $sport_group_id] = $conditions_sport_id;
					}
				}
				$conditions_points_query = $gender . '_' . $conditions_value['value'] . '_';
				
				$conditions_honor = $this->input->post($conditions_points_query . 'honor');
				$conditions_winner = $this->input->post($conditions_points_query . 'winner');
			
				$conditions_update[$conditions_value['id']]['condition_honor'] = $conditions_honor;
				$conditions_update[$conditions_value['id']]['condition_winner'] = $conditions_winner;
			}
		}
		$this->conditions_model->insertConditionsUpdate($conditions_update);
	}
	
	/* Contests */
	function doContest() {
		if ($this->input->post('change_contest_used')) {
			$this->changeContest();
		} else if ($this->input->post('delete_contest')) {
			$this->deleteContest();
		}
		
		if (!$this->input->post('create_contest')) {
			$this->index();
		} else {
			$this->createContest();
		}
	}
	
	function changeContest() {
		if ($this->input->post('change_contest_used') && $this->input->post('contests-select')) {
			if ($this->input->post('contests-select')) {
				$this->load->model('contests_model');
				
				$contest_id = $this->input->post('contests-select');
				$this->contests_model->UpdateContestUsed($contest_id);
			}
		} else {
			$this->createContest();
		}
	}
	
	function deleteContest() {
		if ($this->input->post('delete_contest')) {
			$this->load->model('contests_model');
			
			$contest_id = $this->input->post('contests-select'); 
			//$contest_used = $this->contests_model->getCurrentContest();

			$this->contests_model->deleteContest($contest_id);
			
			/*if ($contest_used['contest_id'] == $contest_id) {
				redirect('/preferences/createContest');
			}*/
		}
	}
	
	function createContest() {
		if ($this->input->post('insert_contest')) {
			$this->load->model('contests_model');
			
			if ($this->input->post('contest_name') AND $this->input->post('sport_section') AND $this->input->post('contest_date')) {
				$contest_info['contest_name'] = $this->input->post('contest_name');
				$contest_info['contest_sport_section'] = $this->input->post('sport_section');
				$contest_info['contest_year'] = $this->input->post('contest_date');
			
				$this->contests_model->createContest($contest_info);
			}
			$this->index();
		} else {
			$this->load->model('sport_sections_model');
			
			$data['sport_sections'] = $this->sport_sections_model->getSportSections();
		
			$data['main_content'] = 'create_contest_view.php';
			$this->load->view('includes/template.php', $data);
		}
	}
	
	function changePrintingSettings() {	
		if ($this->input->post('printer')) {
			$this->load->model('settings_model');
			
			$print_settings = $this->settings_model->getPrintSettings();
			
			foreach ($print_settings as $setting_key => $print_setting) {
				$setting_value = $this->input->post($setting_key);
				if (is_numeric($setting_value)) {
					$this->settings_model->updateSettingValue($setting_key, $setting_value);
					$success = true;
				}
			}
			
			if ($this->input->post('printer')) {
				$this->settings_model->updateSettingValue('printer', $this->input->post('printer'));
			}
			//$this->settings_model->updateSettingValue('gcp', $this->input->post('gcp'));
		}
		if (!$this->input->get('ajax')) {
			$this->index();
		} else {
			if ($success) {
				echo 'success';
			}
		}
	}
	
	function createDiscipline() {
		/*
			- insert sport_group AND sport
			- $this->db->insert_id();
			- select * conditions WHERE sport_section = xy;
			- insert foreach conditions with sport_group_id AND sport_id
		*/
		if ($this->input->post('sport_name') && $this->input->post('sport_group_name')) {
			$this->load->model('contests_model');
			$this->load->model('sport_groups_model');
			$this->load->model('conditions_model');
			$this->load->model('sports_model');
			
			$result = 0;
			$sport_section = '1';
			$sport_group_name = $this->input->post('sport_group_name');
			$sport_name = $this->input->post('sport_name');
			$calcus = $this->input->post('calcus');
			
			/*$sport_section = '1';
			$sport_group_name = 'Kirschenspucken';
			$sport_name = 'Kirschenweitspucken';*/
			
			//$conditions_types = array('classes', 'years');
			$condition_type = 'years';
			//foreach ($conditions_types as $condition_type) {
				$conditions_sections[] = $this->conditions_model->getAllConditions($sport_section, $condition_type);
			//}
			
			$sport_group_id = $this->sport_groups_model->insertSportGroup($sport_group_name, $sport_section, $calcus);
			$sport_id = $this->sports_model->insertSport($sport_name, $sport_group_id);
			
			foreach ($conditions_sections as $conditions) {
				foreach ($conditions as $gender => $conditions_value) {
					foreach ($conditions_value as $condition) {
						$result = $this->conditions_model->insertConditionSport($condition['id'], $sport_group_id, $sport_id);
						if ($result) {
							$result++;
						}
					}
				}
			}
			
			/*$condition_type = 'classes';
			$conditions_sections[] = $this->conditions_model->getAllConditions($sport_section, $condition_type);
			foreach ($conditions_sections as $conditions) {
				foreach ($conditions as $gender => $conditions_value) {
					foreach ($conditions_value as $condition) {
						$result = $this->conditions_model->insertConditionSport($condition['id'], $sport_group_id, $sport_id);
						if ($result) {
							$result++;
						}
					}
				}
			}*/
			
			$data['success']['create_sport_group'] = true;
			$this->index($data);
		} else {
			$data['error']['incomplete'] = true;
			$this->index($data);
		}	
	}
}
?>