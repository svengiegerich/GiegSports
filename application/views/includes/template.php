<?php
	$this->load->model('contests_model');
	/*$contest = $this->contests_model->getCurrentContest();
	$data['contest_name'] = $contest['contest_name'];*/
	
	$this->load->view('includes/header'/*, $data*/);
	
	
	echo '<div class="main_content">';
	$this->load->view($main_content);
	echo '</div>';
	$this->load->view('includes/footer');
?>