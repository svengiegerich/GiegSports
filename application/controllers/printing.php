<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Printing extends CI_Controller {
	
	public function index() {
		$this->load->model('printing_model');
		$this->load->model('settings_model');
		$this->load->model('contests_model');
		
		$arg_list = func_get_args();
		$contest = $this->contests_model->getCurrentContest();
		$points_table_used = $this->contests_model->getPointsUsed();
		
		if(!$this->contests_model->table_exists($points_table_used) or $this->contests_model->tableEmpty($points_table_used)) {
			redirect("/competitors?redirected_from='site.php'");
		}
		
		$data['all_input_tables'] = $this->printing_model->allWrittenInputTables($contest);
		$data['all_charters'] = $this->printing_model->allWrittenCharters($contest);
		
		$data['gcp_status'] = $this->settings_model->getGCPStatus();
 		
 		if (!$this->printing_model->printerConfigured()) {
 			$data['printer_set_up'] = FALSE;
 		}
		
		if ($this->input->get('class_selection') AND $this->input->get('print_function')) {
			$this->load->model('classes_model');
			$data['class_selection']['class_id'] = $this->input->get('class_selection');
			$data['class_selection']['class_name'] = $this->classes_model->getClassName($data['class_selection']['class_id']);
			$data['print_function'] = $this->input->get('print_function');
		}
		
		if (isset($arg_list[0]['input_tables']['printing']['status']['completed']) && $arg_list[0]['input_tables']['printing']['status']['completed']) { $data['input_tables']['printing']['status']['completed'] = $arg_list[0]['input_tables']; }
		if (isset($arg_list[0]['error']['uncomplete']) && $arg_list[0]['error']['uncomplete']) { $data['error']['uncomplete'] = true; }
		
		$data['main_content'] = 'printing_view.php';
		$this->load->view('/includes/template.php', $data);
	}
	
	
	//common
	function pausePrinter() {
		$this->load->model('printing_model');
		
		$this->printing_model->pausePrinter();
		echo 'Der Drucker wurde gestoppt';
	}
	
	//Charters
	function do_charters() {
		if ($this->input->post('print')) {
			$this->start_printing_charters();
		} elseif ($this->input->post('download')) {
			$this->download_charters();
		} else {
			$this->index();
		}
	}
	
	function download_charters() {
		if ($this->input->post('download') && $this->input->post('class_selection') != 'Bitte Auswählen') {	
			$this->load->model('printing_model');
			$this->load->model('classes_model');
			$this->load->model('contests_model');
			$this->load->library('zip');
			
			$charter_types = array('honor', 'winner', 'participants');
			$contest = $this->contests_model->getCurrentContest();
			$class = $this->input->post('class_selection');
			$class_name = $this->classes_model->getClassName($class);
			
			foreach ($charter_types as $charter_type) {
				$files[$charter_type] = $this->printing_model->getChartersFilesNames($contest, $charter_type, $class);
			}
				
			foreach ($files as $charter_type => $charters) {
				if (count($charters) > 0) {
					foreach ($charters as $file) {
						$path = './output/' . $contest['contest_id'] . '/charters/' . $charter_type . '/' . $file;
						$this->zip->read_file($path, false);
					}
				}
			}
			
			$zip_file_name = $class_name . '_urkunden' . '.zip';
			
			$this->zip->download($zip_file_name);
		} else {
			$data['error']['uncomplete'] = true;
			$this->index($data);
		}
	}
	
	function start_printing_charters() {
		if ($this->input->post('print_charters') OR $this->input->post('print') && $this->input->post('class_selection') != 'Bitte Auswählen') {
			$this->load->model('printing_model');
			$this->load->model('contests_model');
			$this->load->model('classes_model');
			
			$contest = $this->contests_model->getCurrentContest();
			$data['class']['class_id'] = $this->input->post('class_selection');
			$data['class']['class_name'] = $this->classes_model->getClassName($data['class']['class_id']);
			
			$charters_types = array('honor', 'winner', 'participant');
			foreach ($charters_types as $charter_type) {
				$data['charters_to_print'][$charter_type] = $this->printing_model->getChartersFilesNames($contest, $charter_type, $data['class']['class_id']);
			}
			
			$data['main_content'] = 'print_charters.php';
			$this->load->view('/includes/template.php', $data); 
			
		} else {
			$data['error']['uncomplete'] = true;
			$this->index($data);
		}
	}
	
	function print_charters() {
		if ($this->input->post('class') && $this->input->post('class') != 'Bitte Auswählen') {
			$this->load->model('printing_model');
			$this->load->model('printing_model');
			$this->load->model('contests_model');
			
			$contest = $this->contests_model->getCurrentContest();
			$class = $this->input->post('class');
			
			if ($this->input->post('honor')) {
				 $charter_type = 'ehren';
				 $this->printing_model->printCharterType($contest, $charter_type, $class);
			} elseif ($this->input->post('winner')) {
				 $charter_type = 'winner';
				 $this->printing_model->printCharterType($contest, $charter_type, $class);
			} elseif ($this->input->post('participant')) {
				 $charter_type = 'participant';
				 $this->printing_model->printCharterType($contest, $charter_type, $class);
			}		
		} else {
			$this->index();
		}
	}
	
	function print_honor_charters() {
		if ($this->input->post('class') && $this->input->post('honor_charters_to_print')) {
			$this->load->model('printing_model');
			$this->load->model('contests_model');
			
			$contest = $this->contests_model->getCurrentContest();
			$charter_type = 'honor';
			$class = $this->input->post('class');
			
			//echo 'Der Druckvorgang wird ausgeführt...' . '<br />';
			if ($this->input->post('honor_charters_to_print') == 'all') {	
				$result = $this->printing_model->printCharterType($contest, $charter_type, $class);
			} else {
				$file_name = $this->input->post('honor_charters_to_print');
				$result = $this->printing_model->printCharter($contest, $charter_type, $file_name);
			}
			
			if ($result) {
				echo '<div class="alert alert-success">Die Dateien wurden erfolgreich in die Druckerwarteschlange eingespeist.</div>';
			} else {
				echo 'Es ist leider ein Fehler aufgetreten.';
			}
		} else {
			echo 'Es ist leider ein Fehler aufgetreten.';
		}
	}
	
	function print_winner_charters() {
		if ($this->input->post('class') && $this->input->post('winner_charters_to_print')) {
			$this->load->model('printing_model');
			$this->load->model('contests_model');
			
			$contest = $this->contests_model->getCurrentContest();
			$charter_type = 'winner';
			$class = $this->input->post('class');
			
			//echo 'Der Druckvorgang wird ausgeführt...';
			
			if ($this->input->post('winner_charters_to_print') == 'all') {	
				$result = $this->printing_model->printCharterType($contest, $charter_type, $class);
			} else {
				$file_name = $this->input->post('winner_charters_to_print');
				$result = $this->printing_model->printCharter($contest, $charter_type, $file_name);
			}
				
			if ($result) {
				echo '<div class="alert alert-success">Die Dateien wurden erfolgreich in die Druckerwarteschlange eingespeist.</div>';
			} else {
				echo 'Es ist leider ein Fehler aufgetreten.';
			}
		} else {
			echo 'Es ist leider ein Fehler aufgetreten.';
		}
	}
	
	function print_participant_charters() {
		if ($this->input->post('class') && $this->input->post('participant_charters_to_print')) {
			$this->load->model('printing_model');
			$this->load->model('contests_model');
			
			$contest = $this->contests_model->getCurrentContest();
			$charter_type = 'participant';
			$class = $this->input->post('class');
			
			//echo 'Der Druckvorgang wird ausgeführt...';
			if ($this->input->post('participant_charters_to_print') == 'all') {	
				$result = $this->printing_model->printCharterType($contest, $charter_type, $class);
			} else {
				$file_name = $this->input->post('participant_charters_to_print');
				$result = $this->printing_model->printCharter($contest, $charter_type, $file_name);
			}
			
			if ($result) {
				echo '<div class="alert alert-success">Die Dateien wurden erfolgreich in die Druckerwarteschlange eingespeist.</div>';
			} else {
				echo 'Es ist leider ein Fehler aufgetreten.';
			}
		} else {
			echo 'Es ist leider ein Fehler aufgetreten.';
		}
	}
	
	
	//Input Tables
	function do_input_table() {
		if ($this->input->post('print')) {
			$this->print_input_table();
		} elseif ($this->input->post('download')) {
			$this->download_input_table();
		} else {
			$this->index();
		}
	}
	
	function print_input_table() {
		if ( $this->input->post('print_input_table') OR $this->input->post('print') && ($this->input->post('class_selection') != 'Bitte Auswählen' && $this->input->post('input_tables') != 'Bitte Auswählen')) {
			$this->load->model('printing_model');
			$this->load->model('contests_model');
			
			$contest = $this->contests_model->getCurrentContest();
			$input_table = $this->input->post('input_tables');
			// von Alert oder select?
			if (empty($input_table)) {
				$input_table = $this->input->post('class_selection') . '.pdf';
			}
			
			$data['input_tables']['printing']['status']['completed'] = $this->printing_model->printInputTable($contest, $input_table);	 
			$data['input_tables']['printing']['status']['completed'] = true;
			$this->index($data);
		} else {
			$data['error']['uncomplete'] = true;
			$this->index($data);
		}
	}
	
	function download_input_table() {
		if ($this->input->post('download') && ($this->input->post('class_selection') != 'Bitte Auswählen' && $this->input->post('input_tables') != 'Bitte Auswählen')) {
			$this->load->helper('download');
			$this->load->model('contests_model');
			$this->load->model('classes_model');
			
			$contest = $this->contests_model->getCurrentContest();
			
			if ($this->input->post('class_selection')) {
				$class_id = $this->input->post('class_selection');
				$file_name = $class_id . '.pdf';
			} else if ($this->input->post('input_tables')) {
				$file_name = $this->input->post('input_tables');
			} else {
				return false;
			}
			
			$file = './output/' .  $contest['contest_id'] . '/input_tables/' . $file_name;
			$data = file_get_contents($file);
			
			$download_file_name = 'wettkampfkarte_' . '.pdf';
			force_download($download_file_name, $data);
		} else {
			$data['error']['uncomplete'] = true;
			$this->index($data);
		}
	}
}
?>