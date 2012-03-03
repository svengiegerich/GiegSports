<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Printing_model extends CI_Model {
	
	/* Writen */
	function allWrittenInputTables($contest) {
		if (!is_dir('./output/' . $contest['contest_id'] . '/input_tables')) { 
			mkdir ('./output/' . $contest['contest_id'] . '/input_tables', 0777, true);
		}
		
		$all_written_input_files = array();
		if ($handle = opendir('./output/' . $contest['contest_id'] . '/input_tables/')) {
		    while (false !== ($file = readdir($handle))) {
		        if ($file != "." && $file != ".." && stripos($file, '.pdf')) {
		        	$this->load->model('classes_model');
		        	$class_id = substr($file, 0 , -4);
		        	$class_name = $this->classes_model->getClassName($class_id);
		        	$all_written_input_files[$class_name] = $file;
		        }
		    }
		    closedir($handle);
		}
		return $all_written_input_files;
	}
	
	function allWrittenCharters($contest) {
		$file_path = './output/' . $contest['contest_id'] . '/charters/prepared_charters.txt';
		
		if (!file_exists($file_path)) { 
			mkdir('./output/' . $contest['contest_id'] . '/charters', 0777, true);
			fopen($file_path, 'w+');
		}
		
		$file = file($file_path);
		
		$all_written_charters = array();
		foreach ($file as $row) {
			$this->load->model('classes_model');
			$classes = explode(';', $row);
			foreach ($classes as $class) {
				if (!empty($class)) {
					$class_id = $class;
					$class_name = $this->classes_model->getClassName($class_id);
					$all_written_charters[$class_name] = $class_id;
				}
			}
		}
		return $all_written_charters; 
	}
	
	function getCompetitorNameFromFileName($file_name) {
		$competitor_name = substr($file_name, 2);
		$upas = Array("ae" => "a", "ue" => "ü", "oe" => "ö", "Ae" => "Ä", "Ue" => "Ü", "Oe" => "Ö"); 
		$competitor_name = str_replace('_',' ', $competitor_name);
		$competitor_name = strtr($competitor_name, $upas);
		$competitor_name = substr($competitor_name, 0, -4);
		$competitor_name = ucfirst($competitor_name);
		
		return $competitor_name;
	}
	
	
	function getChartersFilesNames($contest, $charter_type, $class) {
		$files = array();
		$pattern = '/^' . $class . '_' . '/';
		
		if (!is_dir('./output/' . $contest['contest_id'] . '/charters/' . $charter_type)) { 
			mkdir ('./output/' . $contest['contest_id'] . '/charters/' . $charter_type, 0777, true);
		}
		
		if ($handle = opendir('./output/' . $contest['contest_id'] . '/charters/' . $charter_type)) {
		    while (false !== ($file = readdir($handle))) {
		        if ($file != "." && $file != ".." && preg_match($pattern, $file)) {
		        	
		        	/*$competitor = substr($file, 2);
		        	$upas = Array("ae" => "a", "ue" => "ü", "oe" => "ö", "Ae" => "Ä", "Ue" => "Ü", "Oe" => "Ö"); 
		        	$competitor = str_replace('_',' ', $competitor);
		        	$competitor = strtr($competitor, $upas);
		        	$competitor = substr($competitor, 0, -4);*/
		        	$competitor_name = $this->getCompetitorNameFromFileName($file);
		        	
		        	$files[$competitor_name] = $file;
		        }
		    }
		    closedir($handle);
		}
		
		return $files;
	}
	
	// Real Printing
	function printCharterType($contest, $charter_type, $class) {
		$this->load->model('contests_model');
		$this->load->helper('file'); 
		$this->load->helper(array('phpprintipp', 'file'));
		
		$contest = $this->contests_model->getCurrentContest();
		$files = array();
		$pattern = '/^' . $class . '_' . '/';
		
		if ($handle = opendir('./output/' . $contest['contest_id'] . '/charters/' . $charter_type)) {
		   	while (false !== ($file = readdir($handle))) {
		        if ($file != "." && $file != ".." && stripos($file, '.png') && preg_match($pattern, $file)) {
		        	$files[] = trim($file);
		        }
		    }
		    closedir($handle);
		}
		
		foreach ($files as $file) {
			$print_jobs[] = $this->printCharter($contest, $charter_type, $file);
			
		}
		$result = getUncompletePrintingJobs();
		
		$start_print_job_id = 185; 
		
		foreach ($result as $key => $job) {
			if ($job->job_id->_value0 >= $start_print_job_id) {
				$jobs['job_' . $job->job_id->_value0] = $job->job_state->_value0;
			}
		}
		
		if (isset($print_jobs) && count($files) == count($print_jobs)) {
			//Alle gefunden Dateien wurden in die Warteschleife gegeben
			return true;
		}
		return false;
	}
	
	function printCharter($contest, $charter_type, $file_name) {
		$this->load->helper('file'); 
		$this->load->helper(array('phpprintipp', 'file'));
		$this->load->model('settings_model');
		
		$printer = $this->settings_model->getPrinterToUse();
		
		$file_path = './output/' . $contest['contest_id'] . '/charters/' . $charter_type . '/' . $file_name;
		
		if ($charter_type == 'honor') {
			$print_job = printFile($file_path, $file_path, true);
		} else {
			$print_job = printFile($file_path, $file_name);
		}
		return $print_job;
	}
	
	/* Input Tables */
	function printInputTable($contest, $input_table) {
		$this->load->helper('file'); 
		$this->load->helper(array('phpprintipp', 'file'));
				
		$filepath = './output/' . $contest['contest_id'] . '/input_tables/' . $input_table;
		
		$printing = printFile($filepath, false);
		
		return $printing;
	}
	
	function pausePrinter() {
		$this->load->model('settings_model');
		$this->load->helper('file'); 
		$this->load->helper(array('phpprintipp', 'file'));
		
		$printer = $this->settings_model->getPrinterToUse();
		if ($printer['setting_value'] != 'default') {
			
		}
		pausePrinter();
	}
	
	/* CONFIG */
	function printerConfigured() {
		$this->load->helper('file'); 
		$this->load->helper(array('phpprintipp', 'file'));
		
		$printers = getPrinters();
		if (!empty($printers)){
			return true;
		}
		return false; 
	}
	
	function getAllConfiguredPrinter() {
		$this->load->helper('file'); 
		$this->load->helper(array('phpprintipp', 'file'));
		
		$printers = getPrinters();
		return $printers;
	}
	
} 
?>