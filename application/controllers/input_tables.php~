<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Input_Tables extends CI_Controller {
	
	public function index() {
		$this->load->model('contests_model');
		$this->load->model('classes_model');
		
		$points_table_used = $this->contests_model->getPointsUsed();
		if (!$this->contests_model->table_exists($points_table_used) or $this->contests_model->tableEmpty($points_table_used)) {
			redirect("/competitors?redirected_from='site.php'");
		}
		
		$data['classes'] = $this->classes_model->getClasses();
		
		$data['main_content'] = 'input_tables_view.php';
		$this->load->view('/includes/template.php', $data);
	}
		
	function print_order() {
		$this->load->model('conditions_model');
		$this->load->model('sport_groups_model');
		$this->load->model('sports_model');
		$this->load->model('contests_model');
		$this->load->model('classes_model');
		$this->load->model('contests_sport_groups_model');
		$this->load->helper('url');
		
		$classes = $this->classes_model->getClasses();
		$sport_section = $this->contests_model->getContestSportSection();
		$condition_type = $this->contests_model->getConditionsUsed();
		$conditions = $this->conditions_model->getAllConditions($sport_section, $condition_type);
		$sports = $this->sports_model->getSports($sport_section);
		$sport_groups = $this->contests_sport_groups_model->getSportGroupsUsed();
		
		if ($this->input->post('class-select')) {
			$class_id = $this->input->post('class-select');
			
			if ($class_id == 'all') {
				foreach ($classes as $class) {
					$this->makeInputTable($class->class_id);
				}
				$url_path = '/printing/index';
				redirect($url_path);
				
			} else {
				if ($this->makeInputTable($class_id)) {
					$url_path = '/printing/index?class_selection=' . $class_id . '&print_function=printing/do_input_table';
					redirect($url_path);
				} else {
					$this->index();
				}
			}
		} else {
			$this->load->model('classes');
			
			$data['classes'] = $this->classes_model->getClasses();
			
			$data['main_content'] = 'input_tables_view.php';
			$this->load->view('/includes/template.php', $data);
		}
	}
	
	/*function viewPrint() {
		$data['main_content'] = 'print_view.php';
		$this->load->view('/includes/template.php', $data);
	}*/
	
	function makeInputTable($class_id) {
		$this->load->model('conditions_model');
		$this->load->model('sport_groups_model');
		$this->load->model('sports_model');
		$this->load->model('contests_model');
		$this->load->model('competitors_model');
		$this->load->model('classes_model');
		$this->load->model('contests_sport_groups_model');
		$this->load->helper('file'); 
		$this->load->helper(array('dompdf', 'file'));
		
		$contest = $this->contests_model->getCurrentContest();
		$sport_section = $this->contests_model->getContestSportSection();
		$current_conditions_type = $this->contests_model->getConditionsUsed();	
		$sports = $this->sports_model->getSports($sport_section);
		$conditions = $this->conditions_model->getAllConditions($sport_section, $current_conditions_type);
		
		$sport_groups = $this->contests_sport_groups_model->getSportGroupsUsed();
					
				
		$competitors = $this->competitors_model->getCompetitors($class_id);
		
		//umstellung!
		$class_name = $this->classes_model->getClassName($class_id);
		if (is_numeric($class_name)) {
			$grade = $class_name;
		} else {
			$grade = substr($this->classes_model->getClassName($class_id), 0, -1);
		}
	
		foreach ($competitors as $competitor_id => $competitor) { 
						
				$competitor_name = $competitor['name'];
						$competitor_year = $this->competitors_model->getAge($competitor_id);
						$competitor_gender = $this->competitors_model->getGender($competitor_id);
						
						if ($current_conditions_type == 'classes') {
							$condition_value = $grade;
						} else {
							$condition_value = $competitor_year;
							if ($condition_value > 19) {
								$condition_value = 19;
							}
						}
						$condition_id = $conditions[$competitor_gender][$condition_value]['id'];
						$competitors_infos[$competitor_gender][$condition_value][$competitor_id] = $competitor;
					} 
					$input_table_html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
								   <html xmlns="http://www.w3.org/1999/xhtml">
									<head> 
										<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
										
										<link rel="stylesheet" type="text/css" href="' . base_url() . 'css/pdf_tables.css" />
									</head>
								   <body>';
					
					//Für die Trainings Datei
					/*$input_table_html .= '
									<table>
										<tbody>
											<tr>
											<td id="t-id">0-4</td>
											<td class="training"></td>
											<td class="training"></td>
											<td class="training"></td>
											<td class="training"></td>
											<td class="training"></td>
											</tr><tr>
											<td id="t-id">5-9</td>
											<td class="training"></td>
											<td class="training"></td>
											<td class="training"></td>
											<td class="training"></td>
											<td class="training"></td>
											</tr>
										</tbody>
								   </table>';*/
					//$input_table_html .= '<div class="training">;</div><div class="divider"></div>';
					
					$input_table_html .= '<h1>' .  'Wettkampfkarte für die Klase: ' . $class_name . '</h1>';
					
					$i = 0;
					
					foreach ($competitors_infos as $competitor_gender_index => $competitors_gender) {
						
						foreach ($competitors_gender as $condtion_value_index => $condition_value) {
							
							//überprüft ob es die selben Bedingungen sind
							foreach ($sport_groups as $sport_group_id => $sport_group) {
								$condition = $conditions[$competitor_gender_index][$condtion_value_index]['sport_group_' . $sport_group_id];
								
								$current_conditions['sport_group_' . $sport_group_id] = $condition;
							}
							
							if (!isset($previous_conditions) OR $current_conditions != $previous_conditions) { 
								if (!$i == 0) {
									$input_table_html .= '</tbody></table>';
								}
								$input_table_html .= '<table><thead><tr>';
								$input_table_html .= '<th id="t-name">' . 'Teilnehmer ' . '</th>';
								//$input_table_html .= '<th id="t-id">' . '' . '/* ID: */' . '</th>';
								
								foreach ($sport_groups as $sport_group_id => $sport_group) {
								
									$condition = $conditions[$competitor_gender_index][$condtion_value_index]['sport_group_' . $sport_group_id];
									$sport_name = $sports[$condition]['sport_name'];
									$input_table_html .= '<th> ' . $sport_group['name'] . ': ' . $sport_name . ' </th>';
								
									//für die Überprüfung der Gleichheit
									$previous_conditions['sport_group_' . $sport_group_id] = $condition;
								}
								$input_table_html .= '</tr></thead>';
							
							
								$input_table_html .= '<tbody>';
							}
	
							foreach ($condition_value as $competitor_id => $competitor) {
									$input_table_html .= '<tr>';
									$input_table_html .= '<td class="competitor_name">' . $competitor['name'] . '</td>'; 
									//$input_table_html .= '<td>' . $competitor_id . ';</td>'; 
									
									foreach ($sport_groups as $sport_group) { 
										$input_table_html .= '<td></td>';	
									}
									$input_table_html .= '</tr>';
							}
							
							if (!isset($previous_conditions) OR $current_conditions != $previous_conditions) { 
								$input_table_html .= '</tbody></table>';
							}
							$i++;
						}
					} 
					$input_table_html .= '</tbody></table>';
					$input_table_html .= '</body></html>';
					
					$html = $input_table_html;
					$pdf = pdf_create($html, '', false);
					$file_name = $class_id . '.pdf';
					
					if (!file_exists('./output/' . $contest['contest_id'] . '/input_tables') || !is_dir('./output/' . $contest['contest_id'] . '/input_tables')) { 
						mkdir('./output/' . $contest['contest_id'] . '/input_tables', 0777, true);
					}
					
					write_file('./output/' . $contest['contest_id'] . '/input_tables/' . $file_name, $pdf);
					
					return TRUE;
	} 
	
	/*function download_print_input_table() {
		if ($this->input->post('print')) {
			$this->print_input_table();
		} elseif ($this->input->post('download')) {
			$this->download_input_table();
		} else {
			$this->index();
		}
	}
	
	function print_input_table() {
		if ( $this->input->post('print') ) {
			$class_selection = $this->input->post('class_selection');
			$dir = 'input_tables';
			$files[0] = $class_selection . '.pdf';
			
			$this->print_file($dir, $files);	 
		} else {
			$this->index();
		}
	}
	
	/*function download_input_table() {
		if ($this->input->post('download')) {
		
		$this->load->helper('download');
		
		$class_id = $this->input->post('class_selection');
		
		$file = './input_tables/' . $class_id . '.pdf';
		$data = file_get_contents($file);	
		
		$download_file_name = 'input_table_' . $class_id . '.pdf';
		force_download($download_file_name, $data);
		
		} else {
			$this->index();
		}
	}
	
	
	function print_file($dir, $files) {
		$success = 'success';
		
		//MUSS DRINGEND BEARBEITET WERDEN
		$current_path = shell_exec('pwd');
		$current_path = substr($current_path, 0, -1);
		$path = "$current_path/$dir/";
		
		if (isset($files['all'])) {
			//findet zuerst alle Dateien mit der folgenden Endung 
			$file_type = $files['all'];
			$bash_all_files = 'cd ' . $path . ' && for i in *.' . $file_type .'; do mv -i "$i;" "prefix.$i"; done';
			$all_files = shell_exec($bash_all_files);
			
			$files = explode(';', $all_files);
			
			$bash_code = $this->create_print_command($files, $success);
			
		} else {
			$bash_code = $this->create_print_command($files, $success);
		}
		echo $bash_code;
	}
	
	function create_print_command($files, $sucess) {
		$bash_code = '';
		foreach ($files as $file_name) {
			$bash_code .= 'lp ' . $file_name . ' && echo ' . $sucess . ' && ';
		}
		$bash_code = substr($bash_code, 0, -4);
		return $bash_code;
	}*/
	
	/*function allWrittenInputTables() {
		$bash_code = 'ls ./output/input_tables/';
		$result = shell_exec($bash_code);
		
		$all_wirtten_input_files = explode("\n", $result);
		return $all_wirtten_input_files;
	}*/
}
?>