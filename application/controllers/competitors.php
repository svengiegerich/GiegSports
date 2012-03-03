<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Competitors extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
	}
	
	public function index() {
		$this->load->model('contests_model');
		
		if (!($this->contests_model->getCurrentContest())) {
			redirect('/preferences');
		}
		
		$arg_list = func_get_args();
		
		if (isset($arg_list[0]['competitor_added'])) { $data['competitor_added'] = true; }
		if (isset($arg_list[0]['competitor_changed_class'])) { $data['competitor_changed_class'] = true; }
		if (isset($arg_list[0]['competitor_removed'])) { $data['competitor_removed'] = true; }
		if (isset($arg_list[0]['competitors_exitsts']) AND isset($arg_list[0]['competitors_added']) AND isset($arg_list[0]['points_table_added']) AND isset($arg_list[0]['competitors_class_changed'])) { 
			$data['competitors_added'] = $arg_list[0];
		}
		if (isset($arg_list[0]['incomplete'])) { $data['error']['incomplete'] = true; }
		if (isset($arg_list[0]['competitor_exists'])) { $data['competitor_exists'] = true; }
		
		if ($this->input->get('redirected_from')) { $data['warning']['redirected_from'] = $this->input->get('redirect_from'); }
		
		$data['main_content'] = 'competitors_view.php';
		$this->load->view('/includes/template.php', $data);
	}
	
	function do_upload() {
		$config['upload_path'] = './uploads/competitors';
		$config['allowed_types'] = 'csv';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
	
		$this->load->library('upload', $config);
	
		if (!$this->upload->do_upload()) {
			$data['error']['upload_error'] = array('error' => $this->upload->display_errors());
			
			$data['main_content'] = 'competitors_view.php';
			$this->load->view('/includes/template.php', $data);
		} else {
			
			$data['upload_data'] = $this->upload->data();
			$file_name = $data['upload_data']['file_name'];
			
			$from_grade = $this->input->post('from_grade');
			$to_grade = $this->input->post('to_grade');
			$competitors = $this->add_competitors($file_name, $from_grade, $to_grade);
			
			$this->index($competitors);
		}
	}
	
	function add_competitors($file_name, $from_grade, $to_grade) {
		$file = base_url("/uploads/competitors/$file_name");
		$row = 1;         
		$divider = $this->input->post('divider');
		$handle = fopen ("$file","r");
		while ( ($data = fgetcsv ($handle, 1000, "$divider")) !== FALSE ) {	
			
			//Die vorgelegte Reihenfolge wird gesetzt
			for ($i = 0; $i != 5; $i++) {
				$entry = $this->input->post($i);
				$competitors[$row][$entry] = $data[$i];
			}
		    $row++;
		}
		fclose ($handle);
		
		$this->load->model('competitors_model');
		$competitors_added = $this->competitors_model->insertCompetitors($competitors, $from_grade, $to_grade);
		return $competitors_added;
	}
	
	/*function addSingleCompetitor() {
		echo 'test';
		
		if($this->input->post('insert_single_competitor')) {
			if ($this->input->post('competitor_first_name') AND $this->input->post('competitor_last_name') AND $competitor_class = $this->input->post('competitor_class') AND $this->input->post('competitor_birthday') AND $this->input->post('competitor_gender')) {
				$this->load->model('competitors_model');
				
				$competitor_first_name = $this->input->post('competitor_first_name');
				$competitor_last_name = $this->input->post('competitor_last_name');
				$competitor_class = $this->input->post('competitor_class');
				$competitor_birthday = $this->input->post('competitor_birthday');
				$competitor_gender = $this->input->post('competitor_gender');
				
				$grade = substr($competitor_class , 0, -1);
			
				$competitor['0'] = array($competitor_last_name, $competitor_first_name, $competitor_birthday, $competitor_gender, $competitor_class);
				$competitor['competitor_added'] = $this->competitors_model->insertCompetitors($competitor, $grade, $grade);
				
				
				
				$this->index($competitor['competitor_added']);
			}
		} else {
			$this->index();
		}	
	}*/
	
	function editCompetitor() {
		if ($this->input->post('competitor_last_name') AND $this->input->post('competitor_first_name') AND $this->input->post('competitor_year') AND $this->input->post('competitor_gender')) {	
			$this->load->model('competitors_model');
			$this->load->model('points_model');
			
			$competitor_name = $this->input->post('competitor_last_name') . ' ' . $this->input->post('competitor_first_name');
			$competitor_year = $this->input->post('competitor_year');
			$competitor_gender = $this->input->post('competitor_gender');	
			$competitor_id = $this->competitors_model->getCompetitorId($competitor_name, $competitor_year, $competitor_gender);
				
			if ($this->input->post('insert_single_competitor')) {
				if ($this->input->post('competitor_class') AND empty($competitor_id)) {
					$this->load->model('add_competitors_model');
					//$competitor_year = $this->input->post('competitor_year');
					$competitor_first_name = $this->input->post('competitor_first_name');
					$competitor_last_name = $this->input->post('competitor_last_name');
					$competitor_class = $this->input->post('competitor_class');
					$grade = substr($competitor_class , 0, -1);
					$competitor['0'] = array('lastname' => $competitor_last_name, 'firstname' => $competitor_first_name, 'date_of_birth' => $competitor_year, 'gender' => $competitor_gender,'class' => $competitor_class);
					
					$competitor['competitor_added'] = $this->competitors_model->insertCompetitors($competitor, $grade, $grade);
					$competitor['competitor_added'] = true;
				} else {
					$competitor['competitor_exists'] = true;
				}
			} else if ($this->input->post('change_competitor_class')) {
				if ($this->input->post('competitor_class') AND !empty($competitor_id)) {
					$this->load->model('points_model');
					$this->load->model('classes_model');
					
					$class_id = $this->classes_model->getClassId($this->input->post('competitor_class'));
					$competitor['competitor_changed_class'] = $this->points_model->changeCompetitorClass($competitor_id, $class_id);
					$competitor['competitor_changed_class'] = true;
				}
			} elseif ($this->input->post('delete_single_competitor') AND !empty($competitor_id)) {
				$this->competitors_model->deleteCompetitor($competitor_id);
				$competitor['competitor_removed'] = $this->points_model->deleteCompetitor($competitor_id);	
			}
			$this->index($competitor);
		} else {
			$error['incomplete'] = 1;
			$this->index($error);
		}
	}
}
?>