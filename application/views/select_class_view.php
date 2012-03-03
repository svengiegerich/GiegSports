<?php $this->load->helper('form'); ?>

<script type="text/javascript">
	function showCompetitors(str) {
		$.ajax( {
			url: "<?php echo base_url() . 'index.php/'. $select_class['call_function']; ?>",
			type: 'POST',
			data: { class_id : str },
			success: function(msg) {
					    $('.main_content').html(msg);
					 }
		});
	}
</script>

<section>
	<?php if (isset($rows_added)): ?>
		<div class="row">
			<div class="span12">
				<div class="alert alert-success fade in">
					<a class="close" data-dismiss="alert" href="#">&times;</a>
					Danke schön. Es wurden <?php echo $rows_added; ?> Ergebniss[e] eingetragen.
				</div>
			</div>
		</div>
	<?php endif; ?>
	
	<?php if (isset($warning)): ?>
		<div class="row">
			<div class="span12">
				<?php if (isset($warning['redirected_from'])): ?>
					<div class="alert alert-warning fade in">
						<a class="close" data-dismiss="alert" href="#">&times;</a>
						Bevor diese Aktion möglich ist, müssen sie hier Ergebnisse eintragen.
					</div>
				<?php endif; ?>
	<?php endif; ?>
	
	<?php if (isset($error)): ?>
		<div class="row">
			<div class="alert alert-error fade in">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				<strong>Fehler.</strong>
				<?php if (isset($error['uncomplete'])): ?>
					Es wurden unvollständige Daten eingegeben.
				<?php else: ?>
					Es wurden fehlerhafte/unerlaubte Daten eingegeben.
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
	
	<div class="page-header">
		<div class="row">
			<div class="span9">
				<h1>
					<?php echo $select_class['string']; ?>
				</h1>
			</div>
			<?php if (isset($select_class['upload_sheet'])): ?> 
				<div class="span3">	
					<a class="btn primary pull-rigth" href="http://localhost/run/index.php/<?php echo $select_class['upload_sheet']; ?>">Ergebnisliste hochladen</a> 
				</div>
			<?php endif; ?>
		</div>
	</div>
	
	<div class="row">
		<div class="span12">
			<?php if (isset($select_class['submit_function'])) {
				echo form_open($select_class['submit_function']);
			} ?>
			<div class="span2">
				<h2>für die Klasse:</h2>
			</div>
			<div class="span6">
				<select id="class-select" class="span3" name="class-select" onchange="showCompetitors(this.value)">
					<option value="please_select">-- Bitte Auswählen --</option>
					<?php foreach ($classes as $class): ?>
						<option value="<?php echo $class->class_id; ?>" id="<?php echo $class->class_id; ?>"><?php echo $class->class_name; ?></option>
					<?php endforeach; ?>
				</select>
				</div>
			</div>
			<noscript>
				<input type="submit" name="submit" value="bearbeitn" />
			</noscript>
		</div>
		
		<?php if (!isset($selected_class_id)) {
			$selected_class_id = 'please_select';
		} ?>

		<script>
			var class_select = document.getElementById('class-select');
			selectItemByValue(class_select, <?php echo $selected_class_id; ?>);
		
			function selectItemByValue(elmnt, value){
		    	for(var i=0; i < elmnt.options.length; i++) {
	    	  		if (elmnt.options[i].value == value) {
	    	   			elmnt.selectedIndex = i;
	    	   		}
	    	   	}
	    	}
		</script>
		
		<br /><br />