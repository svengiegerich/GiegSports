<section>
	<?php if (isset($warning)): ?>
		<div class="row">
			<div class="span12">
				<?php if (isset($warning['redirected_from'])): ?>
					<div class="alert alert-warning fade in">
						<a class="close" data-dismiss="alert" href="#">&times;</a>
						Bevor diese Aktion möglich ist, müssen sie hier zuerst Teilnehmer eintragen.
					</div>
				<?php endif; ?>
	<?php endif; ?>
	
	<div class="row">
		<div class="span12">
			<?php /* Teilnehmerlisten */ ?>
			<?php if (isset($competitors_added)): ?>
				<?php if (isset($competitors_added['competitord_added']) && $competitors_added['competitors_added'] > 0): ?>
					<div class="alert alert-success fade in" data-alert="alert">
						<a class="close" data-dismiss="alert" href="#">&times;</a>
						Es wurden <?php echo $competitors_added['competitors_added']; ?> Einträge in die Teilnehmer Liste 	neu eingepflegt.
					</div>
				<?php endif; ?>
				<?php if (isset($competitors_added['points_table_added']) && $competitors_added['points_table_added'] > 0): ?>
					<div class="alert alert-success fade in" data-alert="alert">
						<a class="close" data-dismiss="alert" href="#">&times;</a>
						Es wurden <?php echo $competitors_added['points_table_added']; ?> Einträge in die Liste für die Punktzahlen des ausgewählten Jahres neu eingepflegt.
					</div>
				<?php endif; ?>
				<?php if (isset($competitors_added['competitord_class_changed']) && $competitors_added['competitors_class_changed'] > 0): ?>	
					<div class="alert alert-success fade in" data-alert="alert">
						<a class="close" data-dismiss="alert" href="#">&times;</a>		
						Es wurden <?php echo $competitors_added['competitors_class_changed']; ?> Schüler in ihre "neue" Klasse "verschoben"
					</div>
				<?php endif; ?>
				<?php if (isset($competitors_added['competitors_exitsts']) && $competitors_added['competitors_exitsts'] > 0): ?>
					<div class="alert alert-error fade in" data-alert="alert">
						<a class="close" data-dismiss="alert" href="#">&times;</a>
						<strong>Fehler!</strong>
						Es bestanden schon <?php echo $competitors_added['competitors_exitsts']; ?> Einträge.
					</div>
				<?php endif; ?>
			<?php endif; ?>
			
			<?php /* Einzelner Teilnehmer */ ?>
			<?php if (isset($competitor_added)): ?>
				<div class="alert alert-success fade in" data-alert="alert">
					<a class="close" data-dismiss="alert" href="#">&times;</a>
					<strong>Der Teilnehmer wurde erfolgreich hinzugefügt.</strong>
				</div>
			<?php endif; ?>
			<?php if (isset($competitor_changed_class)): ?>
				<div class="alert alert-success fade in" data-alert="alert">
					<a class="close" data-dismiss="alert" href="#">&times;</a>
					<strong>Die Klasse des Teilnehmers wurde erfolgreich geändert.</strong>
				</div>
			<?php endif; ?>
			<?php if (isset($competitor_removed)): ?>
				<div class="alert alert-success fade in" data-alert="alert">
					<a class="close" data-dismiss="alert" href="#">&times;</a>
					<strong>Der Teilnehmer wurde erfolgreich entfernt.</strong>
				</div>
			<?php endif; ?>
			
			<?php if (isset($error['incomplete'])): ?>
				<div class="alert alert-error fade in" data-alert="alert">
					<a class="close" data-dismiss="alert" href="#">&times;</a>
					<strong>Unvollständige Daten!</strong>
				</div>
			<?php endif; ?>
			<?php if (isset($competitor_exists)): ?>
				<div class="alert alert-error fade in" data-alert="alert">
					<a class="close" data-dismiss="alert" href="#">&times;</a>
					<strong>Den Teilnehmer gibt es schon.</strong>
				</div>
			<?php endif; ?>
		</div>
	</div>
	
	<div class="page-header">
		<h1>Teilnehmer hinzufügen:</h1>
	</div>
	
	<h2>Teilnehmerlisten:</h2>
	<div class="row">
		<div class="span3">
			Hier können sie die Teilnehmeliste hochladen. Diese bekommen sie normalerweise aus ihrem Schulverwaltungssystem. Sie ist die Grundlage für alle weiteren Aktionen.
		</div>
		<div class="span5">
			<?php if (isset($error['upload_error'])): ?>
				<div class="alert alert-error"> 
					<strong>Fehler</strong>: 
						<blockquote><?php echo $error['upload_error']['error']; ?></blockquote>
				</div>
			<?php endif; ?>
			<?php $attributes = array('class' => 'form-horizontal'); echo form_open_multipart('competitors/do_upload', $attributes); ?>
				<fieldset>
					<div class="control-group">
						<?php $options = array(
							'lastname'    => 'Nachname',
							'firstname'  => 'Vorname',
							'date_of_birth' => 'Geburtsdatum',
							'gender'   => 'Geschlecht',
							'class' => 'Klasse');
						 $i = 0; ?>
						 
						<label class="control-label">Reihenfolge: </label>
						<div class="controls selection-bottom">
							<?php foreach ($options as $option_id => $option): ?>
								<?php echo form_dropdown($i, $options, $option_id, 'class = "span3"'); $i++; ?>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Teilnehmerliste hochladen:</label>
						<div class="controls">
							<?php $upload_file = array(
							          'name'        => 'userfile',
							          'id'          => 'upload',
							        );   
							echo form_upload($upload_file); ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Ab Klassenstufe:</label>
						<div class="controls">
							<input type="text" name="from_grade" value="5" class="span1" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Bis zur Klassestufe:</label>
						<div class="controls">
							<input type="text" name="to_grade" value="12" class="span1" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Trennzeichen:</label>
						<div class="controls">
							<input type="text" name="divider" value="," class="span1" />
						</div>
					</div>
					
					<div class="form-actions">
						<input type="submit" name="upload" value="Hochladen" class="btn submit" data-loading-text="Einen Moment Geduld..."/>
					</div>
				</fieldset>
			<?php echo form_close(); ?>
		</div>
	</div>
		
	<h2>Teilnehmer bearbeiten:</h2>
	<div class="row">
		<div class="span3">
			Hier haben sie die Möglichkeit einen Teilnehmer manuell zu bearbeiten und zu verwalten.
		</div>
		<div class="span5">
			<?php $attributes = array('class' => 'form-horizontal'); echo form_open('competitors/editCompetitor', $attributes); ?>
				<fieldset>
					<div class="control-group">
						<label class="control-label">Vorname:</label>
						<div class="controls">
							<input type="text" name="competitor_first_name" value="" class="span3" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Nachname:</label>
						<div class="controls">
							<input type="text" name="competitor_last_name" value="" class="span3" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Klasse:</label>
						<div class="controls">
							<input type="text" name="competitor_class" value="" class="span2" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Geburtsjahr:</label>
						<div class="controls">
							<div class="input">
								<input type="text" name="competitor_year" value="" class="span2" />
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Geschlecht:</label>
						<div class="controls">
							<select name="competitor_gender" class="span2">
								<option value="M">M</option>
								<option value="W">W</option>
							</select>
						</div>
					</div>
					<div class="form-actions">
						<input type="submit" name="insert_single_competitor" value="Hinzufügen" class="btn primary submit"/>
						<input type="submit" name="change_competitor_class" value="Klasse ändern" class="btn primary submit"/>
						<input type="submit" name="delete_single_competitor" value="Löschen" class="btn primary submit"/>
					</div>
				</fieldset>
			<?php form_close(); ?>
		</div>
	</div>	
</section>