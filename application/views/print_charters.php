<?php $this->load->helper('form'); ?>
<section>
	<div class="page-header">
		<h1>Urkunden drucken 
			<small>
				für die Klasse: 
					<!--<div id="class"><?php echo $class; ?></div>-->
					<select name="class" id="class" class="span1">
						<option value="<?php echo $class['class_id']; ?>"><?php echo $class['class_name']; ?></option>
					</select>
			</small>
		</h1>
	</div>

	<div id="print_charter_alter_section">
		
	</div>

	<div id="printing_charter" class="row">
			<div id="honor">
				<div class="row">	
					<div class="span5">
						<h2>
							<div class="span3">Ehrenurkunden:</div>
							<select name="honor_charters_to_print" id="honor_charters_to_print" class="span2">
								<option value="all" selected="selected">Alle (<?php echo count($charters_to_print['honor']); ?>)</option>
								<?php foreach ($charters_to_print['honor'] as $competitor_name => $charter): ?>
									<option value="<?php echo $charter; ?>"><?php echo $competitor_name; ?></option>
								<?php endforeach; ?>
							</select>
						</h2>
					</div>
					<div class="span7">
						<button data-loading-text="loading stuff..." id="1" class="btn primary">Drucken</button>
						<button class="printer_pause icon-refresh btn"><li class="icon-pause"></li></button>
					</div>
				</div>
				
				
				<div class="row offset2">
					<div id="honor_print_result" class="span7"></div>
				</div>
			</div>
	
			<br /><br />
	
			<div id="winner">
				<div class="row">
					<div class="span5">
						<h2>
							<div class="span3">Siegerurkunden:</div>
							<select name="winner_charters_to_print" id="winner_charters_to_print" class="span2">
								<<option value="all" selected="selected">Alle (<?php echo count($charters_to_print['winner']); ?>)</option>
								<?php foreach ($charters_to_print['winner'] as $competitor_name => $charter): ?>
									<option value="<?php echo $charter; ?>"><?php echo $competitor_name; ?></option>
								<?php endforeach; ?>
							</select>
						</h2>
					</div>
					<div class="span7">	
						<button data-loading-text="loading stuff..." id="2" class="btn primary">Drucken</button>
						<button class="printer_pause icon-refresh btn"><li class="icon-pause"></li></button>
					</div>
				</div>
				<div class="row offset2">
					<div id="winner_print_result" class="span7"></div>
				</div>
			</div>
			
			<br /><br />
		
			<div id="participants">
				<div class="row">
					<div class="span5">
						<h2>
							<div class="span3">Teilnehmerurkunden:</div>
							<select name="participants_charters_to_print" id="participants_charters_to_print" class="span2">
								<<option value="all" selected="selected">Alle (<?php echo count($charters_to_print['participant']); ?>)</option>
								<?php foreach ($charters_to_print['participant'] as $competitor_name => $charter): ?>
									<option value="<?php echo $charter; ?>"><?php echo $competitor_name; ?></option>
								<?php endforeach; ?>
							</select>
						</h2>
					</div>
					<div class="span7">
						<button data-loading-text="loading stuff..." id="3" class="btn">Drucken</button>
						<button class="printer_pause icon-refresh btn"><li class="icon-pause"></li></button>
					</div>
				</div>
				<div class="row offset2">
					<div id="participants_print_result" class="span7"></div>
				</div>
			</div>
</section>

<script type="text/javascript">
$('#1').click(function() {
	printHonorCharters();
});

$('#2').click(function() {
	printWinnerCharters();
});

$('#3').click(function() {
	printParticipantCharters();
});

$('.printer_pause').click(function() {
	printerPause();
});

function printHonorCharters() {
	if ($('#honor_charters_to_print option').size() > 1) {
		$('#honor_print_result').append('<p>Der Druckvorgang wird ausgeführt</p>');
	}
	$.ajax( {
		url: "<?php echo base_url() . 'index.php/printing/print_honor_charters'; ?>",
		type: 'POST',
		data: { class: $("#class").val(), honor_charters_to_print: $("#honor_charters_to_print").val()},
		success: function(msg) {
				    $('#honor_print_result').append(msg);
				 }
	});
}

function printWinnerCharters() {
	if ($('#winner_charters_to_print option').size() > 1) {
		$('#winner_print_result').append('<p>Der Druckvorgang wird ausgeführt</p>');
	}
	$.ajax( {
		url: "<?php echo base_url() . 'index.php/printing/print_winner_charters'; ?>",
		type: 'POST',
		data: { class : $("#class").val(), winner_charters_to_print: $("#winner_charters_to_print").val() },
		success: function(msg) {
				    $('#winner_print_result').append(msg);
				 }
	});
}

function printParticipantCharters() {
	if ($('#participant_charters_to_print option').size() > 1) {
		$('#participant_print_result').append('<p>Der Druckvorgang wird ausgeführt</p>');
	}
	$.ajax( {
		url: "<?php echo base_url() . 'index.php/printing/print_participant_charters'; ?>",
		type: 'POST',
		data: { class : $("#class").val(), participant_charters_to_print: $("#participant_charters_to_print").val() },
		success: function(msg) {
				    $('#participants_print_result').append(msg);
				 }
	});
}

function printerPause() {
	$.ajax({
		url: "<?php echo base_url() . 'index.php/printing/pausePrinter'; ?>",
		type: 'POST',
		success: function(msg) {
					$('#print_charter_alter_section').prepend('<div class="row"><div class="span12"><div class="alert">' + msg + '</div></div></div>');			
				 }
	});
}
</script>