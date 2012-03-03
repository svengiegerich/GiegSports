<section>
	<div class="page-header">
		<div class="row">
			<div class="span12">
				<h1>Ergebnisliste hochladen:</h1>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span3">
			Hier können sie ihre Ergebnisslisten aus früheren Wettbewerben hochladen. Bitte beachten sie hierfür, dass die ausgewählten Sportarten den damaligen Gegebenheit entsprechen. Diese können sie wie gewohnt in den Einstellungen festlegen. Auch muss die Reihenfolge, die sie rechts festlegen können, der Datei entsprechen. 
		</div>
		<div class="span9">
			<?php $attributes = array('class' => 'form-horizontal selection-bottom span6'); echo form_open_multipart('add_results/do_upload', $attributes); ?>
				<fieldset>
					<div class="control-group">
						<?php $options['competitor_name'] = 'Name';
						$options['competitor_class'] = 'Klasse';
						$options['competitor_year'] = 'Geburtsjahr';
						$options['competitor_gender'] = 'Geschlecht';
						foreach ($sport_groups as $sport_group_id => $sport_group) {
							$options['sport_group_' . $sport_group_id] = $sport_group['name'];
						}
						$i = 0; ?>
						 
						<label class="control-label">Reihenfolge: </label>
						<div class="controls">
							<?php foreach ($options as $option_id => $option): ?>
								<?php echo form_dropdown($i, $options, $option_id); $i++; ?>
								<br />
							<?php endforeach; ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Datei hochladen:</label>
						<div class="controls">
							<?php $upload_file = array(
						              'name'        => 'result_sheet',
						              'id'          => 'upload',
						              'value'       => '',
						              'maxlength'   => '',
						              'size'        => '',
						              'style'       => '',
						            );     
							echo form_upload($upload_file); ?>
						</div>
					</div>
					<div class="form-actions">
						<?php echo form_submit('submit', 'Hochladen', 'class="btn primary"', 'id=#primary'); ?>
					</div>
				</fieldset>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>