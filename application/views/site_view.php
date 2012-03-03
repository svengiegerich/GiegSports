<?php $this->load->helper('form'); ?>

<script src="<?php echo base_url();?>js/libs/easySlider1.7.js"></script>
<script type="text/javascript">
	$(document).ready(function(){	
		$("#slider").easySlider({
			auto: true, 
			continuous: true,
			numeric: true,
			speed: 1500,
			pause: 10000,
		});
	});	
</script>

<section>

	<div class="row">
		<!--<div class="span12">
			<h2>Daten zu dem Wettbewerb '<?php echo '' . $current_contest['contest_name']; ?>', der
			<?php echo $current_contest['contest_year']; ?> veranstaltet wird:</h2>
		</div>-->
	</div>

	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<div id="slider_box" class="row">
		<div id="content">

			<div id="slider" class="span12">
				<ul>
					<?php /*foreach ($max_points as $sport_group_id => $sport_group):
						$average_points = $sport_group['average_points'];
						$best_competitor = $sport_group['competitor_id'];
						$most_points = $sport_group['points']; 
					?>
						<li>
							<div>
								<script type="text/javascript">
	    							google.load("visualization", "1", {packages:["corechart"]});
	     							google.setOnLoadCallback(drawChart);
	   							    function drawChart() {
	        							var data = new google.visualization.DataTable();
	        							data.addColumn('number', 'Durschnitt');
	        							data.addColumn('number', 'höchste Punktzahl');
	        							data.addRows([
	         								 <?php echo "[" . $average_points . " , " . $most_points . "],"; ?>
	        							]);
							
										var options = {
	          								width: 600, height: 240,
	         								title: 'Bestleistung: <?php echo $sport_group_id; ?>'
	       								};
	
	       					 			var chart = new google.visualization.ColumnChart(document.getElementById('chart_<?php echo $sport_group_id;?>'));
	        							chart.draw(data, options);
	      							}
								</script>
								<div id="chart_<?php echo $sport_group_id; ?>"></div>
							</div>
						</li>
					<?php endforeach; */?>
					<?php $this->load->view('slides.php', $contest_info); ?>
				</ul>
			</div>
		</div>
	</div>
	
	<div id="quick_links" class="row">
		<div class="span12">
			<h1>Schnellnavigation</h1>
			<table class="table table-bordered table-striped">
			  	<thead>
			 	 </thead>
			  	<tbody>
				    <tr>
				      <td><a href="./index.php/add_results" >Ergebnisse eintragen</a></td>
				    </tr>
				    <tr>
				    	<td><a href="./index.php/results" >Ergebnisse anschauen</a></td>
				    </tr>
				    <tr> 
				    	<td><a href="./index.php/results" >Ergebnisse anschauen</a></td>
				    </tr>
				    <tr> 
				    	<td><a href="./index.php/printing" >Urkunden und Wettkampfkarten drucken</a></td>
				    </tr>
				    <tr> 
				    	<td><a href="./index.php/competitor" >Teilnehmer hinzufügen und bearbeiten</a></td>
				    </tr>
				    <tr> 
				    	<td><a href="./index.php/preferences" >Einstellungen und Anpassungen vornehmen</a></td>
				    </tr>
				    <tr> 
				    	<td><a href="./index.php/input_tables" >Wettkampfkarten vorbereiten</a></td>
				    </tr>
			  	</tbody>
			</table>
		</div>
	</div>

	<div id="class_duel" class="row">
		<div class="span12">
		<h2>Klassenduell:</h2>
		<?php $attributes = array('id' => 'class_duel_form'); echo form_open('site/classDuel', $attributes); ?>
			<div class="span3 offset2">
				<select id="first_class_select" name="first_class_select">
					<option value="please_select">-- Bitte Auswählen --</option>
					<?php foreach ($classes as $class): ?>
						<option value="<? echo $class->class_id; ?>" id="<? echo $class->class_id; ?>"><? echo $class->class_name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		
			<div class="span3">
				<select id="second_class_select" name="second_class_select">
					<option value="please_select">-- Bitte Auswählen --</option>
					<?php foreach ($classes as $class): ?>
						<option value="<? echo $class->class_id; ?>" id="<? echo $class->class_id; ?>"><? echo $class->class_name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="span1"><button class="btn primary" id="start_class_duel">Vergleichen</button></div>
		<?php echo form_close(); ?>
		</div>
	</div>
	<div id="result"></div>

<script>
$(document).ready(function(){  
	$("#class_duel_form").submit(function(){
		var print_settings_form = $('#class_duel_form');
		$.ajax({
			url: print_settings_form.attr('action') + "?ajax=true",
			type: print_settings_form.attr('method'),
			data: print_settings_form.serialize(),
			success: function(msg) {
						    $('#result').html(msg);
					 }
		});
		
		return false;
	});
});
</script>