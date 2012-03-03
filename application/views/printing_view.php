<?php $this->load->helper('form');
       $this->load->helper('url'); ?>

<section>
	<div class="row">
		<div class="span12">
			<?php if (isset($printer_set_up) AND $printer_set_up !== TRUE): ?>
				<div class="alert alert-error">
					<strong>Achtung!</strong>
					Bis jetzt wurde für dieses System noch kein Drucker eingerichtet! Bitte richten sie, vor den nächsten Schritten, einen funktionsfähigen Drucker ein.
				</div>
			<?php endif; ?>
			<?php if (isset($error['uncomplete']) && $error['uncomplete']): ?>
				<div class="alert alert-error fade in" data-alert="alert">
					<a class="close" data-dismiss="alert" href="#">&times;</a>
					<strong>Fehler!</strong>
					Bitte wählen sie, bevor sie drucken oder herunterladen, zuerst eine Klasse aus.
				</div>
			<?php endif; ?>
			
			<?php /* Input-Tables */ ?>
			<?php if (isset($input_tables['printing']['status']['completed']) && $input_tables['printing']['status']['completed']): ?>
				<div class="alert alert-success fade in" data-alert="alert">
					<a class="close" data-dismiss="alert" href="#">&times;</a>
					Der Druckauftrag wurde erfolgreich abgeschickt.
				</div>
			<?php endif; ?>
			
		</div>
	</div>
	

	<div class="page-header">
		<h1>Drucken:</h1>
	</div>

	<?php if (isset($class_selection)): ?>
		<div class="row">
			<div class="span7">
				<div class="alert alert-block alert-info">
					<h2>Das gerade vorbereitete jetzt auch:</h2>
					<?php $attributes = array('style' => 'margin: 10px 0px 0px 0px'); echo form_open($print_function, $attributes); ?>
						Für die Klasse:
						<select name="class_selection" class="span1" style="margin-left: 10px">
							<option value="<?php echo $class_selection['class_id']; ?>"><?php echo $class_selection['class_name']; ?></option>
						</select>
						<!--<input type="text" class="class_selection span2" name="class_selection" value="<?php echo $class_selection; ?>" />-->
						<input class="btn btn-primary printing" type="submit" name="print" value="Drucken" />
						<input class="btn btn-primary" type="submit" name="download" value="Herunterladen" />
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="row">
		<div class="span6">
			<h2>Vorbereitete Vorlagen jetzt drucken:</h2>
			<br />
			<?php $attributes = array('class' => '',); echo form_open('printing/do_input_table', $attributes); ?>
				<select name="input_tables">
					<option selected="selected">Bitte Auswählen</option>
					<?php foreach ($all_input_tables as $class_name => $file_name) {
						echo '<option value="' . $file_name . '">' . $class_name . '</option>';
					} ?>
					</select>	
				<div class="btn-group">
					<input type="submit" name="print" value="Drucken" class="btn btn-primary printing" />
				 	<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
				    	<span class="caret"></span>
				  	</a>
				  	<ul class="dropdown-menu">
				   		<li><input type="submit" name="download" value="Download"/></li>
					</ul>
				</div>
			<?php echo form_close(); ?>
		</div>

		<div class="span6">
			<h2>Vorbereitete Urkunden drucken:</h2>
			<br />
			<?php echo form_open('printing/do_charters'); ?>
			<select name="class_selection">
				<option selected="selected">Bitte Auswählen</option>
				<?php foreach ($all_charters as $class_name => $file_name) {
					echo '<option value="' . $file_name . '">' . $class_name . '</option>';
				} ?>
			</select>
			
			<div class="btn-group">
				<input type="submit" name="print" value="Drucken" class="btn btn-primary printing" />
			 	<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
			    	<span class="caret"></span>
			  	</a>
			  	<ul class="dropdown-menu">
			   		<li><input type="submit" name="download" value="Download"/></li>
				</ul>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</section>

<?php if (isset($printer_set_up) AND $printer_set_up !== TRUE): ?>
	<script type="text/javascript">
		$('.printing').attr('disabled', 'disabled');
		$('.printing').addClass('disabled');
	</script>
<?php endif; ?>


<!--<div><button id="print_button_container" class="btn btn-primary">test</button></div>
<script src="http://www.google.com/cloudprint/client/cpgadget.js">
</script>
<script defer="defer">
    var gadget = new cloudprint.Gadget();
       gadget.setPrintButton(document.getElementById("print_button_container"));
    gadget.setPrintDocument("url", "Test", "http://gieglabs.net");
</script>-->