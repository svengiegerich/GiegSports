<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Site extends CI_Controller {
	
	public function index() {
		$this->load->model('contests_model');
		$this->load->model('points_model');
		$this->load->model('classes_model');
		$this->load->model('conditions_model');
		$this->load->model('sport_groups_model');
		$this->load->model('competitors_model');
		$this->load->model('ac_model');
		$this->load->model('sports_model');
		
		$arg_list = func_get_args();
		
		$points_table_used = $this->contests_model->getPointsUsed();
		if(!$this->contests_model->table_exists($points_table_used)) {
			redirect("/competitors?redirected_from='site.php'");
		}
		
		if (!$this->points_model->enoughResults($points_table_used)) {
			redirect("/add_results?redirected_from='site.php'");
		}
		
		if ($this->points_model->enoughResults($points_table_used)) {$data['classes'] = $this->classes_model->getClasses();
			$data['contest_info']['current_contest'] = $this->contests_model->getCurrentContest();
		
			$sport_section = $this->contests_model->getContestSportSection();
			$max_points = $this->points_model->getMaxPoints($sport_section);
			$condition_type = $this->contests_model->getConditionsUsed();
			$conditions = $this->conditions_model->getAllConditions($sport_section, $condition_type);
			$current_conditions_type = $this->contests_model->getConditionsUsed();
			
			foreach ($max_points as $sport_group_id => $sport_group_data) {
		
				$sport_group_name = $this->sport_groups_model->getSportGroupName($sport_group_id);
				
				$competitor_info = $this->competitors_model->getCompetitor($sport_group_data['competitor_id'], $points_table_used);
				
				$competitor_gender = $competitor_info['competitor_gender'];
				if ($current_conditions_type == 'classes') {
					if (is_numeric($competitor_info['class_id'])) {
						$class_name = $this->classes_model->getClassName($competitor_info['class_id']);						
						$value = substr($class_name, 0, -1);
					} else {					
						$value = $competitor_info['class_id'];
					}
				} else if ($current_conditions_type == 'years') {
					$age = $this->competitors_model->getAge($sport_group_data['competitor_id']);
					$value = $age;
					if ($value > 19) {
						$value = 19;
					}
				}		
				
				$sport_id = $conditions[$competitor_gender][$value]['sport_group_' . $sport_group_id];
				$a = $this->ac_model->getA($sport_id, $competitor_gender);
				$c = $this->ac_model->getC($sport_id, $competitor_gender);
				
				$calcus = $this->sport_groups_model->getCalcus($sport_group_id);
				if ($calcus == 1 || $calcus == 2) {
					// funktioniert nur, wenn z.b. 100m
					if (is_numeric($this->sports_model->getSport($sport_id))) {
						$d = $this->sports_model->getSport($sport_id);
					} else {
						$sport = $this->sports_model->getSport($sport_id);
						$d = substr($sport['sport_name'], 0, -1);
					}
					$result = $this->points_model->getResultFromPoints($sport_group_data['points'], $a, $c, $calcus, $d);
					$average_result = $this->points_model->getResultFromPoints($sport_group_data['average_points'], $a, $c, $calcus, $d);
				} else {
					$result = $this->points_model->getResultFromPoints($sport_group_data['points'], $a, $c, $calcus);
					$average_result = $this->points_model->getResultFromPoints($sport_group_data['average_points'], $a, $c, $calcus);
				}
				
				$average_points = $sport_group_data['average_points'];
				$max_points = $sport_group_data['points'];
				
				$data['contest_info']['max_points'][$sport_group_name]['average_points'] = $average_points;
				$data['contest_info']['max_points'][$sport_group_name]['average_result'] = $average_result;
				$data['contest_info']['max_points'][$sport_group_name]['max_points'] = $max_points;
				$data['contest_info']['max_points'][$sport_group_name]['calcus'] = $sport_group_data['calcus'];
				$data['contest_info']['max_points'][$sport_group_name]['competitor'] = array('competitor_id' => $sport_group_data['competitor_id'], 'competitor_name' => $competitor_info['competitor_name'], 'competitor_result' => $result);
			}
			
			
			$data['contest_info']['count_competitors_have_taken_part'] = $this->points_model->enoughResults($points_table_used);
		}
		
		if (count($arg_list) == 2) {
			$data['main_content'] = array('site_view.php', 'class_duel_view.php');
		} else {
			$data['main_content'] = 'site_view.php';
		}
			
		$this->load->view('includes/template', $data); 
	}
	
	function classDuel() {
		$this->load->model('points_model');
		$this->load->model('contests_model');
		$this->load->model('classes_model');
		
		$points_table_used = $this->contests_model->getPointsUsed();
		
		$first_class = $this->input->post('first_class_select');
		$second_class = $this->input->post('second_class_select');
		
		$points_first_class = $this->points_model->getAverageClassPoints($first_class, $points_table_used);
		$points_second_class = $this->points_model->getAverageClassPoints($second_class, $points_table_used);
		
		$first_class_name = $this->classes_model->getClassName($first_class);
		$second_class_name = $this->classes_model->getClassName($second_class);
		
		
		$data['winner']['points']  = max($points_first_class, $points_second_class);
		if (count($data['winner']) == 1) {
			if ($data['winner']['points'] == $points_first_class) {
				$data['loser'] = array('class_name' => $second_class_name, 'points' => $points_second_class);
				$data['winner']['class_name'] = $first_class_name;
			} else {
				$data['loser'] = array('class_name' => $first_class_name, 'points' => $points_first_class);
				$data['winner']['class_name'] = $second_class_name;
			}
		}
		
		if ($this->input->get('ajax')) {	
			$this->load->view('class_duel_view.php', $data); 	
		} else {
			$this->index($data);
		}
	}
}
?>