<section>
	<div class="page-header">
		<h1>Einen neuen Wettbewerb erstellen:</h1>
	</div>
	<div class="span3">
		Geben sie hier bitte die wichtigsten Informationen zu ihrem neuen Wettbewerb ein.
	</div>
	<div class="row">
	<div class="span5">
		<?php
		$this->load->helper('form');
		$attributes = array('class' => 'form-horizontal');
		echo form_open('preferences/createContest', $attributes);
		?>
		<fieldset>
			<div class="control-group">
				<label class="control-label">Name:</label>
				<div class="controls">
					<input type="text" name="contest_name" value="" />
				</div>
			</div>
		
			<div class="control-group">
				<label class="control-label">Sportbereich:</label>
				<div class="controls">	
					<select name="sport_section">
						<?php foreach ($sport_sections as $sport_section_index => $sport_section) {
							echo '<option value="' . $sport_section_index .'">' . $sport_section . '</option>';
						} ?>
					</select>
				</div>
			</div>
		
			<div class="control-group">
				<label class="control-label">Jahr des Wettbewerbs:</label>
				<div class="controls" id="year">	
					<!--<input type="text" name="contest_date" value="" />-->
					<select id="contest_date" name="contest_date"> 
					
					</select>
				</div>
			</div>
			
			<div class="form-actions">
				<input type="submit" class="btn btn-primary" name="insert_contest" value="Wettbewerb erstellen" />
			</div>
		</fieldset>
		<?php echo form_close(); ?>
	</div>
	</div>
</section>

<script type="text/javascript">
var minOffset = -10, maxOffset = 10; // Change to whatever you want
var thisYear = (new Date()).getFullYear();
//var select = $('<select>');

for (var i = minOffset; i <= maxOffset; i++) {
    var year = thisYear + i;
    if (year === thisYear) {
    	$('<option>', {value: year, text: year, selected: 'selected'}).appendTo('#contest_date');
    } else {
    	$('<option>', {value: year, text: year}).appendTo('#contest_date');
    }
}

//select.appendTo('#year');
</script>