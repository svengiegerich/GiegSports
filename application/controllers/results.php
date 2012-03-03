<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Results extends CI_Controller {
	public function index() {
		$this->load->model('classes_model');
		$this->load->model('contests_model');
		$this->load->helper('url');
		
		$points_table_used = $this->contests_model->getPointsUsed();
		if (!$this->contests_model->table_exists($points_table_used) OR $this->contests_model->tableEmpty($points_table_used)) {
			redirect("/competitors?redirected_from='site.php'");
		}
		
		if ($this->classes_model->getClassesTakenPart($points_table_used)) {
			$data['classes'] = $this->classes_model->getClassesTakenPart($points_table_used);
		} else {
			redirect("/add_results?redirected_from='results'");
		}
		
		$data['select_class'] = array( 'call_function' => 'results/load_competitors', 'string' => 'Die Ergebnisse');
		
		$data['main_content'] = 'select_class_view.php';
		$this->load->view('includes/template.php', $data);
		
	}
	
	function load_competitors() {
		$this->load->model('classes_model');	
		$this->load->model('competitors_model');
		$this->load->model('points_model');
		$this->load->model('contests_model');
		$this->load->model('contests_sport_groups_model');
		
		$points_table_used = $this->contests_model->getPointsUsed();
		
		if ($this->classes_model->getClassesTakenPart($points_table_used)) {
			$data['classes'] = $this->classes_model->getClassesTakenPart($points_table_used);
		} else {
			redirect('/add_results');
		}
		
		if ( $this->input->post('class_id') ) {
			$class_id = $this->input->post('class_id');
		} else {
			$class_id = '1';
		}
		$data['selected_class_id'] = $class_id;
		
		$class_name = $this->classes_model->getClassName($class_id);
		$data['select_class'] = array( 'call_function' => 'results/load_competitors', 'submit_function' => 'results/useData', 'string' => 'Die Ergebnisse');
		
		$current_contest = $this->contests_model->getCurrentContest();
		
		$data['competitors_info'] = $this->competitors_model->getCompetitors($class_id);
		$data['competitor_points'] = $this->points_model->getPoints($data['competitors_info']);
		
		$data['who_gets_what'] = $this->points_model->whoGetsWhat($data['competitors_info'], $class_id);
		
		$data['comeptitor_names'] = $this->competitors_model->getCompetitorsNames($data['competitors_info']);
		$data['sport_groups'] = $this->sport_groups_model->getAllSportGroups($current_contest['contest_sport_section']);
		
		$this->load->view('select_class_view.php', $data);
		$this->load->view('results_view.php', $data);
	}
	
	function useData() {
		if ($this->input->post('print')) {
			$this->prepareCharter();
		} else if ($this->input->post('export')) {
			$this->exportData();
		} else {
			$this->index();
		}
	}
	
	function exportData() {
		if ($this->input->post('export')) {
			$this->load->helper('download');
			$this->load->model('competitors_model');
			$this->load->model('points_model');
			$this->load->model('contests_model');
			$this->load->model('classes_model');
			
			$contest = $this->contests_model->getCurrentContest();
			$class_id = $this->input->post('class-select');
			$class_name = $this->classes_model->getClassName($class_id);
			$competitors_info = $this->competitors_model->getCompetitors($class_id);
			$who_gets_what = $this->points_model->whoGetsWhat($competitors_info, $class_id);
			$competitors_points= $this->points_model->getPoints($competitors_info);
			
			foreach ($who_gets_what as $charter_section) {
				foreach ($charter_section as $competitor_id) {
					$export_data[$competitor_id]['name'] = $competitors_info[$competitor_id]['name'];
					foreach ($competitors_points[$competitor_id] as $point_id => $competitor_point) {
						$export_data[$competitor_id][$point_id] = $competitor_point;
					}
				}
			}
			if (!file_exists('./output/' . $contest['contest_id'] . '/data_export') || !is_dir('./output/' . $contest['contest_id'] . '/data_export')) { 
				mkdir ('./output/' . $contest['contest_id'] . '/data_export', 0777, true);
			}
			
			$file_name = 'Ergebnissliste_' . $class_name . '.csv';
			$file = './output/' . $contest['contest_id'] . '/data_export/' . $file_name;            
			$handle = fopen ("$file","a+");
			foreach ($export_data as $fields) {
			   fputcsv($handle, $fields);
			}
			fclose ($handle);
			$csv = file_get_contents($file);	
			
			force_download($file_name, $csv);
		}
		$this->index();
	}
	
	function prepareCharter() {
		$this->load->model('competitors_model');
		$this->load->model('points_model');
		$this->load->model('contests_model');

		$contest = $this->contests_model->getCurrentContest();		
		$class_id =  $this->input->post('class-select');
		$class = $class_id;
		$competitors_info = $this->competitors_model->getCompetitors($class_id);
		$who_gets_what = $this->points_model->whoGetsWhat($competitors_info, $class_id);
		$competitor_points= $this->points_model->getPoints($competitors_info);
		$year = substr($contest['contest_year'], 2);
		
		$i = 0;
		foreach ($who_gets_what as $what_id => $what) {
			$competitors_names = $this->competitors_model->getCompetitorsNames($what);
			$competitors_points = $this->points_model->getPoints($what);
			
			foreach ($what as $who) {
				$print_with_results = FALSE;
				
				if ($competitors_points[$who]['total_points'] > 0) { 
					$competitor_name = $competitors_names[$who];
					$competitor_points = $competitors_points[$who];
					
					switch ($what_id) {
						case 'honorcharter':
							$path = $contest['contest_id'] . '/charters/honor';
							break;
						case 'winnercharter':
							$path = $contest['contest_id'] . '/charters/winner';
							break;
						case 'participantcharter':
							$path = $contest['contest_id'] . '/charters/participant';
							break;
					}
					
					if (!file_exists('./output/' . $path . '/') || !is_dir('./output/' . $path . '/')) { 
						mkdir('./output/' . $path . '/', 0777, true);
					}
				
					$this->makeCharter($competitor_name, $competitor_points, $class_id, $path, $print_with_results, substr($what_id, 0, -7), $year);
					$i++;
				}
			}
		}
		
		if ($i > 0) {
			$handle = fopen ('./output/' . $contest['contest_id'] . '/charters/prepared_charters.txt', 'a+');
			$file_content = fgets($handle);
			$pattern = '/^' . $class . ';' . '/';
			if (!preg_match($pattern, $file_content)) {
				fwrite($handle, $class . ';');
			}
			fclose($handle);
			
			
			//$bash_code = 'echo "' . $class . ';" >> ./output/' . $contest['contest_id'] . '/charters/prepared_charters.gs';
			//$result = shell_exec($bash_code);
		}
		
		$print_function = '/printing/index?class_selection=' . $class_id . '&print_function=printing/do_charters';
		redirect($print_function);
	} 
	
	
	
	
	
	
	
	
	
	

	function makeCharter($competitor_name, $competitor_points, $class_id, $path, $print_with_results, $charter_type, $year) {
		$this->load->model('settings_model');
		
		$print_settings = $this->settings_model->getPrintSettings();
		foreach ($print_settings as $setting_key => $print_setting) {
			//schaut ob es überhaupt mit der pixelverschiebung zu tun hat:
			if($setting_key!=str_replace("axis","",$setting_key)) {
				$$setting_key = $print_setting['setting_value'];
			}
		}
		
		$total_points = $competitor_points['total_points'];
				
		$textnr = 4;
		$width = 420; 
		$height = 595;
		
		if ($charter_type == 'honor') {
			$width = 850;		
			$move_x_axis = 529 + $move_x_axis;
			$move_y_axis = 112 + $move_y_axis;;
		}
		
		$im = imagecreate ($width , $height);
		
		//Dicke der Linien festlegen
		imagesetthickness($im, 50);
		
		//Breite und Höhe des $im Objekts		
		$image_height = ImageSY($im);
		$image_width = ImageSX($im);
		
		//Farben
		$black = ImageColorAllocate ($im, 0, 0, 0);
				
		//Schriften (arial)
		$font = '/libs/fonts/Arial.ttf'; #!!!!!!!!!!!!!!!!!!!!!!!!!!
		$font_height = ImageFontHeight(2);
		$font_width = ImageFontWidth(2);
		$length = $font_width*strlen($competitor_name); 
				
		//Mitte des Bildes
		$image_center_x = ($image_width/2)-($length/2); 
		$image_center_y = ($image_height/2)-($font_height/2); 
				
		//Transparenten Hintergrund erstellen
		$im = imagecreatetruecolor($width,$height); 
		imagesavealpha($im, true); 
		imagealphablending($im, false); 
		$background = imagecolorallocatealpha($im, 255, 255, 255, 127); 
		imagefilledrectangle($im, 0, 0, $width, $height, $background); 
		imagealphablending($im, true); 
		
		//"Drucke" die Jahreszahl
		$x_year = 317 + $move_x_axis;
		$y_year = 160 + $move_y_axis;
		imagettftext($im, 7, 0, $x_year, $y_year, $black, $font, $year);
		
		//"Drucke" den Competitor Name
		$x_competitor_name = $image_center_x + $move_x_axis;
		$y_competitor_name = 356 + $move_y_axis;
		if ($charter_type == 'honor') { 
			$x_competitor_name = $x_competitor_name - 190; 
			$y_competitor_name = $y_competitor_name + 8;
		} 
		imagettftext($im, 15, 0, $x_competitor_name, $y_competitor_name, $black, $font, $competitor_name);
		
		//"Drucke" die Gesamtpunktzahl
		$x_competitor_points = 190 + $move_x_axis;
		$y_competitor_points = 300 + $move_y_axis;
		imagettftext($im, 15, 0, $x_competitor_points, $y_competitor_points, $black, $font, $total_points);
		
		//sport_section_2
		$x_sport_section_2_1 = 198 + $move_x_axis;
		$x_sport_section_2_2 = 263 + $move_x_axis;
		$y_sport_section_2 = 218 + $move_y_axis;
		imageLine($im, $x_sport_section_2_1, $y_sport_section_2, $x_sport_section_2_2, $y_sport_section_2, $black);
		//sport_section_3
		$x_sport_section_3_1 = 198 + $move_x_axis;
		$x_sport_section_3_2 = 263 + $move_x_axis;
		$y_sport_section_3 = 250 + $move_y_axis;
		imageLine($im, $x_sport_section_3_1, $y_sport_section_3, $x_sport_section_3_2, $y_sport_section_3, $black);
		
		//druck auch die Ergebnisse 
		if ($print_with_results) {
			//hier können dann die Ergebnisse rein
		}
		
		//wandelt leerzeichen und  Umlaute um, schreibt alles klein
		$upas = Array("ä" => "ae", "ü" => "ue", "ö" => "oe", "Ä" => "Ae", "Ü" => "Ue", "Ö" => "Oe"); 
		$file_name = str_replace(' ','_',$competitor_name);
		$file_name = strtr($file_name, $upas);
		$file_name = strtolower($file_name);

		$charter_path = './output/' . $path . '/' . $class_id . '_' . $file_name . '.png';
		ImagePNG($im, $charter_path);
		
	}
}
?>