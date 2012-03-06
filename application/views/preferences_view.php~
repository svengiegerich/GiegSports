<?php $this->load->helper('form'); ?>

<section>
	<?php if (isset($success)): ?>
		<?php if (isset($success['sport_groups_changed'])): ?>
			<div class="alert alert-success fade in" data-alert="alert">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				<strong>Erfolgreich.</strong> Die Sport-Gruppe wurde erfolgreich erstellt.
			</div>
		<?php endif; ?>
		<?php if (isset($success['conditions_changed'])): ?>
			<div class="alert alert-success fade in" data-alert="alert">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				<strong>Erfolgreich durchgeführt.</strong> Die Bedingungen wurden ohne Fehler geändert.
			</div>
		<?php endif; ?>
		<?php if (isset($success['conditions_reseted'])): ?>
			<div class="alert alert-success fade in" data-alert="alert">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				<strong>Erfolgreich durchgeführt.</strong> Die Bedingungen wurden zurückgesetzt.
			</div>
		<?php endif; ?>
	<?php endif; ?>
	
	<div>
		<h1>Einstellungen für den Wettbewerb '<?php echo $current_contest['contest_name']; ?>' in dem Sport Bereich '<?php echo $sport_section_name; ?>':</h1>
	</div>
		
	<div class="row">
		<div id="subnav" class="span46">
			<ul class="nav nav-pills">
				   <li><a href="#sport_groups">Sportgruppen</a></li>
				   <li><a href="#conditions">Bedingungen</a></li>
				   <li><a href="#printing">Drucken</a></li>
				   <li><a href="#contests">Wettbewerbe</a></li>
			 </ul>
		</div>
	</div>	
	
	<br />
		
	<?php if (isset($warning)): ?>
		<?php if (isset($warning['no_used_sport_groups']) && $warning['no_used_sport_groups']): ?>
			<div class="alert alert-warning fade in" data-alert="alert">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				<strong>Sie haben noch keine Sport Gruppen ausgewählt.</strong> Bitte wählen sie nun die Sportgruppen aus, welche sie in den Wettbewerb benutzen wollen.
			</div>
		<?php endif; ?>
		<?php if (isset($warning['no_competitors']) && $warning['no_competitors']): ?>
			<div class="alert alert-warning fade in" data-alert="alert">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				<strong>Sie haben noch keine Teilnehmer in die Datenbank hinzugefügt.</strong>Beachten sie bitte, dass Sie ohne diese nicht arbeiten können. <a href="<?php echo base_url() . 'index.php/competitors'; ?>">Teilnehmer hinzufügen</a>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	
	<?php if (isset($error)): ?>
		<?php if (isset($error['incomplete'])): ?>
			<div class="alert alert-error fade in" data-alert="alert">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				<strong>Unvollständige Daten!</strong>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	
	<section id="sport_groups">
		<div class="page-header">
			<h1>Sportgruppen:</h1>
		</div>
		<div class="row">
			<div class="span12" id ="change_sport_groups_alert_section">
				<?php if (isset($competitors_have_taken_part)): ?>
					<div class="alert alert-warning fade in" data-alert="alert">
						<a class="close" data-dismiss="alert" href="#">&times;</a>
						Während des Wettbewerbs können keine Sportarten geändert werden.
					</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="row">
			<div class="span3">
				Hier können sie die Sportbereiche, die später im Wettkampf zum Einsatz kommen sollen, auswählen.
				
				<br /><br />
				
				<button class="btn" data-toggle="modal" data-target="#create_sport-group_alert" <?php echo (isset($competitors_have_taken_part)) ? 'disabled="disabled" ' : '' ?>>
					Eigene Sportgruppe erstellen
				</button>
			</div>
			<div class="span7">
				<h2>Welche Sport Bereiche soll es geben:</h2>
				<?php $attributes = array('id' => 'change_sport_groups_form'); echo form_open('preferences/changeSportGroups', $attributes); ?>
					<fieldset class="span3">
						<?php foreach ($all_sport_groups as $sport_group_id => $sport_group): ?>
								<div class="control-group">	
									<div class="checkbox">
										<label class="control-label">
											<input type="checkbox" name="sport_group[]" value="<?php echo $sport_group_id; ?>" 
												<?php echo ($sport_group_id == isset($used_sport_groups[$sport_group_id])) ? 'checked="checked" ' : '' ?>
												<?php echo (isset($competitors_have_taken_part)) ? 'disabled="disabled" ' : '' ?>
												/> <?php echo $sport_group['name']; ?>
										</label>
									</div>
								</div>
						<?php endforeach; ?>
						<div class="form-actions">
							<input type="submit" name="submit" class="btn" value="Ändern der Sport Bereiche" <?php echo (isset($competitors_have_taken_part)) ? 'disabled="disabled" ' : '' ?> />
						</div>
					</fieldset>
				<?php echo form_close(); ?>
			</div>
			
			<div id="create_sport-group_alert" class="modal hide fade">
				<?php $attributes = array('class' => 'form-horizontal'); echo form_open('preferences/createDiscipline',$attributes); ?>	
				  	<div class="modal-header">
				    	<a class="close" data-dismiss="modal">×</a>
				    	<h3>Eine eigene Sportgruppe erstellen?</h3>
				  	</div>
				  	<div class="modal-body">
				    	<fieldset>
					    	<div class="control-group">
				    			<label class="control-label">Sport Gruppen Name:</label>
				    		    <div class="controls">
				    		        <input type="text" id="sport_group_name" name="sport_group_name">
				    			</div>
				    		</div>
				    		<div class="control-group">
				    			<label class="control-label">Sport Name:</label>
				    		    <div class="controls">
				    		        <input type="text" id="sport_name" name="sport_name">
				    			</div>
				    		</div>
				    		<div class="control-group">
				    			<label class="control-label">Bewertung:</label>
				    		    <div class="controls">
				    		        <select name="calcus">
				    		        	<option selected="selected" value="0">Punkte</option>
				    		        </select>
				    			</div>
				    		</div>    
				    	</fieldset>
					</div>
					<div class="modal-footer">
						<input type="submit" name="delete_contest" value="Sportgruppe mit Diziplin erstellen" class="btn btn-primary"/>
					    <a href="#" class="btn" data-dismiss="modal">Abbrechen</a>
					</div>
				<?php echo form_close(); ?>
			</div>
		
		</div>
	</section>
	
	<section id="conditions">
		<div class="page-header">
			<h1>Bedingungen:</h1>
		</div>
		<div class="row">
			<div class="span12" id="change_conditions_alter_section">
			</div>
		</div>
		<div class="row">
			<div class="span3">Hier können sie die Bedingungen, nach denen die Ergebnissse ausgewertet werden, ändern und festlegen z.B. welche Altersgruppe oder Jahrgangsstufe welche Sportarten machen muss.
			</div>
			<div class="span9">
				<h2><small>Sie bewerten die Ergebnisse im Moment nach: </small><?php echo ($current_conditions_type == 'classes') ? ('Klassen') : ('Alter') ?></h2>
				<!-- easy changes -->
				<?php $attributes = array('id' => 'change_conditions_form'); echo form_open('preferences/changeConditions', $attributes); ?>
				
					Nach <select id="conditions_type-select" name="conditions_type-select">
						<option value="classes" <?php echo ($current_conditions_type != 'classes') ? ('selected') : ('') ?>>Klasse</option>
						<option value="years" <?php echo ($current_conditions_type != 'years') ? ('selected') : '' ?>>Alter</option>
					</select>
					<input type="submit" name="change_conditions_type_used" value="bewerten" class="btn   conditions_buttons"/>
						
					<br /><br />
					
					<div id="extended_changes" class="content">
					<h3 class="toogle">Erweiterte Änderungsmöglichkeiten:</h3>
					<?php foreach ($conditions as $gender => $conditions_gender): ?>
						<h2><small>Geschlecht: <strong><?php echo $gender; ?></strong></small></h2>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th><?php echo ($current_conditions_type != 'classes') ? ('Klasse') : ('Alter') ?></th>
								<?php
								foreach ($all_sport_groups as $sport_group) {
								echo '<th>' . $sport_group['name'] . '</th>';
								} ?>
								<th>Sieger</th>
								<th>Ehren</th>
							</tr>
						</thead>
						
							<tbody>
							<!--value muss mit name übereinstimmen, da browser ein kein option ausgewählt wurde, automatisch den "Anzeigestring" des Option nimmt-->
							
							<?php foreach ($conditions_gender as $conditions_value): ?>
								<tr>
									<td><?php echo $conditions_value['value']; ?></td>
									<?php foreach ($all_sport_groups as $sport_group_id => $sport_group): ?>
										<?php if (isset($conditions_value['sport_group_' . $sport_group_id])): ?>
											<td>
												<select class="span2" name="<?php echo $conditions_value['id'] . '_' . $sport_group_id; ?>">
													<?php foreach ($sports[$sport_group_id] as $sport): ?>
														<!-- wenn gesetzt oder nicht -->
														<?php if ($sport['id'] == $conditions_value['sport_group_' . $sport_group_id]): ?>
															<option selected="selected" name="<?php echo $sport['id']; ?>"><?php echo $sport['name']; ?></option>
														<?php else: ?>
															<option value="<?php echo $sport['id']; ?>"><?php echo $sport['name']; ?></option>
														<?php endif; ?>
													<?php endforeach; ?>
												</select>
											</td>
										<?php else: ?>
											<td></td>
										<?php endif; ?>
									<?php endforeach; ?>
								
									<td>
										<input class="span1" name="<?php echo $gender . '_' . $conditions_value['value'] . '_winner'; ?>" value="<?php echo $conditions_value['winner']; ?>" />
									</td>
									<td>
										<input class="span1" name="<?php echo $gender . '_' . $conditions_value['value'] . '_honor'; ?>" value="<?php echo $conditions_value['honor']; ?>" />
									</td>
								</tr>
							<?php endforeach; ?>
							</tbody>
								
					</table><?php endforeach; ?>
					
					<style type="text/css">
						@media only screen and (max-width: 500px), (min-device-width: 768px) and (max-device-width: 1024px)  {
							#extended_changes {
								display: none;
							}
							
							td:nth-of-type(1):before {
								content: "<?php echo ($current_conditions_type != 'classes') ? ('Klasse') : ('Alter') ?>: ";
							}
							<?php $i = 2; ?>
							<?php foreach ($all_sport_groups as $sport_group): ?>
								td:nth-of-type(<?php echo $i; ?>):before {
									content: "<?php echo $sport_group['name']; ?>: ";
								}
								<?php $i++; ?>
							<?php endforeach; ?>
							
							<?php $charter_types = array('Sieger', 'Ehren'); ?>
							<?php foreach ($charter_types as $type): ?>
								td:nth-of-type(<?php echo $i; ?>):before {
									content: "<?php echo $type; ?>: ";
								}
								<?php $i++; ?>
							<?php endforeach; ?>
						}
					</style>
					
					<?php if (isset($timekeeping)): ?>
						<?php echo $timekeeping['setting_label']; ?>:
						<select name="timekeeping" class="span2">
							<?php foreach ($timekeeping_options as $timekeeping_option_key => $timekeeping_option): ?>
								 <?php if ($timekeeping_option_key == $timekeeping['setting_value']): ?>
								 	<option selected="selected" value="<?php echo $timekeeping_option_key; ?>"><?php echo $timekeeping_option; ?></option>
								 <?php else: ?>
								 	<option value="<?php echo $timekeeping_option_key; ?>"><?php echo $timekeeping_option; ?></option>
								 <?php endif; ?>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
					
					<fieldset>
						<div class="form-actions conditions_buttons">
							<input type="submit" name="extended_conditions_changes" class="btn  " value="Ändern der Bedingungen" />
							<input type="submit" name="reset_conditions" value="Zurücksetzten" class="btn pull-right" id="reset_conditions_type" />
						</div>
					</fieldset>
				</div>
			</form>
		</div>
	</section>
	
	<section id="printing">
		<div class="page-header">
			<h1>Druckeinstellungen:</h1>
		</div>
		<div class="row">
			<div class="span12" id="printing_alert_section">
				<?php if (isset($printer_set_up) AND $printer_set_up !== TRUE): ?>
					<div class="alert alert-error">
						<strong>Achtung!</strong>
						Bis jetzt wurde für dieses System noch kein Drucker eingerichtet! Bitte richten sie, vor den nächsten Schritten, einen funktionsfähigen Drucker ein.
					</div>
				<?php endif; ?>
			</div>
		</div>
	
	<div class="row" id="printing_settings">
		<div class="span3">
			Hier können sie die Pixelverschiebungen ihrem Drucker anpassen, da manche Modelle eine leichte Verschiebung der Pixel besitzen.
		</div>
		<div class="span5">
			<?php $attributes = array('class' => 'form-horizontal', 'id' => 'change_printing_settings_form'); echo form_open('preferences/changePrintingSettings', $attributes); ?>
				<fieldset>
					<div class="control-group">
						<label class="control-label">Drucker:</label>
						<div class="controls">
							<select name="printer" class="span2">
								<option value="default">Standarddrucker</option>
								<?php if (isset($printers)): ?>
									<?php foreach ($printers as $printer): ?>
										<option value="<?php echo $printer['printer_name']; ?>"><?php echo $printer['printer_name']; ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</div>
					</div>
					
					<?php foreach ($print_settings as $setting_key => $print_setting): ?>
					<div class="control-group">
						<label class="control-label"><?php echo $print_setting['setting_label']; ?>: </label>
						<div class="controls">
							<input type="text" name="<?php echo $setting_key; ?>" value="<?php echo  $print_setting['setting_value']; ?>" class="span1 required numeric" /> px
						</div>
					</div>
					<?php endforeach; ?>
					<?php if (1 == 0): ?>
					<div class="control-group">
						<label class="control-label"><?php echo $gcp['setting_label']; ?>:</label>
						<div class="controls">
							<input type="text" name="<?php echo $gcp['setting_key']; ?>" value="<?php echo $gcp['setting_value']; ?>" class="span2 required numeric" />
						</div>
					</div>
					<?php endif; ?>
					<div class="form-actions">
						<input type="submit" name="submit" value="Ändern" class="btn  " id="change_print_settings"/>
					</div>
					
				</fieldset>
			<?php echo form_close(); ?>
		</div>
	</div>
	</section>
	
	<section id="contests">
		<div class="page-header">
			<h1>Wettbewerbe:</h1>
		</div>
		<div class="row" id="contest">
			<div class="span3">
				Hier können sie einen neuen Wettbewerb erstellen, oder in einen schon vorhandenen wechseln.
			</div>
			<div class="span7">
				<?php $attributes = array('class' => 'form-vertical'); echo form_open('preferences/doContest', $attributes); ?>
					<fieldset>
						<label>Wettbewerb:  </label>
							<select name="contests-select" class="span2">
								<?php foreach ($all_contests as $contest_id => $contest): ?>
									<option value="<?php echo $contest_id; ?>" id ="<?php echo $contest_id; ?>"><?php echo$contest['contest_name']; ?></option>
								<?php endforeach; ?>
							</select> 
							
							<input type="submit" class="btn btn- " name="change_contest_used" value="wechseln" />
							<input type="submit" name="delete_contest" value="Löschen" class="btn" data-toggle="modal" data-target="#delete_alert"/>
							
								<div id="delete_alert" class="modal hide fade">
								  <div class="modal-header">
								    <a class="close" data-dismiss="modal">×</a>
								    <h3>Wettbewerb löschen?</h3>
								  </div>
								  <div class="modal-body">
								    <p>Sind sie sich sicher, dass sie diesen Wettbewerb mit sämtlichen ihren Daten löschen wollen?</p>
								  </div>
								  <div class="modal-footer">
								    <input type="submit" name="delete_contest" value="Wettbewerb löschen" class="btn btn-danger"/>
								    <a href="#" class="btn" data-dismiss="modal">Abbrechen</a>
								  </div>
								</div>
								
							
							<div class="form-actions">
								  <input type="submit" class="btn btn-primary middle" name="create_contest" value="Neuen Wettbewerb erstellen" />
							</div>
					</fieldset>
				<?php echo form_close(); ?>
			</div>
		</div>
	</section>
</div>
</section>
</section>

<script type="text/javascript">
$(document).ready(function(){  
	$("#change_printing_settings_form").submit(function(){
		var print_settings_form = $('#change_printing_settings_form');
		$.ajax({
			url: print_settings_form.attr('action') + "?ajax=true",
			type: print_settings_form.attr('method'),
			data: print_settings_form.serialize(),
			success: function(msg) {
						if (msg === 'success') {
							$('#printing_alert_section').prepend('<div class="alert alert-success fade in"><a class="close" data-dismiss="alert" href="#">&times;</a><strong>Erfolgreich</strong> geändert.');
						}
					 }
		});
		
		return false;
	});
	
	$("#change_sport_groups_form").submit(function() {
		var change_sport_groups_form = $('#change_sport_groups_form');
		$.ajax({
			url: change_sport_groups_form.attr('action') + "?ajax=true",
			type: change_sport_groups_form.attr('method'),
			data: change_sport_groups_form.serialize(),
			success: function(msg) {
						if (msg === 'success') {
							$('#change_sport_groups_alert_section').prepend('<div class="alert alert-success fade in"><a class="close" data-dismiss="alert" href="#">&times;</a><strong>Erfolgreich</strong> geändert.');
						}
					 }
		});
		
		return false;
	});
	
	/*
	var conditions_buttons = $('.conditions_buttons');
	conditions_buttons.(function() {
		var change_conditions_form = $('#change_conditions_form');
		
		$.ajax({
			url: change_conditions_form.attr('action') + "?ajax=true",
			type: change_conditions_form.attr('method'),
			data: change_conditions_form.serialize() + $(this).attr("name") + "=" + $(this).val('#'),
			success: function(msg) {
						alert(msg);
						$('#change_conditions_alert_section').prepend('<div class="alert alert-success"><strong>Erfolgreich</strong> geändert.');
					 }
		});
	});*/
});
</script>

<script src="<?php echo base_url();?>js/validation.js"></script>