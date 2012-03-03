<section>
	<div class="page-header">
		<div class="row">
			<div class="span12">
				<h1>Wettkampfkarten</h1>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span3">
			Hier können sie die Wettkampfkarten für den späteren Wettbewerb vorbereiten und danach ausdrucken lassen oder herunterladen.
		</div>
		<div class="span9">
			<?php $this->load->helper('form'); ?>
			<?php $attributes = array('class' => 'form-horizontal'); echo form_open('input_tables/print_order', $attributes); ?>
				<select id="class-select" name="class-select" onchange="showCompetitors(this.value)">
						<option value="all" id="all">Alle</option>
					<?php foreach ($classes as $class): ?>
						<option value="<?php echo $class->class_id; ?>" id="<?php echo $class->class_id; ?>"><?php echo $class->class_name; ?></option>
					<?php endforeach; ?>
				</select>
			
				<input type="submit" name="print-button" value="Vorbereiten" data-loading-text="Einen Moment Geduld…" id ="input_tables_submit" class="btn">
			<?php echo form_close(); ?>
		</div>
	</div>
</section>

<script type="text/javascript">
$('#input_tables_submit').click(function() {
	$('#input_tables_submit').button();
	$('#input_tables_submit').button('loading');
});
</script>